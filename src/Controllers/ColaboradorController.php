<?php

namespace App\Controllers;

use App\Models\Colaborador;

/**
 * Class ColaboradorController
 *
 * Gestiona todas las acciones para el módulo de Colaboradores,
 * implementando la lógica para la búsqueda, paginación y ordenamiento.
 */
class ColaboradorController extends BaseController
{
    /**
     * Muestra la página principal de Colaboradores con la tabla dinámica.
     */
    public function index()
    {
        // 1. Recopila los parámetros de la URL para la paginación, búsqueda y orden.
        $page = (int)($_GET['page'] ?? 1);
        $perPage = (int)($_GET['perPage'] ?? 10);
        $search = $_GET['search'] ?? '';
        $sort = $_GET['sort'] ?? 'id';
        $order = $_GET['order'] ?? 'asc';

        // 2. Prepara las opciones y el modelo.
        $colaboradorModel = new Colaborador();
        $options = [
            'page' => $page,
            'perPage' => $perPage,
            'sort' => $sort,
            'order' => $order,
            'search' => $search,
        ];

        // 3. Obtiene los datos paginados y el conteo total de registros.
        $colaboradores = $colaboradorModel->findAll($options);
        $totalRecords = $colaboradorModel->countFiltered($options);
        $totalPages = ceil($totalRecords / $perPage);

        // 4. Prepara la configuración para el componente de tabla reutilizable.
        $tableConfig = [
            'columns' => [
                ['header' => 'ID', 'field' => 'id'],
                ['header' => 'Nombre', 'field' => 'nombre'],
                ['header' => 'Apellido', 'field' => 'apellido'],
                ['header' => 'Email', 'field' => 'email'],
                ['header' => 'Ubicación', 'field' => 'ubicacion'],
                ['header' => 'Teléfono', 'field' => 'telefono'],
            ],
            'data' => $colaboradores,
            'pagination' => [
                'currentPage' => $page,
                'perPage' => $perPage,
                'totalPages' => $totalPages,
                'totalRecords' => $totalRecords,
                'search' => $search,
                'sort' => $sort,
                'order' => $order,
                'filters' => [] // No hay filtros específicos para colaboradores por ahora
            ],
            'actions' => [
                'edit_route' => 'colaboradores&action=index',
                'delete_route' => 'colaboradores&action=destroy',
                // No hay ruta de imágenes para colaboradores, así que no la definimos.
            ]
        ];
        
        // 5. Renderiza la vista principal, pasándole la configuración de la tabla.
        $this->render('Views/colaboradores/index.php', [
            'pageTitle' => 'Gestionar Colaboradores',
            'formId' => 'form-colaborador',
            'tableConfig' => $tableConfig
        ]);
    }

    /**
     * Procesa el guardado de un nuevo colaborador.
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            (new Colaborador())->save($_POST);
            $_SESSION['mensaje_sa2'] = ['title' => '¡Éxito!', 'text' => 'Colaborador creado.', 'icon' => 'success'];
            header('Location: ' . BASE_URL . 'index.php?route=colaboradores');
            exit;
        }
    }

    /**
     * Procesa la actualización de un colaborador existente.
     */
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            (new Colaborador())->save($_POST);
            $_SESSION['mensaje_sa2'] = ['title' => '¡Éxito!', 'text' => 'Colaborador actualizado.', 'icon' => 'success'];
            header('Location: ' . BASE_URL . 'index.php?route=colaboradores');
            exit;
        }
    }

    /**
     * Procesa la eliminación de un colaborador.
     */
    public function destroy()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            (new Colaborador())->delete($_POST['id']);
            $_SESSION['mensaje_sa2'] = ['title' => '¡Eliminado!', 'text' => 'Colaborador eliminado.', 'icon' => 'success'];
            header('Location: ' . BASE_URL . 'index.php?route=colaboradores');
            exit;
        }
    }
}