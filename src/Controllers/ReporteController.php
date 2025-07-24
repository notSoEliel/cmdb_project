<?php

namespace App\Controllers;

use App\Models\Inventario;
use App\Models\Asignacion;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color; // Añadir para usar colores de fondo
use PhpOffice\PhpSpreadsheet\Style\Fill; // Añadir para usar rellenos

// Ya no necesitamos las clases de Chart si vamos a quitar la gráfica
// use PhpOffice\PhpSpreadsheet\Chart\Chart;
// use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
// use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
// use PhpOffice\PhpSpreadsheet\Chart\Layout;
// use PhpOffice\PhpSpreadsheet\Chart\Legend;
// use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
// use PhpOffice\PhpSpreadsheet\Chart\Title;
// use PhpOffice\PhpSpreadsheet\Chart\Axis;

/**
 * Class ReporteController
 * Centraliza toda la lógica para la visualización y exportación de reportes.
 */
class ReporteController extends BaseController
{
    /**
     * Muestra la página principal de reportes con todas las vistas previas.
     */
    public function index()
    {
        $inventarioModel = new Inventario();
        $asignacionModel = new Asignacion();

        // --- Datos para Resumen y Gráfico ---
        $resumenData = $inventarioModel->getSummaryByCategory();
        $chartLabels = [];
        $chartDataAsignados = [];
        $chartDataDisponibles = [];
        $chartDataTotal = [];
        if (is_array($resumenData)) {
            foreach ($resumenData as &$categoria) {
                $categoria['equipos_disponibles'] = ($categoria['total_equipos'] ?? 0) - ($categoria['equipos_asignados'] ?? 0);
                $chartLabels[] = $categoria['categoria'];
                $chartDataAsignados[] = $categoria['equipos_asignados'] ?? 0;
                $chartDataDisponibles[] = $categoria['equipos_disponibles'];
                $chartDataTotal[] = $categoria['total_equipos'] ?? 0;
            }
        }

        // --- Datos para Reporte Detallado por Categoría ---
        $todosLosEquipos = (new Inventario())->findAll([
            'paginate' => false,
            'filters' => ['i.estado' => ['NOT IN', 'Donado', 'En Descarte']]
        ]);
        $equiposPorCategoria = [];
        if (is_array($todosLosEquipos)) {
            foreach ($todosLosEquipos as $equipo) {
                $categoriaNombre = $equipo['nombre_categoria'] ?? 'Sin Categoría';
                $equiposPorCategoria[$categoriaNombre][] = $equipo;
            }
        }

        // --- Renderiza la vista con todos los datos ---
        $this->render('Views/admin/reportes/index.php', [
            'pageTitle' => 'Reportes y Análisis',
            'resumenPorCategoria' => $resumenData,
            'asignacionesActivas' => $asignacionModel->findActiveAssignments(),
            'equiposPorCategoria' => $equiposPorCategoria, // Se pasan los datos para las pestañas
            'chartLabels' => json_encode($chartLabels),
            'chartDataAsignados' => json_encode($chartDataAsignados),
            'chartDataDisponibles' => json_encode($chartDataDisponibles),
            'chartDataTotal' => json_encode($chartDataTotal) // Se pasan los datos del total para el gráfico
        ]);
    }

    /**
     * Helper para añadir una hoja de carátula al inicio del Spreadsheet.
     *
     * @param Spreadsheet $spreadsheet El objeto Spreadsheet.
     * @param string $reportTitle El título del reporte.
     * @param string $reportDescription Una descripción corta del reporte.
     */
    private function addCoverSheet(Spreadsheet $spreadsheet, string $reportTitle, string $reportDescription = '')
    {
        // Crea una nueva hoja al inicio y la activa (será la primera hoja)
        $coverSheet = $spreadsheet->getActiveSheet();
        $coverSheet->setTitle('Carátula');

        // Establecer un color de fondo para toda la hoja para que se vea más profesional
        $coverSheet->getStyle('A1:Z100')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFF0F8FF'); // Un azul muy claro (AliceBlue)

        // Estilos para la carátula
        $coverSheet->getDefaultRowDimension()->setRowHeight(25); // Altura por defecto para filas
        $coverSheet->getDefaultColumnDimension()->setWidth(20); // Ancho por defecto para columnas

        // Centrar todo el contenido vertical y horizontalmente en la carátula
        $coverSheet->getStyle('A1:Z100')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $coverSheet->getStyle('A1:Z100')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        // Título principal
        $coverSheet->mergeCells('A4:F4'); // Ajustar celdas para título
        $coverSheet->setCellValue('A4', $reportTitle);
        $coverSheet->getStyle('A4')->getFont()->setBold(true)->setSize(26)->setColor(new Color(Color::COLOR_DARKBLUE)); // Título más grande y en azul oscuro

        // Descripción del reporte
        if (!empty($reportDescription)) {
            $coverSheet->mergeCells('A6:F6'); // Dejar un espacio
            $coverSheet->setCellValue('A6', $reportDescription);
            $coverSheet->getStyle('A6')->getFont()->setSize(14)->setColor(new Color(Color::COLOR_BLUE)); // Descripción en azul
        }

        // Fecha de generación
        $coverSheet->mergeCells('A9:F9'); // Dejar más espacio
        $coverSheet->setCellValue('A9', 'Generado el: ' . date('d/m/Y H:i:s'));
        $coverSheet->getStyle('A9')->getFont()->setSize(12)->setItalic(true);

        // Información adicional (ej. nombre del sistema)
        $coverSheet->mergeCells('A11:F11');
        $coverSheet->setCellValue('A11', 'Sistema de Gestión CMDB');
        $coverSheet->getStyle('A11')->getFont()->setSize(10)->setColor(new Color('FF808080'));

        // Ajustar algunas alturas de fila para mejor espaciado visual
        $coverSheet->getRowDimension(5)->setRowHeight(40); // Espacio después del título
        $coverSheet->getRowDimension(7)->setRowHeight(30); // Espacio después de la descripción
        $coverSheet->getRowDimension(10)->setRowHeight(20); // Espacio antes del pie
    }

    /**
     * Exporta el Resumen de Inventario a Excel.
     */
    public function exportarResumen()
    {
        $resumen = (new Inventario())->getSummaryByCategory();
        $spreadsheet = new Spreadsheet();

        // AÑADIR CARÁTULA
        $this->addCoverSheet($spreadsheet, 'Resumen de Inventario por Categoría', 'Vista general del estado de los equipos clasificados por tipo.');

        // La hoja de datos será la segunda hoja (índice 1)
        $sheet = $spreadsheet->createSheet(); // Esto crea una nueva hoja en el índice 1
        $spreadsheet->setActiveSheetIndex(1); // Activamos la hoja de datos
        $sheet->setTitle('Resumen por Categoría');

        // Estilo "bonito"
        $sheet->mergeCells('A1:D1');
        $sheet->setCellValue('A1', 'Resumen de Inventario por Categoría');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:D3')->getFont()->setBold(true);
        $sheet->getStyle('A1:D' . (count($resumen) + 3))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Centrar datos

        // Encabezados
        $sheet->setCellValue('A3', 'Categoría');
        $sheet->setCellValue('B3', 'Total de Equipos');
        $sheet->setCellValue('C3', 'Asignados');
        $sheet->setCellValue('D3', 'Disponibles');

        // Datos
        $rowNumber = 4;
        foreach ($resumen as $item) {
            $sheet->setCellValue('A' . $rowNumber, $item['categoria']);
            $sheet->setCellValue('B' . $rowNumber, $item['total_equipos']);
            $sheet->setCellValue('C' . $rowNumber, $item['equipos_asignados']);
            $sheet->setCellValue('D' . $rowNumber, $item['total_equipos'] - $item['equipos_asignados']);
            $rowNumber++;
        }

        // Ajustar el ancho de las columnas automáticamente
        foreach (range('A', 'D') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);

        // Descarga
        $writer = new Xlsx($spreadsheet);
        // NO necesitamos setIncludeCharts(true) si no hay gráficos

        $spreadsheet->setActiveSheetIndex(0);

        $fileName = 'Resumen_Inventario_' . date('Y-m-d') . '.xlsx'; // Nombre con fecha

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        $writer->save('php://output');
        exit;
    }

    /**
     * Exporta un listado de todas las asignaciones activas a un archivo Excel.
     */
    public function exportarAsignaciones()
    {
        // 1. Obtener los datos del modelo
        $asignaciones = (new Asignacion())->findActiveAssignments();

        // 2. Crear el objeto Spreadsheet y configurar la hoja
        $spreadsheet = new Spreadsheet();

        // AÑADIR CARÁTULA
        $this->addCoverSheet($spreadsheet, 'Reporte de Asignaciones Activas', 'Detalle de todos los equipos actualmente asignados a colaboradores.');

        // La hoja de datos será la segunda hoja (índice 1)
        $sheet = $spreadsheet->createSheet(); // Crea una nueva hoja en el índice 1
        $spreadsheet->setActiveSheetIndex(1); // Activamos la hoja de datos
        $sheet->setTitle('Asignaciones Activas');

        // 3. Añadir el título "bonito" y los encabezados
        $sheet->mergeCells('A1:E1');
        $sheet->setCellValue('A1', 'Reporte de Asignaciones Activas');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A3', 'Equipo');
        $sheet->setCellValue('B3', 'Serie');
        $sheet->setCellValue('C3', 'Colaborador Asignado');
        $sheet->setCellValue('D3', 'Departamento');
        $sheet->setCellValue('E3', 'Fecha de Asignación');
        $sheet->getStyle('A3:E3')->getFont()->setBold(true);
        $sheet->getStyle('A1:E' . (count($asignaciones) + 3))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Centrar datos


        // 4. Llenar el archivo con los datos
        $rowNumber = 4;
        foreach ($asignaciones as $item) {
            $sheet->setCellValue('A' . $rowNumber, $item['nombre_equipo']);
            $sheet->setCellValue('B' . $rowNumber, $item['serie']);
            $sheet->setCellValue('C' . $rowNumber, $item['nombre_colaborador']);
            $sheet->setCellValue('D' . $rowNumber, $item['departamento']);
            $sheet->setCellValue('E' . $rowNumber, $item['fecha_asignacion']);
            $rowNumber++;
        }

        // Ajustar el ancho de las columnas automáticamente
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $spreadsheet->setActiveSheetIndex(0);

        // 5. Enviar el archivo al navegador
        $writer = new Xlsx($spreadsheet);
        $fileName = 'Reporte_Asignaciones_' . date('Y-m-d') . '.xlsx'; // Nombre con fecha

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        $writer->save('php://output');
        exit;
    }

    /**
     * Exporta el listado detallado de inventario a un Excel con una hoja por categoría.
     */
    public function exportarDetalladoPorCategoria()
    {
        $todosLosEquipos = (new Inventario())->findAll([
            'paginate' => false,
            'filters' => ['i.estado' => ['NOT IN', 'Donado', 'En Descarte']]
        ]);
        $equiposPorCategoria = [];
        foreach ($todosLosEquipos as $equipo) {
            $categoriaNombre = $equipo['nombre_categoria'] ?? 'Sin Categoría';
            $equiposPorCategoria[$categoriaNombre][] = $equipo;
        }

        $spreadsheet = new Spreadsheet();

        // AÑADIR CARÁTULA
        $this->addCoverSheet($spreadsheet, 'Reporte Detallado de Inventario por Categoría', 'Inventario completo, organizado en hojas individuales por cada categoría de equipo.');

        $sheetIndex = 1; // Empezamos en el índice 1 porque la hoja 0 es la carátula

        foreach ($equiposPorCategoria as $categoria => $equipos) {
            if ($sheetIndex > 0) { // Siempre crear una nueva hoja si no es la hoja 0 (la carátula)
                $spreadsheet->createSheet();
            }
            $sheet = $spreadsheet->setActiveSheetIndex($sheetIndex);
            $sheet->setTitle(substr($categoria, 0, 31)); // El nombre de la hoja tiene un límite de 31 caracteres

            // Título y encabezados "bonitos"
            $sheet->mergeCells('A1:F1');
            $sheet->setCellValue('A1', 'Reporte Detallado - Categoría: ' . $categoria);
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A3:F3')->getFont()->setBold(true);
            $sheet->getStyle('A1:F' . (count($equipos) + 3))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Centrar datos


            $sheet->setCellValue('A3', 'ID');
            $sheet->setCellValue('B3', 'Nombre Equipo');
            $sheet->setCellValue('C3', 'Marca');
            $sheet->setCellValue('D3', 'Modelo');
            $sheet->setCellValue('E3', 'Serie');
            $sheet->setCellValue('F3', 'Estado');

            // Llenado de datos
            $rowNumber = 4;
            foreach ($equipos as $equipo) {
                $sheet->setCellValue('A' . $rowNumber, $equipo['id']);
                $sheet->setCellValue('B' . $rowNumber, $equipo['nombre_equipo']);
                $sheet->setCellValue('C' . $rowNumber, $equipo['marca']);
                $sheet->setCellValue('D' . $rowNumber, $equipo['modelo']);
                $sheet->setCellValue('E' . $rowNumber, $equipo['serie']);
                $sheet->setCellValue('F' . $rowNumber, $equipo['estado']);
                $rowNumber++;
            }
            foreach (range('A', 'F') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);
            $sheetIndex++;
        }

        $spreadsheet->setActiveSheetIndex(0);

        // Descarga
        $writer = new Xlsx($spreadsheet);
        // Ya no necesitamos setIncludeCharts(true)
        $fileName = 'Reporte_Detallado_Por_Categoria_' . date('Y-m-d') . '.xlsx'; // Nombre con fecha

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        $writer->save('php://output');
        exit;
    }
}
