<?php
session_start();

require_once '../vendor/autoload.php';
require_once '../config/database.php';
require_once '../config/app.php'; //


use App\Controllers\CategoriaController;
use App\Controllers\ColaboradorController;
use App\Controllers\InventarioController;
// Agregaremos más controladores aquí en el futuro

// --- Router Básico ---
$route = $_GET['route'] ?? 'home';
$action = $_GET['action'] ?? 'index';

switch ($route) {
    case 'categorias':
        $controller = new CategoriaController();
        if (method_exists($controller, $action)) {
            $controller->$action();
        } else {
            echo "Error: Acción no encontrada.";
        }
        break;
    case 'colaboradores':
        $controller = new ColaboradorController();
        if (method_exists($controller, $action)) {
            $controller->$action();
        } else {
            echo "Error: Acción no encontrada.";
        }
        break;
    case 'inventario': // <-- Nuevo case
        $controller = new InventarioController();
        if (method_exists($controller, $action)) {
            $controller->$action();
        } else {
            echo "Error: Acción no encontrada.";
        }
        break;

    // Aquí irán las rutas para 'colaboradores', 'inventario', etc.

    default:
        // --- Página de inicio por defecto ---
        $pageTitle = "Menú Principal - CMDB";
        ob_start(); // Inicia el buffer de salida para capturar el HTML
?>
        <div class="px-4 py-5 my-5 text-center">
            <h1 class="display-5 fw-bold text-body-emphasis">Sistema CMDB</h1>
            <div class="col-lg-6 mx-auto">
                <p class="lead mb-4">Bienvenido al sistema de gestión de configuración. Por favor, selecciona un módulo para empezar a trabajar.</p>
                <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                    <a href="?route=inventario" class="btn btn-primary btn-lg px-4 gap-3">Gestionar Inventario</a>
                    <a href="?route=colaboradores" class="btn btn-outline-secondary btn-lg px-4 gap-3">Gestionar Colaboradores</a>
                    <a href="?route=categorias" class="btn btn-outline-secondary btn-lg px-4">Gestionar Categorías</a>
                </div>
            </div>
        </div>
<?php
        $content = ob_get_clean(); // Obtiene el contenido del buffer y lo limpia
        require '../src/Views/layout.php'; // Carga el layout principal
        break;
}
