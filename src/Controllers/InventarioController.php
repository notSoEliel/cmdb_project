<?php

namespace App\Controllers;

use App\Models\Inventario;
use App\Models\Categoria;
use App\Models\InventarioImagen;

/**
 * Class InventarioController
 *
 * Gestiona todas las acciones relacionadas con el módulo de inventario,
 * incluyendo la visualización de la lista, la creación, actualización,
 * eliminación de equipos y la gestión de sus imágenes.
 */
class InventarioController extends BaseController
{
    /**
     * Muestra la página principal del inventario.
     * Recoge los parámetros de la URL para la búsqueda, paginación y ordenamiento,
     * prepara una configuración para la tabla dinámica y renderiza la vista.
     */
    public function index()
    {
        // --- 1. Recopilación de Parámetros de la URL ---
        $page = (int)($_GET['page'] ?? 1);
        $perPage = (int)($_GET['perPage'] ?? 10);
        $search = $_GET['search'] ?? '';
        $sort = $_GET['sort'] ?? 'id';
        $order = $_GET['order'] ?? 'asc';
        $filters = [];
        if (!empty($_GET['categoria_id'])) {
            $filters['categoria_id'] = $_GET['categoria_id'];
        }

        // --- 2. Preparación de Modelos y Opciones ---
        $inventarioModel = new Inventario();
        $categoriaModel = new Categoria();

        // Array de opciones para pasar a los métodos del modelo
        $options = [
            'page' => $page,
            'perPage' => $perPage,
            'sort' => $sort,
            'order' => $order,
            'search' => $search,
            'filters' => $filters,
            // Le indicamos al modelo qué columnas queremos seleccionar con el JOIN
            'selectClause' => 'i.*, c.nombre as nombre_categoria' 
        ];

        // --- 3. Obtención de Datos y Cálculo de Paginación ---
        $inventarios = $inventarioModel->findAll($options);
        $totalRecords = $inventarioModel->countFiltered($options);
        $totalPages = ceil($totalRecords / $perPage);

        // --- 4. Configuración para la Tabla Reutilizable ---
        $tableConfig = [
            'columns' => [
                ['header' => '', 'field' => 'thumbnail_path', 'type' => 'image'],
                ['header' => 'ID', 'field' => 'id'],
                ['header' => 'Equipo', 'field' => 'nombre_equipo'],
                ['header' => 'Categoría', 'field' => 'nombre_categoria'],
                ['header' => 'Marca', 'field' => 'marca'],
                ['header' => 'Serie', 'field' => 'serie'],
                ['header' => 'Estado', 'field' => 'estado'],
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
                'edit_route' => 'inventario&action=index', // La edición se maneja en la misma página
                'delete_route' => 'inventario&action=destroy',
                'image_route' => 'inventario&action=showImages'
            ]
        ];

        // --- 5. Renderizado de la Vista ---
        $this->render('Views/inventario/index.php', [
            'pageTitle' => 'Gestionar Inventario',
            'formId' => 'form-inventario',
            'categorias' => $categoriaModel->findAll(), // Para el dropdown del formulario
            'tableConfig' => $tableConfig // Pasamos toda la config a la vista
        ]);
    }

    /**
     * Procesa la creación de un nuevo equipo.
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $inventarioModel = new Inventario();
            $inventarioModel->save($_POST);
            $_SESSION['mensaje_sa2'] = ['title' => '¡Éxito!', 'text' => 'Equipo guardado.', 'icon' => 'success'];
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
            $inventarioModel = new Inventario();
            $inventarioModel->save($_POST);
            $_SESSION['mensaje_sa2'] = ['title' => '¡Éxito!', 'text' => 'Equipo actualizado.', 'icon' => 'success'];
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
}