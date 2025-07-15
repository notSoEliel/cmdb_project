<?php

namespace App\Controllers;

use App\Models\Categoria;

/**
 * Class CategoriaController
 *
 * Gestiona las acciones para el módulo de Categorías.
 */
class CategoriaController extends BaseController
{
    /**
     * Muestra la página principal de Categorías con la tabla dinámica.
     */
    public function index()
    {
        // 1. Recopila parámetros de la URL.
        $page = (int)($_GET['page'] ?? 1);
        $perPage = (int)($_GET['perPage'] ?? 10);
        $search = $_GET['search'] ?? '';
        $sort = $_GET['sort'] ?? 'c.nombre';
        $order = $_GET['order'] ?? 'asc';

        // 2. Prepara el modelo y las opciones.
        $categoriaModel = new Categoria();
        $options = [
            'page' => $page,
            'perPage' => $perPage,
            'sort' => $sort,
            'order' => $order,
            'search' => $search,
        ];

        // 3. Obtiene los datos y el conteo total.
        $categorias = $categoriaModel->findAll($options);
        $totalRecords = $categoriaModel->countFiltered($options);
        $totalPages = ceil($totalRecords / $perPage);

        // 4. Prepara la configuración para la tabla reutilizable.
        $tableConfig = [
            'columns' => [
                // 'field' es para mostrar el dato, 'sort_by' es para ordenar
                ['header' => 'ID', 'field' => 'id', 'sort_by' => 'c.id'],
                ['header' => 'Nombre', 'field' => 'nombre', 'sort_by' => 'c.nombre'],
            ],
            'data' => $categorias,
            'pagination' => [
                'currentPage' => $page,
                'perPage' => $perPage,
                'totalPages' => $totalPages,
                'totalRecords' => $totalRecords,
                'search' => $search,
                'sort' => $sort,
                'order' => $order,
                'filters' => []
            ],
            'actions' => [
                'edit_route' => 'categorias&action=index',
                'delete_route' => 'categorias&action=destroy',
            ]
        ];

        // 5. Renderiza la vista, pasándole la configuración.
        $this->render('Views/categorias/index.php', [
            'pageTitle' => 'Gestionar Categorías',
            'tableConfig' => $tableConfig
        ]);
    }

    /**
     * Procesa el guardado de una nueva categoría.
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            (new Categoria())->save($_POST);
            $_SESSION['mensaje_sa2'] = ['title' => '¡Éxito!', 'text' => 'Categoría creada.', 'icon' => 'success'];
            header('Location: ' . BASE_URL . 'index.php?route=categorias');
            exit;
        }
    }

    /**
     * Procesa la actualización de una categoría existente.
     */
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            (new Categoria())->save($_POST);
            $_SESSION['mensaje_sa2'] = ['title' => '¡Éxito!', 'text' => 'Categoría actualizada.', 'icon' => 'success'];
            header('Location: ' . BASE_URL . 'index.php?route=categorias');
            exit;
        }
    }

    /**
     * Procesa la eliminación de una categoría.
     */
    public function destroy()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            (new Categoria())->delete($_POST['id']);
            $_SESSION['mensaje_sa2'] = ['title' => '¡Eliminada!', 'text' => 'Categoría eliminada.', 'icon' => 'success'];
            header('Location: ' . BASE_URL . 'index.php?route=categorias');
            exit;
        }
    }
}
