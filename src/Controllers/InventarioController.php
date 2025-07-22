<?php

namespace App\Controllers;

// Se añaden todos los modelos que este controlador necesita
use App\Models\Inventario;
use App\Models\Categoria;
use App\Models\Colaborador;
use App\Models\Asignacion;
use App\Models\InventarioImagen;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Class InventarioController
 *
 * Gestiona todas las acciones para el módulo de inventario.
 * Es el controlador más complejo, ya que maneja la lógica de
 * asignaciones, imágenes, filtros y el CRUD básico.
 */
class InventarioController extends BaseController
{
    /**
     * Muestra la página principal del inventario con la tabla dinámica.
     * Es responsable de recopilar todos los parámetros de la URL (para filtros,
     * búsqueda, paginación y orden) y de preparar una configuración
     * detallada para el componente de vista de tabla reutilizable.
     */
    public function index()
    {
        // --- 1. Recopilación de Parámetros de la URL ---
        $page = (int)($_GET['page'] ?? 1);
        $perPage = (int)($_GET['perPage'] ?? 10);
        $search = $_GET['search'] ?? '';
        $sort = $_GET['sort'] ?? 'i.nombre_equipo';
        $order = $_GET['order'] ?? 'asc';

        // Se inicializa el array de filtros.
        $filters = [];
        // Se añade un filtro si se ha seleccionado una categoría.
        if (!empty($_GET['categoria_id'])) {
            $filters['i.categoria_id'] = $_GET['categoria_id'];
        }
        // Se añade un filtro si se ha seleccionado un colaborador.
        if (!empty($_GET['colaborador_id'])) {
            // ...añadimos el filtro por su ID...
            $filters['a.colaborador_id'] = $_GET['colaborador_id'];
            // ...y TAMBIÉN añadimos un filtro para excluir los estados no relevantes.
            // La estructura ['NOT IN', 'val1', 'val2'] es entendida por el nuevo buildWhereClause.
            $filters['i.estado'] = ['NOT IN', 'En Descarte', 'Donado'];
        }

        // --- 2. Preparación de Modelos y Opciones ---
        $inventarioModel = new Inventario();
        $categoriaModel = new Categoria();
        $colaboradorModel = new Colaborador();

        // Array de opciones consolidadas para pasar a los métodos del modelo.
        $options = [
            'page' => $page,
            'perPage' => $perPage,
            'sort' => $sort,
            'order' => $order,
            'search' => $search,
            'filters' => $filters,
            // Se define el SELECT para incluir el nombre de la categoría y del colaborador.
            'selectClause' => 'i.*, c.nombre as nombre_categoria, CONCAT(co.nombre, " ", co.apellido) as nombre_colaborador, a.id as asignacion_id'
        ];

        // --- 3. Obtención de Datos y Cálculo de Paginación ---
        $inventarios = $inventarioModel->findAll($options);
        $totalRecords = $inventarioModel->countFiltered($options);
        $totalPages = ceil($totalRecords / $perPage);

        // --- 4. Configuración para la Tabla Reutilizable ---
        $tableConfig = [
            'columns' => [
                // 'field' se usa para mostrar el dato; 'sort_by' para ordenar en la BD.
                ['header' => '', 'field' => 'thumbnail_path', 'type' => 'image'],
                ['header' => 'ID', 'field' => 'id', 'sort_by' => 'i.id'],
                ['header' => 'Equipo', 'field' => 'nombre_equipo', 'sort_by' => 'i.nombre_equipo'],
                ['header' => 'Categoría', 'field' => 'nombre_categoria', 'sort_by' => 'nombre_categoria'],
                ['header' => 'Marca', 'field' => 'marca', 'sort_by' => 'i.marca'],
                ['header' => 'Modelo', 'field' => 'modelo'], // No ordenable en este ejemplo
                ['header' => 'Serie', 'field' => 'serie'],   // No ordenable
                ['header' => 'Costo', 'field' => 'costo', 'sort_by' => 'i.costo'],
                ['header' => 'Fecha Ingreso', 'field' => 'fecha_ingreso', 'sort_by' => 'i.fecha_ingreso'],
                ['header' => 'Fin de Vida Útil', 'field' => 'fecha_fin_vida', 'sort_by' => 'fecha_fin_vida'],
                ['header' => 'Asignado a', 'field' => 'nombre_colaborador', 'sort_by' => 'nombre_colaborador'],
                ['header' => 'Estado', 'field' => 'estado', 'sort_by' => 'i.estado'],
            ],
            'data' => $inventarios,
            'pagination' => [
                'currentPage' => $page,
                'perPage' => $perPage,
                'totalPages' => $totalPages,
                'totalRecords' => $totalRecords,
                'search' => $search,
                'sort' => $sort,
                'order' => $order,
                'filters' => $filters
            ],
            'actions' => [
                'route' => 'inventario', // Ruta base para las acciones
                'edit_action' => 'index',      // La acción para editar es 'index'
                'delete_action' => 'destroy',
                'unassign_action' => 'unassign',
                'image_action' => 'showImages'
            ],
            // Configuración para el nuevo dropdown de filtro
            'dropdown_filters' => [
                'categoria' => [
                    'label' => 'Categoría',
                    'name' => 'categoria_id',
                    'options' => (new Categoria())->findAll() // Obtenemos todas las categorías
                ],
                'colaborador' => [
                    'label' => 'Colaborador',
                    'name' => 'colaborador_id',
                    'options' => array_map(fn($c) => ['id' => $c['id'], 'nombre' => $c['nombre'] . ' ' . $c['apellido']], (new Colaborador())->findAll())
                ]
            ]
        ];

        // --- 5. Renderizado de la Vista ---
        $this->render('Views/inventario/index.php', [
            'pageTitle' => 'Gestionar Inventario',
            'formId' => 'form-inventario',
            'categorias' => $categoriaModel->findAll(), // Para el formulario de agregar/editar
            'tableConfig' => $tableConfig // Pasamos toda la configuración a la vista
        ]);
    }

    /**
     * Muestra el formulario para asignar un equipo a un colaborador.
     */
    public function showAssignForm()
    {
        $inventario_id = (int)($_GET['id'] ?? 0);
        $equipo = (new Inventario())->findById($inventario_id);

        // Si no se encuentra el equipo, muestra la página 404
        if (!$equipo) {
            http_response_code(404);
            require_once '../src/Views/error-404.php';
            exit;
        }

        $this->render('Views/inventario/asignar.php', [
            'equipo' => $equipo,
            'colaboradores' => (new Colaborador())->findAll(),
            'pageTitle' => 'Asignar Equipo'
        ]);
    }

    /**
     * Procesa el envío del formulario de asignación.
     */
    public function assign()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $inventario_id = (int)$_POST['inventario_id'];
            $colaborador_id = (int)$_POST['colaborador_id'];
            try {
                (new Asignacion())->create($inventario_id, $colaborador_id);
                $_SESSION['mensaje_sa2'] = ['title' => '¡Éxito!', 'text' => 'Equipo asignado.', 'icon' => 'success'];
            } catch (\Exception $e) {
                $_SESSION['mensaje_sa2'] = ['title' => '¡Error!', 'text' => 'No se pudo asignar el equipo: ' . $e->getMessage(), 'icon' => 'error'];
            }
            header('Location: ' . BASE_URL . 'index.php?route=inventario');
            exit;
        }
    }

    /**
     * Procesa la des-asignación de un equipo.
     */
    public function unassign()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $asignacion_id = (int)$_POST['asignacion_id'];
            $inventario_id = (int)$_POST['inventario_id'];
            try {
                (new Asignacion())->unassign($asignacion_id, $inventario_id);
                $_SESSION['mensaje_sa2'] = ['title' => '¡Éxito!', 'text' => 'Equipo des-asignado.', 'icon' => 'success'];
            } catch (\Exception $e) {
                $_SESSION['mensaje_sa2'] = ['title' => '¡Error!', 'text' => 'No se pudo des-asignar el equipo.', 'icon' => 'error'];
            }
            header('Location: ' . BASE_URL . 'index.php?route=inventario');
            exit;
        }
    }

    /**
     * Procesa la creación de un nuevo equipo.
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $inventarioModel = new Inventario();
                $inventarioModel->save($_POST);
                $_SESSION['mensaje_sa2'] = ['title' => '¡Éxito!', 'text' => 'Equipo guardado.', 'icon' => 'success'];
            } catch (\Throwable $e) {
                handleException($e); // <-- LLAMAMOS A NUESTRO HELPER
            }
            header('Location: ' . BASE_URL . 'index.php?route=inventario');
            exit;
        }
    }

    /**
     * Procesa la actualización de un equipo existente.
     */
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $inventarioModel = new Inventario();
                $inventarioModel->save($_POST);
                $_SESSION['mensaje_sa2'] = ['title' => '¡Éxito!', 'text' => 'Equipo actualizado.', 'icon' => 'success'];
            } catch (\Throwable $e) {
                handleException($e); // <-- LLAMAMOS A NUESTRO HELPER
            }
            header('Location: ' . BASE_URL . 'index.php?route=inventario');
            exit;
        }
    }

    /**
     * Procesa la eliminación de un equipo.
     */
    public function destroy()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $inventarioModel = new Inventario();
            $inventarioModel->delete($_POST['id']);
            $_SESSION['mensaje_sa2'] = ['title' => '¡Eliminado!', 'text' => 'Equipo eliminado.', 'icon' => 'success'];
            header('Location: ' . BASE_URL . 'index.php?route=inventario');
            exit;
        }
    }

    /**
     * Muestra la página de gestión de imágenes para un equipo.
     */
    public function showImages()
    {
        $inventario_id = (int)($_GET['id'] ?? 0);
        $inventarioModel = new Inventario();
        $imagenModel = new InventarioImagen();

        $this->render('Views/inventario/imagenes.php', [
            'equipo' => $inventarioModel->findById($inventario_id),
            'imagenes' => $imagenModel->findByInventarioId($inventario_id),
            'pageTitle' => 'Gestionar Imágenes'
        ]);
    }

    /**
     * Procesa la subida de una nueva imagen, validando su tipo.
     */
    public function uploadImage()
    {
        $inventario_id = (int)($_POST['inventario_id'] ?? 0);
        if (!$inventario_id) {
            header('Location: ' . BASE_URL . 'index.php?route=inventario');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['imagen'])) {
            $imagen = $_FILES['imagen'];
            if ($imagen['error'] === UPLOAD_ERR_OK) {
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
                $file_extension = strtolower(pathinfo($imagen['name'], PATHINFO_EXTENSION));

                if (in_array($file_extension, $allowed_extensions)) {
                    $uploadDir = '../public/uploads/inventario/';
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                    $fileName = uniqid() . '-' . basename($imagen['name']);
                    $targetPath = $uploadDir . $fileName;

                    if (move_uploaded_file($imagen['tmp_name'], $targetPath)) {
                        (new InventarioImagen())->save($inventario_id, $fileName);
                        $_SESSION['mensaje_sa2'] = ['title' => '¡Éxito!', 'text' => 'Imagen subida.', 'icon' => 'success'];
                    } else {
                        $_SESSION['mensaje_sa2'] = ['title' => 'Error', 'text' => 'No se pudo mover el archivo. Revisa permisos.', 'icon' => 'error'];
                    }
                } else {
                    $_SESSION['mensaje_sa2'] = ['title' => 'Archivo no Válido', 'text' => 'Solo se permiten imágenes (jpg, png, webp, gif).', 'icon' => 'error'];
                }
            } else {
                $_SESSION['mensaje_sa2'] = ['title' => 'Error', 'text' => 'Hubo un problema con la subida.', 'icon' => 'error'];
            }
        }

        header('Location: ' . BASE_URL . 'index.php?route=inventario&action=showImages&id=' . $inventario_id);
        exit;
    }

    /**
     * Elimina una imagen específica.
     */
    public function destroyImage()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $imagen_id = (int)($_POST['imagen_id'] ?? 0);
            $inventario_id = (int)($_POST['inventario_id'] ?? 0);
            $imagenModel = new InventarioImagen();
            $imagen = $imagenModel->findById($imagen_id);

            if ($imagen) {
                $filePath = '../public/uploads/inventario/' . $imagen['ruta_imagen'];
                if (file_exists($filePath)) unlink($filePath);
                $imagenModel->delete($imagen_id);
                $_SESSION['mensaje_sa2'] = ['title' => '¡Éxito!', 'text' => 'Imagen eliminada.', 'icon' => 'success'];
            }
            header('Location: ' . BASE_URL . 'index.php?route=inventario&action=showImages&id=' . $inventario_id);
            exit;
        }
    }

    /**
     * Establece una imagen como la principal (thumbnail).
     */
    public function setThumbnail()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $imagen_id = (int)($_POST['imagen_id'] ?? 0);
            $inventario_id = (int)($_POST['inventario_id'] ?? 0);
            (new InventarioImagen())->setThumbnail($inventario_id, $imagen_id);
            $_SESSION['mensaje_sa2'] = ['title' => '¡Éxito!', 'text' => 'Thumbnail actualizado.', 'icon' => 'success'];
            header('Location: ' . BASE_URL . 'index.php?route=inventario&action=showImages&id=' . $inventario_id);
            exit;
        }
    }

    /**
     * Exporta los datos del inventario a un archivo Excel.
     */
    public function exportToExcel()
    {
        // 1. Obtener TODOS los datos, sin paginación
        $inventarioModel = new Inventario();
        $options = $_GET; // Usamos los filtros y búsqueda actuales de la URL
        $options['paginate'] = false; // Le decimos al modelo que no pagine
        $inventarios = $inventarioModel->findAll($options);

        // 2. Crear el objeto Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Inventario');

        // 3. Añadir los encabezados
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Equipo');
        $sheet->setCellValue('C1', 'Categoría');
        $sheet->setCellValue('D1', 'Marca');
        $sheet->setCellValue('E1', 'Modelo');
        $sheet->setCellValue('F1', 'Serie');
        $sheet->setCellValue('G1', 'Estado');
        $sheet->setCellValue('H1', 'Asignado a');
        $sheet->setCellValue('I1', 'Fecha de Ingreso');

        // 4. Llenar el archivo con los datos
        $rowNumber = 2;
        foreach ($inventarios as $item) {
            $sheet->setCellValue('A' . $rowNumber, $item['id']);
            $sheet->setCellValue('B' . $rowNumber, $item['nombre_equipo']);
            $sheet->setCellValue('C' . $rowNumber, $item['nombre_categoria']);
            $sheet->setCellValue('D' . $rowNumber, $item['marca']);
            $sheet->setCellValue('E' . $rowNumber, $item['modelo']);
            $sheet->setCellValue('F' . $rowNumber, $item['serie']);
            $sheet->setCellValue('G' . $rowNumber, $item['estado']);
            $sheet->setCellValue('H' . $rowNumber, $item['nombre_colaborador']);
            $sheet->setCellValue('I' . $rowNumber, $item['fecha_ingreso']);
            $rowNumber++;
        }

        // 5. Enviar el archivo al navegador para su descarga
        $writer = new Xlsx($spreadsheet);
        $fileName = 'Reporte_Inventario_' . date('Y-m-d') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        $writer->save('php://output');
        exit;
    }
}
