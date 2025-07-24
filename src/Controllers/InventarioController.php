<?php

namespace App\Controllers;

// Se añaden todos los modelos que este controlador necesita
use App\Models\Inventario;
use App\Models\Categoria;
use App\Models\Colaborador;
use App\Models\Asignacion;
use App\Models\InventarioImagen;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

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
        // Filtro permanente para excluir equipos no activos
        // Le pasamos el filtro como un array: ['OPERADOR', 'valor1', 'valor2', ...]
        $filters['i.estado'] = ['NOT IN', 'Donado', 'En Descarte'];
        // Se añade el filtro de categoría si se aplica desde la URL.
        if (!empty($_GET['categoria_id'])) {
            $filters['i.categoria_id'] = $_GET['categoria_id'];
        }
        // Se añade el filtro de colaborador si se aplica desde la URL.
        if (!empty($_GET['colaborador_id'])) {
            $filters['a.colaborador_id'] = $_GET['colaborador_id'];
            // $filters['i.estado'] = ['NOT IN', 'En Descarte', 'Donado'];
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
            'selectClause' => 'i.*, c.nombre as nombre_categoria, CONCAT(co.nombre, " ", co.apellido) as nombre_colaborador, a.id as asignacion_id'
        ];

        // --- 3. Obtención de Datos y Cálculo de Paginación ---
        $inventarios = $inventarioModel->findAll($options);
        $totalRecords = $inventarioModel->countFiltered($options);
        $totalPages = ceil($totalRecords / $perPage);

        // --- 4. Configuración para la Tabla Reutilizable ---
        $tableConfig = [
            'columns' => [
                ['header' => '', 'field' => 'thumbnail_path', 'type' => 'image'],
                ['header' => 'ID', 'field' => 'id', 'sort_by' => 'i.id'],
                ['header' => 'Equipo', 'field' => 'nombre_equipo', 'sort_by' => 'i.nombre_equipo'],
                ['header' => 'Categoría', 'field' => 'nombre_categoria', 'sort_by' => 'nombre_categoria'],
                ['header' => 'Marca', 'field' => 'marca', 'sort_by' => 'i.marca'],
                ['header' => 'Modelo', 'field' => 'modelo'],
                ['header' => 'Serie', 'field' => 'serie'],
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
                'route' => 'inventario',
                'edit_action' => 'showAddForm', // CORRECTO: Apunta al showAddForm
                'delete_action' => 'destroy',
                'unassign_action' => 'unassign',
                'image_action' => 'showImages'
            ],
            'dropdown_filters' => [
                'categoria' => [
                    'label' => 'Categoría',
                    'name' => 'categoria_id',
                    'options' => (new Categoria())->findAll()
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
            'formIds' => ['form-inventario'],
            'tableConfig' => $tableConfig
        ]);
    }

    /**
     * Muestra el formulario para añadir un nuevo equipo (individual o por lote)
     * o para editar un equipo existente.
     * También gestiona la sugerencia automática del número de serie para lotes.
     */
    public function showAddForm()
    {
        $inventarioModel = new Inventario();
        $categoriaModel = new Categoria();
        $categorias = $categoriaModel->findAll();
        $equipoActual = null;
        $isEditing = false;
        $suggestedNextSerialNumber = 1; // Valor por defecto

        // Determinar si estamos editando un equipo
        if (isset($_GET['editar_id']) && !empty($_GET['editar_id'])) {
            $equipoActual = $inventarioModel->findById((int)$_GET['editar_id']);
            if (!$equipoActual) {
                http_response_code(404);
                require_once '../src/Views/error-404.php';
                exit;
            }
            $isEditing = true;

            // Lógica mejorada para precargar prefijo y número final al editar
            // Si la serie existente tiene un patrón de prefijo-número
            if (preg_match('/^(.*)-(\d+)$/', $equipoActual['serie'], $matches)) {
                $equipoActual['prefijo_serie'] = $matches[1] . '-'; // Incluir el guion en el prefijo
                $equipoActual['numero_final_serie'] = $matches[2];
            } else {
                // Si no tiene prefijo o no sigue el patrón esperado, asume que toda la serie es el número final
                $equipoActual['prefijo_serie'] = ''; // Deja el prefijo vacío
                $equipoActual['numero_final_serie'] = $equipoActual['serie']; // Toda la serie es el número
            }
        }

        // Determinar si se debe mostrar el formulario por lote (ya sea por URL o después de la sugerencia)
        $showBatchForm = (isset($_GET['form_action']) && $_GET['form_action'] === 'batch');

        // Lógica para sugerir el siguiente número de serie solo si estamos en el formulario por lote
        if ($showBatchForm && !$isEditing) {
            $prefijo_serie = $_GET['prefijo_serie_sug'] ?? ''; // Obtener prefijo si viene de la URL (para re-sugerir)
            // ID de la categoría "Clave de Software" (AJUSTA ESTE VALOR AL ID REAL DE TU BD)
            $softwareKeyCategoryId = 3; // Ejemplo: Asume que el ID para 'Clave de Software' es 3
            $excludeCategoryIds = [$softwareKeyCategoryId]; // Categorías a excluir de la sugerencia

            $lastNum = $inventarioModel->findLastSerialNumber($prefijo_serie, $excludeCategoryIds);

            // Si el prefijo no se ha usado antes, sugerir 1 (o 0 si prefieres)
            if ($prefijo_serie !== '' && $lastNum === 0) {
                $suggestedNextSerialNumber = 1; // O 0, según tu preferencia
            } else {
                $suggestedNextSerialNumber = $lastNum + 1;
            }
        }

        $this->render('Views/inventario/add_edit.php', [
            'pageTitle' => $isEditing ? 'Editar Equipo' : ($showBatchForm ? 'Añadir Equipos por Lote' : 'Añadir Nuevo Equipo'),
            'formIds' => ['form-inventario', 'form-inventario-lote'], // Ambos IDs para la validación
            'categorias' => $categorias,
            'equipoActual' => $equipoActual, // Será null si no estamos editando
            'isEditing' => $isEditing,
            'showBatchForm' => $showBatchForm, // Este ya viene del GET
            'suggestedNextSerialNumber' => $suggestedNextSerialNumber, // Pasar la sugerencia
        ]);
    }

    /**
     * Endpoint para validar la unicidad del número de serie vía AJAX.
     * Utilizado por jQuery Validate con la regla 'remote'.
     *
     * @return void Imprime 'true' o 'false' en JSON.
     */
    public function checkSerialUniqueness()
    {
        header('Content-Type: application/json');
        $serie = trim($_GET['serie'] ?? ''); // El nombre del campo que jQuery Validate envía
        $id = (int)($_GET['id'] ?? null); // El ID del equipo si estamos editando

        $inventarioModel = new Inventario();

        if (empty($serie)) {
            echo json_encode(false); // No permitir series vacías
            exit;
        }

        // Aquí solo comprobamos unicidad. El formato ya debería validarlo jQuery en cliente
        // o las validaciones previas en store/update si se omite jQuery
        $isUnique = !$inventarioModel->exists('serie', $serie, $id);

        echo json_encode($isUnique);
        exit;
    }


    /**
     * Procesa la creación de un nuevo equipo individual.
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $inventarioModel = new Inventario();
            $inventarioImagenModel = new InventarioImagen(); // Instanciamos el modelo de imágenes

            $data = $_POST; // Datos del formulario

            // Determinar si es una edición
            $isEditing = !empty($data['id']);
            $currentId = (int)($data['id'] ?? 0);

            // Saneamiento inicial (trim) para todos los campos de texto
            $data['nombre_equipo'] = trim($data['nombre_equipo'] ?? '');
            $data['marca'] = trim($data['marca'] ?? '');
            $data['modelo'] = trim($data['modelo'] ?? '');
            $data['notas_donacion'] = trim($data['notas_donacion'] ?? '');

            // Normalizar y construir el número de serie completo
            $data['prefijo_serie'] = strtoupper(trim($data['prefijo_serie'] ?? '')); // ALL CAPS
            if (!empty($data['prefijo_serie']) && substr($data['prefijo_serie'], -1) !== '-') {
                $data['prefijo_serie'] .= '-';
            }
            $data['numero_inicio_serie'] = (int)($data['numero_inicio_serie'] ?? 0);
            $baseSerial = $data['prefijo_serie'] . sprintf('%04d', $data['numero_inicio_serie']); // Formato XXXXX-0001

            $cantidad = (int)($data['cantidad'] ?? 1); // Por defecto es 1 si no se envía

            // ID de la categoría "Clave de Software" (AJUSTA ESTE VALOR AL ID REAL DE TU BD)
            $softwareKeyCategoryId = 9; // El ID para 'Software: Licencia de Software' es 9 según el HTML de add_edit.php


            try {
                // Validaciones Comunes para CREACIÓN y EDICIÓN
                if (empty($data['nombre_equipo'])) {
                    throw new \Exception('El nombre del equipo es obligatorio.');
                }
                if (empty($data['categoria_id'])) {
                    throw new \Exception('La categoría es obligatoria.');
                }
                if (empty($data['marca'])) {
                    throw new \Exception('La marca es obligatoria.');
                }
                if (empty($data['modelo'])) {
                    throw new \Exception('El modelo es obligatorio.');
                }
                if (empty($baseSerial)) { // La serie ya construida debe existir
                    throw new \Exception('El número de serie es obligatorio (prefijo + número).');
                }
                if (!preg_match('/^[a-zA-Z0-9\s\-_]+$/', $baseSerial)) {
                    throw new \Exception('El número de serie contiene caracteres no permitidos. Solo se permiten letras, números, guiones, guiones bajos y espacios.');
                }
                if (strlen($baseSerial) > 50) {
                    throw new \Exception('El número de serie es demasiado largo (máximo 50 caracteres).');
                }
                if (!is_numeric($data['costo']) || (float)$data['costo'] < 0) {
                    throw new \Exception('El costo debe ser un número válido y no negativo.');
                }
                if (empty($data['fecha_ingreso'])) {
                    throw new \Exception('La fecha de ingreso es obligatoria.');
                }
                if (!is_numeric($data['tiempo_depreciacion_anios']) || (int)$data['tiempo_depreciacion_anios'] < 0) {
                    throw new \Exception('La depreciación en años debe ser un número entero no negativo.');
                }

                if ($isEditing) {
                    // Lógica para EDICIÓN de un solo equipo existente
                    // La validación de unicidad de serie la maneja InventarioModel::save()
                    if (empty($data['estado'])) {
                        throw new \Exception('El estado del equipo es obligatorio.');
                    }
                    if (isset($data['estado']) && in_array($data['estado'], ['En Descarte', 'Donado'])) {
                        if (empty(trim($data['notas_donacion'] ?? ''))) {
                            throw new \Exception('Las notas de donación/descarte son obligatorias para el estado seleccionado.');
                        }
                    }

                    $equipoData = [
                        'id' => $currentId,
                        'nombre_equipo' => $data['nombre_equipo'],
                        'marca' => $data['marca'],
                        'modelo' => $data['modelo'],
                        'serie' => $baseSerial, // La serie construida
                        'costo' => $data['costo'],
                        'fecha_ingreso' => $data['fecha_ingreso'],
                        'tiempo_depreciacion_anios' => $data['tiempo_depreciacion_anios'],
                        'categoria_id' => $data['categoria_id'],
                        'estado' => $data['estado'],
                        'notas_donacion' => $data['notas_donacion'],
                    ];

                    $inventarioModel->save($equipoData);
                    $_SESSION['mensaje_sa2'] = ['title' => '¡Éxito!', 'text' => 'Equipo actualizado correctamente.', 'icon' => 'success'];
                } else {
                    // Lógica para CREACIÓN (individual o por lote)
                    if ($cantidad < 1) {
                        throw new \Exception('La cantidad de equipos debe ser al menos 1.');
                    }
                    if ($data['numero_inicio_serie'] < 0) {
                        throw new \Exception('El número inicial de serie no puede ser negativo.');
                    }
                    if ($cantidad > 1 && (int)($data['categoria_id'] ?? 0) === $softwareKeyCategoryId) {
                        throw new \Exception('No se permite la creación por lote para la categoría "Clave de Software".');
                    }

                    // --- Lógica de subida y procesamiento de la miniatura ---
                    $thumbnailFileName = null; // Inicializar a null

                    // Si hay un archivo de miniatura subido
                    if (isset($_FILES['imagen_miniatura']) && $_FILES['imagen_miniatura']['error'] === UPLOAD_ERR_OK) {
                        $imagen = $_FILES['imagen_miniatura'];
                        $allowed_extensions = ['jpg', 'jpeg', 'png'];
                        $file_extension = strtolower(pathinfo($imagen['name'], PATHINFO_EXTENSION));

                        if (!in_array($file_extension, $allowed_extensions)) {
                            throw new \Exception('Solo se permiten imágenes (jpg, jpeg, png) para la miniatura.');
                        }

                        $uploadDir = '../public/uploads/inventario/';
                        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                        $thumbnailFileName = uniqid() . '-' . basename($imagen['name']);
                        $targetPath = $uploadDir . $thumbnailFileName;

                        if (!move_uploaded_file($imagen['tmp_name'], $targetPath)) {
                            throw new \Exception('No se pudo subir la miniatura. Revisa los permisos de la carpeta de uploads.');
                        }
                    } elseif ($cantidad > 1) { // Si es un lote, la miniatura es OBLIGATORIA
                        throw new \Exception('La miniatura es obligatoria para la creación de equipos por lote.');
                    }

                    // Validación Previa de Rango de Series (solo si es lote)
                    if ($cantidad > 1) {
                        $seriesDuplicadasPrevias = $inventarioModel->checkBatchSerialRangeForDuplicates($data['prefijo_serie'], $data['numero_inicio_serie'], $cantidad);
                        if (!empty($seriesDuplicadasPrevias)) {
                            $_SESSION['mensaje_sa2'] = ['title' => '¡Error de Lote!', 'text' => 'Algunas series en el rango propuesto ya existen. Por favor, ajuste el Número Inicial de Serie o el Prefijo de Serie. Series en conflicto: ' . implode(', ', $seriesDuplicadasPrevias), 'icon' => 'error'];
                            header('Location: ' . BASE_URL . 'index.php?route=inventario&action=showAddForm');
                            exit;
                        }
                    }

                    $commonData = [
                        'nombre_equipo' => $data['nombre_equipo'],
                        'categoria_id' => $data['categoria_id'],
                        'marca' => $data['marca'],
                        'modelo' => $data['modelo'],
                        'costo' => $data['costo'],
                        'fecha_ingreso' => $data['fecha_ingreso'],
                        'tiempo_depreciacion_anios' => $data['tiempo_depreciacion_anios'],
                        'estado' => 'Disponible', // Estado por defecto para nuevos equipos
                        'notas_donacion' => null,
                    ];

                    $erroresSerie = [];
                    $equiposInsertados = 0;

                    for ($i = 0; $i < $cantidad; $i++) {
                        $numeroSerieActual = $data['numero_inicio_serie'] + $i;
                        $serieGenerada = $data['prefijo_serie'] . sprintf('%04d', $numeroSerieActual);

                        $itemData = $commonData;
                        $itemData['serie'] = $serieGenerada;

                        try {
                            // Llamamos a InventarioModel::save para crear el equipo base
                            $newEquipmentId = $inventarioModel->save($itemData); // Obtiene el ID del equipo recién creado

                            // --- Guardar miniatura a través de InventarioImagenModel ---
                            // Si se subió una miniatura, guardarla en inventario_imagenes y marcarla como thumbnail
                            if ($thumbnailFileName) {
                                $inventarioImagenModel->save($newEquipmentId, $thumbnailFileName);
                                // Marcar la imagen recién guardada como thumbnail (usando su propio ID)
                                $inventarioImagenModel->setThumbnail($newEquipmentId, \App\Core\Database::getInstance()->lastInsertId());
                            }
                            $equiposInsertados++;
                        } catch (\Exception $e) {
                            if (strpos($e->getMessage(), 'El número de serie') !== false && strpos($e->getMessage(), 'ya está registrado') !== false) {
                                $erroresSerie[] = $serieGenerada;
                            } else {
                                error_log("Error al guardar la serie '{$serieGenerada}': " . $e->getMessage());
                                $erroresSerie[] = $serieGenerada . " (Error desconocido)";
                            }
                        }
                    }

                    if ($equiposInsertados > 0 && empty($erroresSerie)) {
                        $msgText = ($cantidad == 1) ? 'Equipo guardado correctamente.' : "Se añadieron {$equiposInsertados} equipos por lote correctamente.";
                        $_SESSION['mensaje_sa2'] = ['title' => '¡Éxito!', 'text' => $msgText, 'icon' => 'success'];
                    } elseif ($equiposInsertados > 0 && !empty($erroresSerie)) {
                        $msgText = ($cantidad == 1) ? 'Se guardó 1 equipo, pero hubo problemas.' : "Se añadieron {$equiposInsertados} equipos con éxito, pero " . count($erroresSerie) . " series fueron omitidas porque ya existían o hubo un problema: " . implode(', ', $erroresSerie) . ". Por favor, revise el inventario.";
                        $_SESSION['mensaje_sa2'] = ['title' => '¡Alerta!', 'text' => $msgText, 'icon' => 'warning'];
                    } else {
                        $_SESSION['mensaje_sa2'] = ['title' => '¡Error!', 'text' => 'No se pudo añadir ningún equipo. Todas las series generadas ya existen o hubo otros problemas. Por favor, verifique el formulario.', 'icon' => 'error'];
                    }
                }
            } catch (\Throwable $e) {
                $_SESSION['mensaje_sa2'] = ['title' => '¡Error!', 'text' => $e->getMessage(), 'icon' => 'error'];
            }
            header('Location: ' . BASE_URL . 'index.php?route=inventario'); // Redirigir siempre
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
                $data = $_POST; // Datos del formulario

                // 1. Saneamiento inicial (trim)
                $data['serie'] = trim($data['serie'] ?? '');
                $data['nombre_equipo'] = trim($data['nombre_equipo'] ?? '');
                $data['marca'] = trim($data['marca'] ?? '');
                $data['modelo'] = trim($data['modelo'] ?? '');
                $data['notas_donacion'] = trim($data['notas_donacion'] ?? '');

                // 2. Validaciones de datos (obligatorios, formato, rangos)
                if (empty($data['id'])) {
                    throw new \Exception('ID de equipo no proporcionado para la actualización.');
                }
                if (empty($data['nombre_equipo'])) {
                    throw new \Exception('El nombre del equipo es obligatorio.');
                }
                if (empty($data['categoria_id'])) {
                    throw new \Exception('La categoría es obligatoria.');
                }
                if (empty($data['marca'])) {
                    throw new \Exception('La marca es obligatoria.');
                }
                if (empty($data['modelo'])) {
                    throw new \Exception('El modelo es obligatorio.');
                }
                if (empty($data['serie'])) {
                    throw new \Exception('El número de serie es obligatorio.');
                }
                // Validar formato de serie
                if (!preg_match('/^[a-zA-Z0-9\s\-_]+$/', $data['serie'])) {
                    throw new \Exception('El número de serie contiene caracteres no permitidos. Solo se permiten letras, números, guiones, guiones bajos y espacios.');
                }
                if (strlen($data['serie']) > 50) { // Longitud máxima
                    throw new \Exception('El número de serie es demasiado largo (máximo 50 caracteres).');
                }
                if (!is_numeric($data['costo']) || (float)$data['costo'] < 0) {
                    throw new \Exception('El costo debe ser un número válido y no negativo.');
                }
                if (empty($data['fecha_ingreso'])) {
                    throw new \Exception('La fecha de ingreso es obligatoria.');
                }
                if (!is_numeric($data['tiempo_depreciacion_anios']) || (int)$data['tiempo_depreciacion_anios'] < 0) {
                    throw new \Exception('La depreciación en años debe ser un número entero no negativo.');
                }
                if (empty($data['estado'])) {
                    throw new \Exception('El estado del equipo es obligatorio.');
                }
                // La validación de notas de donación/descarte ahora se hace aquí, antes de save()
                if (isset($data['estado']) && in_array($data['estado'], ['En Descarte', 'Donado'])) {
                    if (empty(trim($data['notas_donacion'] ?? ''))) {
                        throw new \Exception('Las notas de donación/descarte son obligatorias para el estado seleccionado.');
                    }
                }

                // Si llegamos aquí, los datos son válidos en el controlador
                // El modelo se encargará de la unicidad de la serie y el guardado
                $inventarioModel->save($data);
                $_SESSION['mensaje_sa2'] = ['title' => '¡Éxito!', 'text' => 'Equipo actualizado correctamente.', 'icon' => 'success'];
            } catch (\Throwable $e) {
                $_SESSION['mensaje_sa2'] = ['title' => '¡Error!', 'text' => $e->getMessage(), 'icon' => 'error'];
            }
            header('Location: ' . BASE_URL . 'index.php?route=inventario');
            exit;
        }
    }

    /**
     * Muestra el formulario para asignar un equipo a un colaborador.
     */
    public function showAssignForm()
    {
        $inventario_id = (int)($_GET['id'] ?? 0);
        $equipo = (new Inventario())->findById($inventario_id);

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
                $_SESSION['mensaje_sa2'] = ['title' => '¡Error!', 'text' => 'No se pudo des-asignar el equipo: ' . $e->getMessage(), 'icon' => 'error'];
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
     * Genera y muestra una imagen de código QR para un equipo.
     */


    public function showQrCode()
    {
        $id = (int)($_GET['id'] ?? 0);
        $publicUrl = BASE_URL . 'index.php?route=public&action=showEquipo&id=' . $id;

        // Construir el builder manualmente (no usar Builder::create())
        $builder = new Builder(
            writer: new PngWriter(),
            data: $publicUrl,
            size: 300,
            margin: 10
        );

        $result = $builder->build();

        header('Content-Type: ' . $result->getMimeType());
        echo $result->getString();
        exit;
    }

    /**
     * Muestra una vista filtrada únicamente con los equipos donados.
     */
    public function showDonados()
    {
        // 1. Recopila parámetros de la URL para búsqueda, orden, etc.
        $options = $_GET;
        $page = (int)($_GET['page'] ?? 1);
        $perPage = (int)($_GET['perPage'] ?? 10);

        // 2. FUERZA el filtro para que solo muestre equipos con estado 'Donado'.
        $options['filters']['i.estado'] = 'Donado';

        // 3. Obtiene los datos usando la lógica del modelo que ya existe.
        $inventarioModel = new Inventario();
        $data = $inventarioModel->findAll($options);
        $totalRecords = $inventarioModel->countFiltered($options);
        $totalPages = ceil($totalRecords / $perPage);

        // 4. Prepara la configuración para la tabla reutilizable.
        $tableConfig = [
            'columns' => [
                ['header' => 'ID', 'field' => 'id', 'sort_by' => 'i.id'],
                ['header' => 'Equipo', 'field' => 'nombre_equipo', 'sort_by' => 'i.nombre_equipo'],
                ['header' => 'Categoría', 'field' => 'nombre_categoria', 'sort_by' => 'nombre_categoria'],
                ['header' => 'Notas de Donación', 'field' => 'notas_donacion'],
                ['header' => 'Fecha Ingreso', 'field' => 'fecha_ingreso', 'sort_by' => 'i.fecha_ingreso'],
            ],
            'data' => $data,
            'pagination' => [
                'currentPage' => $page,
                'perPage' => $perPage,
                'totalPages' => $totalPages,
                'totalRecords' => $totalRecords,
                'search' => $_GET['search'] ?? '',
                'sort' => $_GET['sort'] ?? 'id',
                'order' => $_GET['order'] ?? 'desc',
                'filters' => $options['filters']
            ],
            'actions' => [] // No hay acciones para los equipos donados
        ];

        // 5. Renderiza la nueva vista.
        $this->render('Views/inventario/donados.php', [
            'pageTitle' => 'Inventario de Equipos Donados',
            'tableConfig' => $tableConfig
        ]);
    }

    /**
     * Revierte el estado de un equipo de 'Donado' a 'En Stock'.
     */
    public function revertirDonacion()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)$_POST['id'];
            $inventarioModel = new Inventario();

            // 1. Busca los datos actuales del equipo
            $equipo = $inventarioModel->findById($id);

            if ($equipo) {
                // 2. Modifica solo los campos necesarios
                $equipo['estado'] = 'En Stock';
                $equipo['notas_donacion'] = null; // Limpia las notas

                // 3. Llama al método save() con los datos actualizados
                $inventarioModel->save($equipo);
                $_SESSION['mensaje_sa2'] = ['title' => '¡Revertido!', 'text' => 'El equipo ha vuelto al inventario.', 'icon' => 'success'];
            } else {
                $_SESSION['mensaje_sa2'] = ['title' => 'Error', 'text' => 'No se encontró el equipo.', 'icon' => 'error'];
            }

            header('Location: ' . BASE_URL . 'index.php?route=inventario&action=showDonados');
            exit;
        }
    }

    /**
     * Muestra una vista filtrada únicamente con los equipos "En Descarte".
     * Es idéntica a showDonados pero filtra por 'En Descarte'.
     */
    public function showDescartados()
    {
        // 1. Recopila parámetros de la URL para búsqueda, orden, etc.
        $options = $_GET;
        $page = (int)($_GET['page'] ?? 1);
        $perPage = (int)($_GET['perPage'] ?? 10);

        // 2. FUERZA el filtro para que solo muestre equipos con estado 'En Descarte'.
        $options['filters']['i.estado'] = 'En Descarte';

        // 3. Obtiene los datos usando la lógica del modelo que ya existe.
        $inventarioModel = new Inventario();
        $data = $inventarioModel->findAll($options);
        $totalRecords = $inventarioModel->countFiltered($options);
        $totalPages = ceil($totalRecords / $perPage);

        // 4. Prepara la configuración para la tabla reutilizable.
        $tableConfig = [
            'columns' => [
                ['header' => 'ID', 'field' => 'id', 'sort_by' => 'i.id'],
                ['header' => 'Equipo', 'field' => 'nombre_equipo', 'sort_by' => 'i.nombre_equipo'],
                ['header' => 'Categoría', 'field' => 'nombre_categoria', 'sort_by' => 'nombre_categoria'],
                ['header' => 'Notas de Descarte', 'field' => 'notas_donacion'], // Se usa el mismo campo para notas
                ['header' => 'Fecha Ingreso', 'field' => 'fecha_ingreso', 'sort_by' => 'i.fecha_ingreso'],
            ],
            'data' => $data,
            'pagination' => [
                'currentPage' => $page,
                'perPage' => $perPage,
                'totalPages' => $totalPages,
                'totalRecords' => $totalRecords,
                'search' => $_GET['search'] ?? '',
                'sort' => $_GET['sort'] ?? 'id',
                'order' => $_GET['order'] ?? 'desc',
                'filters' => $options['filters']
            ],
            'actions' => [] // No hay acciones directas para los equipos descartados desde aquí
        ];

        // 5. Renderiza la nueva vista.
        $this->render('Views/inventario/descartados.php', [
            'pageTitle' => 'Inventario de Equipos Descartados',
            'tableConfig' => $tableConfig
        ]);
    }

    /**
     * Revierte el estado de un equipo de 'En Descarte' a 'En Stock'.
     * Es similar a revertirDonacion.
     */
    public function revertirDescarte()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)$_POST['id'];
            $inventarioModel = new Inventario();

            $equipo = $inventarioModel->findById($id);

            if ($equipo) {
                $equipo['estado'] = 'En Stock';
                $equipo['notas_donacion'] = null; // Limpia las notas de descarte

                $inventarioModel->save($equipo);
                $_SESSION['mensaje_sa2'] = ['title' => '¡Revertido!', 'text' => 'El equipo ha vuelto al inventario.', 'icon' => 'success'];
            } else {
                $_SESSION['mensaje_sa2'] = ['title' => 'Error', 'text' => 'No se encontró el equipo.', 'icon' => 'error'];
            }

            header('Location: ' . BASE_URL . 'index.php?route=inventario&action=showDescartados');
            exit;
        }
    }
}
