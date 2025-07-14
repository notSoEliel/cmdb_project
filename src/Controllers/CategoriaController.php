<?php

namespace App\Controllers;

use App\Models\Categoria;

class CategoriaController extends BaseController
{

    public function index()
    {
        $categoriaModel = new Categoria();

        // Lógica de ordenamiento
        $sort = $_GET['sort'] ?? 'id';
        $order = $_GET['order'] ?? 'asc';

        // Preparamos los datos para la vista
        $data = [
            'categorias' => $categoriaModel->findAll(['sort' => $sort, 'order' => $order]),
            'pageTitle' => 'Gestionar Categorías'
        ];

        // En lugar de 'require', ahora usamos 'render'
        $this->render('Views/categorias/index.php', $data);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categoriaModel = new Categoria();
            $categoriaModel->save($_POST);

            // Guardamos el mensaje en la sesión
            $_SESSION['mensaje_sa2'] = [
                'title' => '¡Éxito!',
                'text' => 'Categoría creada correctamente.',
                'icon' => 'success'
            ];

            header('Location: ' . BASE_URL . 'index.php?route=categorias');
            exit;
        }
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categoriaModel = new Categoria();
            $categoriaModel->save($_POST);

            // Guardamos el mensaje en la sesión
            $_SESSION['mensaje_sa2'] = [
                'title' => '¡Éxito!',
                'text' => 'Categoría actualizada correctamente.',
                'icon' => 'success'
            ];

            header('Location: ' . BASE_URL . 'index.php?route=categorias');
            exit;
        }
    }

    public function destroy()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categoriaModel = new Categoria();
            $categoriaModel->delete($_POST['id']);

            // Guardamos el mensaje en la sesión
            $_SESSION['mensaje_sa2'] = [
                'title' => '¡Éxito!',
                'text' => 'Categoría eliminada correctamente.',
                'icon' => 'success'
            ];

            header('Location: ' . BASE_URL . 'index.php?route=categorias');
            exit;
        }
    }
}
