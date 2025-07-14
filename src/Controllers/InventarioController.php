<?php

namespace App\Controllers;

use App\Models\Inventario;
use App\Models\Categoria;

class InventarioController extends BaseController
{

    public function index()
    {
        $inventarioModel = new Inventario();
        $categoriaModel = new Categoria();

        // Lógica de ordenamiento
        $sort = $_GET['sort'] ?? 'id';
        $order = $_GET['order'] ?? 'asc';

        // Preparamos los datos para la vista
        $data = [
            'inventarios' => $inventarioModel->findAll(['sort' => $sort, 'order' => $order]),
            'categorias' => $categoriaModel->findAll(),
            'pageTitle' => 'Gestionar Inventario',
            'formId' => 'form-inventario'
        ];

        $this->render('Views/inventario/index.php', $data);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $inventarioModel = new Inventario();
            $inventarioModel->save($_POST);

            // Guardamos el mensaje en la sesión
            $_SESSION['mensaje_sa2'] = [
                'title' => '¡Éxito!',
                'text' => 'Equipo guardado correctamente.',
                'icon' => 'success'
            ];

            header('Location: ' . BASE_URL . 'index.php?route=inventario');
            exit;
        }
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $inventarioModel = new Inventario();
            $inventarioModel->save($_POST);

            // Guardamos el mensaje en la sesión
            $_SESSION['mensaje_sa2'] = [
                'title' => '¡Éxito!',
                'text' => 'Equipo actualizado correctamente.',
                'icon' => 'success'
            ];

            header('Location: ' . BASE_URL . 'index.php?route=inventario');
            exit;
        }
    }

    public function destroy()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $inventarioModel = new Inventario();
            $inventarioModel->delete($_POST['id']);

            // Guardamos el mensaje en la sesión
            $_SESSION['mensaje_sa2'] = [
                'title' => '¡Éxito!',
                'text' => 'Equipo eliminado correctamente.',
                'icon' => 'success'
            ];

            header('Location: ' . BASE_URL . 'index.php?route=inventario');
            exit;
        }
    }

    /**
     * Muestra la página de gestión de imágenes para un equipo específico.
     */
    public function showImages()
    {
        $inventario_id = $_GET['id'] ?? 0;

        $inventarioModel = new Inventario();
        $imagenModel = new \App\Models\InventarioImagen();

        $equipo = $inventarioModel->findById($inventario_id);
        $imagenes = $imagenModel->findByInventarioId($inventario_id);

        $data = [
            'equipo' => $equipo,
            'imagenes' => $imagenes,
            'pageTitle' => 'Gestionar Imágenes',
        ];

        $this->render('Views/inventario/imagenes.php', $data);
    }

    /**
     * Procesa la subida de una nueva imagen, validando el tipo de archivo.
     */
    public function uploadImage()
    {
        // Asegurarse de que el inventario_id esté presente
        if (!isset($_POST['inventario_id'])) {
            // Redirigir o mostrar un error si no hay ID
            header('Location: ' . BASE_URL . 'index.php?route=inventario');
            exit;
        }

        $inventario_id = $_POST['inventario_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['imagen'])) {
            $imagen = $_FILES['imagen'];

            if ($imagen['error'] === UPLOAD_ERR_OK) {

                // --- INICIO DE LA VALIDACIÓN DE TIPO DE ARCHIVO ---

                $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];
                $allowed_mime_types = ['image/jpeg', 'image/png', 'image/webp'];

                $file_extension = strtolower(pathinfo($imagen['name'], PATHINFO_EXTENSION));
                $file_mime_type = $imagen['type'];

                if (in_array($file_extension, $allowed_extensions) && in_array($file_mime_type, $allowed_mime_types)) {

                    // Si la validación es exitosa, procedemos a mover el archivo
                    $uploadDir = '../public/uploads/inventario/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    $fileName = uniqid() . '-' . basename($imagen['name']);
                    $targetPath = $uploadDir . $fileName;

                    if (move_uploaded_file($imagen['tmp_name'], $targetPath)) {
                        $imagenModel = new \App\Models\InventarioImagen();
                        $imagenModel->save($inventario_id, $fileName);
                        $_SESSION['mensaje_sa2'] = ['title' => '¡Éxito!', 'text' => 'La imagen se ha subido correctamente.', 'icon' => 'success'];
                    } else {
                        $_SESSION['mensaje_sa2'] = ['title' => '¡Error de Servidor!', 'text' => 'No se pudo mover el archivo. Verifica los permisos.', 'icon' => 'error'];
                    }
                } else {
                    // Si el tipo de archivo no es válido, preparamos un mensaje de error
                    $_SESSION['mensaje_sa2'] = ['title' => 'Archivo no Válido', 'text' => 'Solo se permiten archivos de tipo JP(E)G, PNG o WEBP.', 'icon' => 'error'];
                }
                // --- FIN DE LA VALIDACIÓN ---

            } else {
                $_SESSION['mensaje_sa2'] = ['title' => 'Error de Subida', 'text' => 'Hubo un problema al recibir el archivo.', 'icon' => 'error'];
            }
        } else {
            $_SESSION['mensaje_sa2'] = ['title' => 'Error', 'text' => 'No se recibió ningún archivo.', 'icon' => 'error'];
        }

        // Redirige de vuelta a la página de imágenes
        header('Location: ' . BASE_URL . 'index.php?route=inventario&action=showImages&id=' . $inventario_id);
        exit;
    }

    /**
     * Elimina una imagen.
     */
    public function destroyImage()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $imagen_id = $_POST['imagen_id'];
            $inventario_id = $_POST['inventario_id'];

            $imagenModel = new \App\Models\InventarioImagen();
            $imagen = $imagenModel->findById($imagen_id);

            if ($imagen) {
                // 1. Borrar el archivo físico del servidor
                $filePath = '../public/uploads/inventario/' . $imagen['ruta_imagen'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                // 2. Borrar el registro de la base de datos
                $imagenModel->delete($imagen_id);
                $mensaje = 'Imagen eliminada.';
            } else {
                $mensaje = 'Error: No se encontró la imagen.';
            }

            // Guardamos el mensaje en sesion
            $_SESSION['mensaje_sa2'] = [
                'title' => '¡Éxito!',
                'text' => $mensaje,
                'icon' => 'success'
            ];

            header('Location: ' . BASE_URL . 'index.php?route=inventario&action=showImages&id=' . $inventario_id);
            exit;
        }
    }

    /**
     * Establece una imagen como thumbnail.
     */
    public function setThumbnail()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $imagen_id = $_POST['imagen_id'];
            $inventario_id = $_POST['inventario_id'];

            $imagenModel = new \App\Models\InventarioImagen();
            $imagenModel->setThumbnail($inventario_id, $imagen_id);

            // Guardamos el mensaje en la sesión
            $_SESSION['mensaje_sa2'] = [
                'title' => '¡Éxito!',
                'text' => 'Thumbnail actualizado.',
                'icon' => 'success'
            ];

            header('Location: ' . BASE_URL . 'index.php?route=inventario&action=showImages&id=' . $inventario_id);
            exit;
        }
    }
}
