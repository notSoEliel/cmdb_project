<?php

namespace App\Controllers;

use App\Models\Necesidad;

/**
 * Class NecesidadController
 * Gestiona la lógica para las solicitudes de necesidades.
 */
class NecesidadController extends BaseController
{
    // --- ACCIONES PARA COLABORADORES ---

    /**
     * Muestra una lista de las solicitudes hechas por el colaborador logueado.
     */
    public function misSolicitudes()
    {
        $necesidadModel = new Necesidad();
        $colaboradorId = $_SESSION['user_id'] ?? 0;

        // Filtramos para obtener solo las solicitudes del colaborador actual
        $solicitudes = $necesidadModel->findAll(['filters' => ['n.colaborador_id' => $colaboradorId]]);

        $this->render('Views/portal/mis_solicitudes.php', [
            'pageTitle' => 'Mis Solicitudes',
            'solicitudes' => $solicitudes
        ]);
    }

    /**
     * Muestra el formulario para crear una nueva solicitud.
     */
    public function showForm()
    {
        $this->render('Views/portal/crear_solicitud.php', [
            'pageTitle' => 'Crear Nueva Solicitud'
        ]);
    }

    /**
     * Guarda la nueva solicitud enviada por el colaborador.
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'colaborador_id' => $_SESSION['user_id'] ?? 0,
                'descripcion' => $_POST['descripcion'] ?? ''
            ];

            if (!empty($data['colaborador_id']) && !empty($data['descripcion'])) {
                (new Necesidad())->create($data);
                $_SESSION['mensaje_sa2'] = ['title' => '¡Enviada!', 'text' => 'Tu solicitud ha sido enviada.', 'icon' => 'success'];
            } else {
                $_SESSION['mensaje_sa2'] = ['title' => 'Error', 'text' => 'La descripción no puede estar vacía.', 'icon' => 'error'];
            }
            // Redirige a la lista de "mis solicitudes"
            header('Location: ' . BASE_URL . 'index.php?route=necesidades&action=misSolicitudes');
            exit;
        }
    }

    // --- ACCIONES PARA ADMINISTRADORES ---

    /**
     * Muestra la tabla de administración con TODAS las solicitudes.
     */
    public function adminIndex()
    {
        $necesidadModel = new Necesidad();
        // Recopila parámetros para la tabla dinámica (paginación, etc.)
        $options = $_GET;
        $page = (int)($_GET['page'] ?? 1);
        $perPage = (int)($_GET['perPage'] ?? 10);

        $data = $necesidadModel->findAll($options);
        $totalRecords = $necesidadModel->countFiltered($options);
        $totalPages = ceil($totalRecords / $perPage);

        // Prepara la configuración para la tabla reutilizable
        $tableConfig = [
            'columns' => [
                ['header' => 'ID', 'field' => 'id', 'sort_by' => 'n.id'],
                ['header' => 'Colaborador', 'field' => 'nombre_colaborador', 'sort_by' => 'nombre_colaborador'],
                ['header' => 'Descripción', 'field' => 'descripcion'],
                ['header' => 'Estado', 'field' => 'estado', 'sort_by' => 'n.estado'],
                ['header' => 'Fecha', 'field' => 'fecha_solicitud', 'sort_by' => 'n.fecha_solicitud'],
            ],
            'data' => $data,
            'pagination' => [
                'currentPage' => $page,
                'perPage' => $perPage,
                'totalPages' => $totalPages,
                'totalRecords' => $totalRecords,
                'search' => $_GET['search'] ?? '',
                'sort' => $_GET['sort'] ?? 'id',
                'order' => $_GET['order'] ?? 'asc',
                'filters' => []
            ],
            'actions' => [
                'route' => 'necesidades',
                'edit_action' => 'showUpdateForm',
                // No hay delete_action para las solicitudes
            ],
        ];

        $this->render('Views/admin/necesidades/index.php', [
            'pageTitle' => 'Gestionar Solicitudes',
            'tableConfig' => $tableConfig
        ]);
    }

    /**
     * Muestra el formulario para que un admin edite el estado de una solicitud.
     */
    public function showUpdateForm()
    {
        $necesidad_id = (int)($_GET['id'] ?? 0);
        $solicitud = (new Necesidad())->findById($necesidad_id);

        if (!$solicitud) {
            http_response_code(404);
            require_once '../src/Views/error-404.php';
            exit;
        }

        $this->render('Views/admin/necesidades/editar.php', [
            'pageTitle' => 'Gestionar Solicitud',
            'solicitud' => $solicitud
        ]);
    }

    /**
     * Procesa la actualización del estado de una solicitud por parte de un admin.
     */
    public function updateStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $necesidad_id = (int)($_POST['necesidad_id'] ?? 0);
            $newState = $_POST['estado'] ?? 'Solicitado';

            (new Necesidad())->updateStatus($necesidad_id, $newState);
            $_SESSION['mensaje_sa2'] = ['title' => '¡Actualizado!', 'text' => 'El estado de la solicitud ha cambiado.', 'icon' => 'success'];

            header('Location: ' . BASE_URL . 'index.php?route=necesidades&action=adminIndex');
            exit;
        }
    }
}
