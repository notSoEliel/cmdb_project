<?php

namespace App\Controllers;

use App\Core\AuthService;
use App\Core\ValidationService;

/**
 * Controlador Base
 * Contiene la lógica compartida, como renderizar vistas y proteger rutas.
 */
abstract class BaseController
{
    public function __construct()
    {
        // --- INICIO DEL GUARDIÁN DE RUTAS ---
        $authService = new AuthService();
        $route = $_GET['route'] ?? 'home';

        // Definimos las rutas que no necesitan login
        $publicRoutes = ['login', 'logout'];
        if (in_array($route, $publicRoutes)) {
            return; // Si la ruta es pública, no se hace ninguna comprobación.
        }

        // Definimos qué rutas requieren qué rol.
        $protectedRoutes = [
            // Rutas exclusivas para 'admin'
            'home'          => ['role' => 'admin'],
            'inventario'    => ['role' => 'admin'],
            'colaboradores' => ['role' => 'admin'],
            'categorias'    => ['role' => 'admin'],
            'usuarios'      => ['role' => 'admin'],

            // Rutas exclusivas para 'colaborador'
            'portal'        => ['role' => 'colaborador'],

            // Ruta con acciones para roles mixtos
            'necesidades'   => [
                'actions' => [
                    'adminIndex'      => 'admin',
                    'showUpdateForm'  => 'admin',
                    'updateStatus'    => 'admin',
                    'misSolicitudes'  => 'colaborador',
                    'showForm'        => 'colaborador',
                    'store'           => 'colaborador',
                ]
            ]
        ];

        // 1. Revisa si la ruta actual está protegida para algún rol.
        $requiredRole = null;
        foreach ($protectedRoutes as $role => $routes) {
            if (in_array($route, $routes)) {
                $requiredRole = $role;
                break;
            }
        }

        // 2. Si la ruta requiere un rol...
        if ($requiredRole) {
            // ...primero, verifica si el usuario ha iniciado sesión.
            if (!$authService->isLoggedIn()) {
                header('Location: ' . BASE_URL . 'index.php?route=login');
                exit;
            }

            // ...segundo, verifica si el rol del usuario es el correcto.
            if ($authService->getRole() !== $requiredRole) {
                http_response_code(403);
                require_once '../src/Views/error-403.php';
                exit;
            }
        }
        // --- FIN DEL GUARDIÁN DE RUTAS ---
    }

    /**
     * Renderiza una vista. Por defecto, la envuelve en el layout principal.
     * @param string $view La ruta a la vista.
     * @param array $data Los datos para la vista.
     * @param bool $useLayout Si es false, renderiza la vista sin el layout.
     */
    protected function render(string $view, array $data = [], bool $useLayout = true)
    {
        // Si el controlador le pasa un array de 'formIds'...
        if (isset($data['formIds']) && is_array($data['formIds'])) {
            $validationService = new ValidationService();
            // ...le pide al ValidationService que genere los scripts.
            $data['validationScript'] = $validationService->generateJQueryValidateScript($data['formIds']);
        }

        extract($data);


        if ($useLayout) {
            // Si se usa el layout, captura el contenido de la vista en una variable
            ob_start();
            require_once "../src/{$view}";
            $content = ob_get_clean();
            // y luego carga el layout, que usará la variable $content.
            require_once '../src/Views/layout.php';
        } else {
            // Si no se usa el layout, simplemente carga el archivo de la vista directamente.
            require_once "../src/{$view}";
        }
    }
}
