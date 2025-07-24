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
    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'id' => $_POST['id'] ?? null,
                'colaborador_id' => $_SESSION['user_id'] ?? 0,
                'descripcion' => $_POST['descripcion'] ?? ''
            ];

            if (!empty($data['colaborador_id']) && !empty($data['descripcion'])) {
                (new Necesidad())->save($data);
                $_SESSION['mensaje_sa2'] = ['title' => '¡Éxito!', 'text' => 'Tu solicitud ha sido guardada.', 'icon' => 'success'];
            }
            header('Location: ' . BASE_URL . 'index.php?route=necesidades&action=misSolicitudes');
            exit;
        }
    }

    /**
     * Muestra el formulario para que el colaborador edite su solicitud.
     */
    public function showEditForm()
    {
        $necesidadModel = new Necesidad();
        $id = (int)($_GET['id'] ?? 0);
        $solicitud = $necesidadModel->findById($id);

        // Seguridad: Asegurarse de que el colaborador solo pueda editar sus propias solicitudes
        if (!$solicitud || $solicitud['colaborador_id'] != $_SESSION['user_id']) {
            http_response_code(403);
            require_once '../src/Views/error-403.php';
            exit;
        }

        $this->render('Views/portal/editar_solicitud.php', [
            'pageTitle' => 'Editar Solicitud',
            'solicitud' => $solicitud
        ]);
    }

    /**
     * Permite al colaborador eliminar su solicitud.
     */
    public function destroy()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            $necesidadModel = new Necesidad();
            $solicitud = $necesidadModel->findById($id);

            if ($solicitud && $solicitud['colaborador_id'] == $_SESSION['user_id']) {
                $necesidadModel->delete($id);
                $_SESSION['mensaje_sa2'] = ['title' => '¡Eliminada!', 'text' => 'Tu solicitud ha sido eliminada.', 'icon' => 'success'];
            }
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
        $options = $_GET;
        $page = (int)($_GET['page'] ?? 1);
        $perPage = (int)($_GET['perPage'] ?? 10);

        // Filtro por defecto: mostrar solo las solicitudes "abiertas"
        $estadoFiltro = $_GET['estado'] ?? 'abiertas';

        if ($estadoFiltro === 'abiertas') {
            $options['filters']['n.estado'] = ['IN', 'Solicitado', 'Aprobado'];
        } elseif ($estadoFiltro !== 'todos') {
            $options['filters']['n.estado'] = $estadoFiltro;
        }

        // Orden por defecto: las más antiguas primero (FIFO)
        $options['sort'] = $_GET['sort'] ?? 'n.fecha_solicitud';
        $options['order'] = $_GET['order'] ?? 'asc';

        $data = $necesidadModel->findAll($options);
        $totalRecords = $necesidadModel->countFiltered($options);
        $totalPages = ceil($totalRecords / $perPage);

        $tableConfig = [
            'columns' => [
                ['header' => 'ID', 'field' => 'id', 'sort_by' => 'n.id'],
                ['header' => 'Colaborador', 'field' => 'nombre_colaborador', 'sort_by' => 'nombre_colaborador'],
                ['header' => 'Descripción', 'field' => 'descripcion'],
                ['header' => 'Estado', 'field' => 'estado', 'sort_by' => 'n.estado'],
                ['header' => 'Fecha Solicitud', 'field' => 'fecha_solicitud', 'sort_by' => 'n.fecha_solicitud'],
                ['header' => 'Fecha Resolución', 'field' => 'fecha_resolucion'], // Nueva columna
            ],
            'data' => $data,
            // Se completa la configuración de la paginación con todas las claves necesarias.
            'pagination' => [
                'currentPage' => $page,
                'perPage' => $perPage,
                'totalPages' => $totalPages,
                'totalRecords' => $totalRecords,
                'search' => $_GET['search'] ?? '',
                'sort' => $_GET['sort'] ?? 'n.fecha_solicitud',
                'order' => $_GET['order'] ?? 'asc',
                'filters' => $options['filters'] ?? []
            ],
            'actions' => ['edit_action' => 'showUpdateForm'],
            'dropdown_filters' => [
                'estado' => [
                    'label' => 'Estado',
                    'name' => 'estado',
                    'options' => [
                        ['id' => 'abiertas', 'nombre' => 'Abiertas (Por Defecto)'],
                        ['id' => 'todos', 'nombre' => 'Ver Todos'],
                        ['id' => 'Solicitado', 'nombre' => 'Solicitado'],
                        ['id' => 'Aprobado', 'nombre' => 'Aprobado'],
                        ['id' => 'Rechazado', 'nombre' => 'Rechazado'],
                        ['id' => 'Completado', 'nombre' => 'Completado'],
                    ]
                ]
            ]
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
