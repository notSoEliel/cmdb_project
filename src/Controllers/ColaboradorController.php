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
     * Muestra la página principal de Colaboradores con la tabla dinámica completa.
     */
    public function index()
    {
        // 1. Recopilación de parámetros (sin cambios)
        $page = (int)($_GET['page'] ?? 1);
        $perPage = (int)($_GET['perPage'] ?? 10);
        $search = $_GET['search'] ?? '';
        $sort = $_GET['sort'] ?? 'co.nombre';
        $order = $_GET['order'] ?? 'asc';

        // 2. Preparación del modelo y opciones (sin cambios)
        $colaboradorModel = new Colaborador();
        $options = [
            'page' => $page,
            'perPage' => $perPage,
            'sort' => $sort,
            'order' => $order,
            'search' => $search,
        ];

        // 3. Obtención de datos (sin cambios)
        $colaboradores = $colaboradorModel->findAll($options);
        $totalRecords = $colaboradorModel->countFiltered($options);
        $totalPages = ceil($totalRecords / $perPage);

        // 4. Configuración para la Tabla Reutilizable
        $tableConfig = [
            'columns' => [
                ['header' => 'ID', 'field' => 'id', 'sort_by' => 'co.id'],
                ['header' => 'Nombre', 'field' => 'nombre', 'sort_by' => 'co.nombre'],
                ['header' => 'Apellido', 'field' => 'apellido', 'sort_by' => 'co.apellido'],
                ['header' => 'ID Único', 'field' => 'identificacion_unica'], // No ordenable en este ejemplo
                ['header' => 'Email', 'field' => 'email', 'sort_by' => 'co.email'],
                ['header' => 'Ubicación', 'field' => 'ubicacion'], // No ordenable
                ['header' => 'Teléfono', 'field' => 'telefono'],   // No ordenable
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
                'filters' => []
            ],
            'actions' => [
                'route' => 'colaboradores',
                'edit_action' => 'index',
                'delete_action' => 'destroy',
            ],
        ];

        // 5. Renderizado de la Vista (sin cambios)
        $this->render('Views/colaboradores/index.php', [
            'pageTitle' => 'Gestionar Colaboradores',
            'formId' => ['form-colaborador'],
            'tableConfig' => $tableConfig
        ]);
    }

    /**
     * Procesa el guardado de un nuevo colaborador.
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                (new Colaborador())->save($_POST, $_FILES);
                $_SESSION['mensaje_sa2'] = ['title' => '¡Éxito!', 'text' => 'Colaborador creado.', 'icon' => 'success'];
            } catch (\Throwable $e) {
                handleException($e); // <-- LLAMAMOS A NUESTRO HELPER
            }
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
            try {
                (new Colaborador())->save($_POST, $_FILES);
                $_SESSION['mensaje_sa2'] = ['title' => '¡Éxito!', 'text' => 'Colaborador actualizado.', 'icon' => 'success'];
            } catch (\Throwable $e) {
                handleException($e); // <-- LLAMAMOS A NUESTRO HELPER
            }
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
