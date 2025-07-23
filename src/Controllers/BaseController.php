<?php

namespace App\Controllers;

use App\Core\AuthService;
use App\Core\ValidationService;

/**
 * Controlador Base
 * Contiene la lógica compartida, como renderizar vistas y, más importante,
 * proteger todas las rutas de la aplicación.
 */
abstract class BaseController
{
    /**
     * El constructor actúa como un "guardián" de seguridad para cada página.
     * Se ejecuta antes que cualquier método de los controladores hijos.
     */
    public function __construct()
    {
        $authService = new AuthService();
        $route = $_GET['route'] ?? 'home';
        $action = $_GET['action'] ?? 'index';

        // 1. Definimos las rutas que son 100% públicas y no requieren login.
        $publicRoutes = ['login', 'logout', 'forgot-password', 'public', 'reset-password'];
        if (in_array($route, $publicRoutes)) {
            return; // Si la ruta es pública, la ejecución continúa sin más comprobaciones.
        }

        // 2. Si la ruta no es pública, es obligatorio que el usuario haya iniciado sesión.
        if (!$authService->isLoggedIn()) {
            // Si no ha iniciado sesión, se le redirige al login.
            header('Location: ' . BASE_URL . 'index.php?route=login');
            exit;
        }

        // 3. Definimos un mapa completo de qué roles pueden acceder a qué rutas y acciones.
        $permissions = [
            'admin' => [
                'home' => ['index'],
                'inventario' => [
                    'index',
                    'store',
                    'update',
                    'destroy',
                    'showAssignForm',
                    'assign',
                    'unassign',
                    'showImages',
                    'uploadImage',
                    'destroyImage',
                    'setThumbnail',
                    'exportToExcel',
                    'showAddForm',
                    'batchStore',
                    'checkSerialUniqueness',
                    'showQrCode',
                    'showDonados',
                ],
                'colaboradores' => ['index', 'store', 'update'],
                'categorias' => ['index', 'store', 'update', 'destroy'],
                'usuarios' => ['index', 'store', 'update'],
                'admin_profile' => ['index', 'updatePassword'],
                'necesidades' => ['adminIndex', 'showUpdateForm', 'updateStatus'],
            ],
            'colaborador' => [
                'portal' => ['index', 'misEquipos', 'showProfile', 'updateLocation', 'updatePassword', 'showEquipoImages'],
                'necesidades' => ['misSolicitudes', 'showForm', 'store'],
            ]
        ];

        // 4. Verificamos si el rol del usuario actual tiene permiso para la ruta y acción solicitadas.
        $userRole = $authService->getRole();
        $hasPermission = false;

        if (isset($permissions[$userRole])) {
            // Revisa si el rol tiene permiso para toda la ruta (todas las acciones)
            if (isset($permissions[$userRole][$route]) && !is_array($permissions[$userRole][$route])) {
                // Esta lógica es por si en el futuro queremos dar acceso a un controlador entero
                // (no la usamos ahora, pero es bueno tenerla)
            }
            // O revisa si tiene permiso para la acción específica
            elseif (isset($permissions[$userRole][$route]) && in_array($action, $permissions[$userRole][$route])) {
                $hasPermission = true;
            }
        }

        // 5. Si no tiene permiso, se muestra la página de error 403.
        if (!$hasPermission) {
            http_response_code(403); // 403 Forbidden
            require_once '../src/Views/error-403.php';
            exit;
        }
    }

    /**
     * Renderiza una vista. Es capaz de generar scripts de validación
     * si se le pasa 'formId' (string) o 'formIds' (array).
     */
    protected function render(string $view, array $data = [], bool $useLayout = true)
    {
        $idsParaValidar = [];
        if (isset($data['formId'])) {
            $idsParaValidar[] = $data['formId'];
        }
        if (isset($data['formIds'])) {
            $idsParaValidar = array_merge($idsParaValidar, $data['formIds']);
        }

        if (!empty($idsParaValidar)) {
            $validationService = new ValidationService();
            $data['validationScript'] = $validationService->generateJQueryValidateScript($idsParaValidar);
        }

        extract($data);

        if ($useLayout) {
            ob_start();
            require_once "../src/{$view}";
            $content = ob_get_clean();
            require_once '../src/Views/layout.php';
        } else {
            require_once "../src/{$view}";
        }
    }
}
