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
            'pageTitle' => 'Gestionar Inventario'
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
            'pageTitle' => 'Gestionar Imágenes'
        ];

        $this->render('Views/inventario/imagenes.php', $data);
    }

    /**
     * Procesa la subida de una nueva imagen.
     */
    public function uploadImage()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['imagen'], $_POST['inventario_id'])) {
            $inventario_id = $_POST['inventario_id'];
            $imagen = $_FILES['imagen'];

            if ($imagen['error'] === UPLOAD_ERR_OK) {
                $uploadDir = '../public/uploads/inventario/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $fileName = uniqid() . '-' . basename($imagen['name']);
                $targetPath = $uploadDir . $fileName;

                if (move_uploaded_file($imagen['tmp_name'], $targetPath)) {
                    $imagenModel = new \App\Models\InventarioImagen();
                    $imagenModel->save($inventario_id, $fileName);

                    $_SESSION['mensaje_sa2'] = ['title' => '¡Subida!', 'text' => 'La imagen se ha subido correctamente.', 'icon' => 'success'];
                } else {
                    // ¡Este es el mensaje de error clave!
                    $_SESSION['mensaje_sa2'] = ['title' => '¡Error de Permisos!', 'text' => 'No se pudo mover el archivo. Asegúrate de que la carpeta /public/uploads/inventario/ tenga permisos de escritura.', 'icon' => 'error'];
                }
            } else {
                $_SESSION['mensaje_sa2'] = ['title' => 'Error de Subida', 'text' => 'Hubo un problema al recibir el archivo.', 'icon' => 'error'];
            }
        } else {
            $_SESSION['mensaje_sa2'] = ['title' => 'Error', 'text' => 'No se recibió ningún archivo o falta el ID del inventario.', 'icon' => 'error'];
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
