<?php
session_start();

require_once '../vendor/autoload.php';
require_once '../config/database.php';
require_once '../config/app.php';

use App\Controllers\CategoriaController;
use App\Controllers\ColaboradorController;
use App\Controllers\InventarioController;
use App\Controllers\DashboardController;

// --- Router Básico ---
$route = $_GET['route'] ?? 'home';
$action = $_GET['action'] ?? 'index';

// El switch solo se encarga de crear el controlador y llamar a la acción.
switch ($route) {
    case 'categorias':
        $controller = new CategoriaController();
        break;
    case 'colaboradores':
        $controller = new ColaboradorController();
        break;
    case 'inventario':
        $controller = new InventarioController();
        break;
    case 'home':
    default:
        $controller = new DashboardController();
        break;
}

// Se llama a la acción correspondiente en el controlador seleccionado.
if (method_exists($controller, $action)) {
    $controller->$action();
} else {
    // Manejo de error si la acción no existe en el controlador.
    echo "Error 404: Acción no encontrada.";
}