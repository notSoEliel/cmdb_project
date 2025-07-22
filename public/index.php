<?php

// =================================================================
// 1. INICIALIZACIÓN Y MANEJO DE ERRORES
// =================================================================
session_start();
ini_set('display_errors', 0); // Oculta errores de PHP al usuario final
error_reporting(E_ALL);

// Carga de archivos de configuración y helpers
require_once '../vendor/autoload.php';
require_once '../config/database.php';
require_once '../config/app.php';
require_once '../src/Core/helpers.php'; // Nuestro helper con la función handleException()

// Convierte todos los errores de PHP (warnings, notices) en Excepciones
// para que nuestro bloque try/catch los pueda atrapar.
set_error_handler(function ($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});


// =================================================================
// 2. DEFINICIÓN DE RUTAS Y CONTROLADORES
// =================================================================
use App\Controllers\AuthController;
use App\Controllers\CategoriaController;
use App\Controllers\ColaboradorController;
use App\Controllers\InventarioController;
use App\Controllers\DashboardController;
use App\Controllers\NecesidadController;
use App\Controllers\PortalController;
use App\Controllers\UsuarioController;

// Mapa de rutas válidas de la aplicación
$validRoutes = [
    'login'         => AuthController::class,
    'logout'        => AuthController::class,
    'portal'        => PortalController::class,
    'necesidades'   => NecesidadController::class,
    'usuarios'      => UsuarioController::class,
    'home'          => DashboardController::class,
    'inventario'    => InventarioController::class,
    'colaboradores' => ColaboradorController::class,
    'categorias'    => CategoriaController::class,
];


// =================================================================
// 3. EJECUCIÓN PRINCIPAL Y MANEJO DE EXCEPCIONES
// =================================================================
try {
    $route = $_GET['route'] ?? 'home';
    $action = $_GET['action'] ?? 'index';

    // Si la ruta no está en nuestro mapa, es un 404
    if (!isset($validRoutes[$route])) {
        throw new Exception("Ruta no encontrada: {$route}", 404);
    }

    $controllerClass = $validRoutes[$route];
    $controller = new $controllerClass();

    // Si la acción no existe en el controlador, es un 404
    if (!method_exists($controller, $action)) {
        throw new Exception("Acción no encontrada: {$action} en la ruta {$route}", 404);
    }

    // Si todo está bien, ejecuta la acción
    $controller->$action();
} catch (Throwable $e) {
    // Si cualquier cosa dentro del bloque 'try' falla, atrapamos el error aquí.

    // Si el código de error es 404, mostramos nuestra página 404.
    if ($e->getCode() === 404) {
        http_response_code(404);
        require_once __DIR__ . '/../src/Views/error-404.php';
    } else {
        // Para cualquier otro error (500, de base de datos, etc.),
        // llamamos a nuestra función de ayuda centralizada.
        handleException($e);
    }
    exit;
}
