<?php
// =================================================================
// MANEJADOR DE ERRORES GLOBAL
// =================================================================
ini_set('display_errors', 0); // Siempre 0 en producción.
error_reporting(E_ALL);

/**
 * Función central para manejar cualquier error y mostrar la página de error.
 * Ahora incluye la lógica para detectar errores de duplicados.
 * @param Throwable $e El objeto del error o excepción.
 */
function globalErrorHandler(Throwable $e): void
{
    error_log($e->getFile() . ':' . $e->getLine() . ' - ' . $e->getMessage());

    if (ob_get_level()) ob_end_clean();

    // Lógica para detectar el código de error SQLSTATE
    $sqlState = '';
    if ($e instanceof \PDOException) {
        $sqlState = $e->errorInfo[0] ?? $e->getCode();
    } elseif ($e->getPrevious() instanceof \PDOException) {
        $sqlState = $e->getPrevious()->errorInfo[0] ?? $e->getPrevious()->getCode();
    }

    // Si el error es de tipo 'Integrity constraint violation' (duplicados, etc.)
    if ($sqlState === '23000') {
        http_response_code(409); // 409 Conflict
        // Usamos la ruta corregida a la vista
        require_once __DIR__ . '/../src/Views/error-duplicate.php';
    } else {
        // Para cualquier otro error, mostramos la página genérica
        http_response_code(500);
        $errorMessage = "Ocurrió un error inesperado.";
        // Usamos la ruta corregida a la vista
        require_once __DIR__ . '/../src/Views/error.php';
    }
    exit;
}

// 1. Se registra el manejador de excepciones ANTES de cualquier otra cosa.
set_exception_handler('globalErrorHandler');

// 2. Se convierte los errores de PHP en Excepciones para que sean atrapados.
set_error_handler(function ($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

// =================================================================
// INICIALIZACIÓN DE LA APLICACIÓN
// =================================================================
session_start();

// Se eliminó la conexión a la BD de aquí. Confiamos en nuestra clase Database.
require_once '../vendor/autoload.php';
require_once '../config/database.php';
require_once '../config/app.php';
require_once '../src/Core/helpers.php';

use App\Controllers\CategoriaController;
use App\Controllers\ColaboradorController;
use App\Controllers\InventarioController;
use App\Controllers\DashboardController;

// =================================================================
// EJECUCIÓN PRINCIPAL DENTRO DE UN BLOQUE TRY/CATCH
// =================================================================
// 1. Definimos un mapa de rutas válidas y sus controladores.
$validRoutes = [
    'home' => DashboardController::class,
    'inventario' => InventarioController::class,
    'colaboradores' => ColaboradorController::class,
    'categorias' => CategoriaController::class,
];

// 2. Obtenemos la ruta y la acción de la URL.
$route = $_GET['route'] ?? 'home';
$action = $_GET['action'] ?? 'index';

try {
    // 3. Verificamos si la ruta solicitada existe en nuestro mapa.
    if (isset($validRoutes[$route])) {
        $controllerClass = $validRoutes[$route];
        $controller = new $controllerClass();

        // Verificamos si el método (acción) existe en el controlador.
        if (method_exists($controller, $action)) {
            $controller->$action();
        } else {
            // Si la acción no existe, lanzamos un error 404.
            throw new Exception("Acción no encontrada: {$action}", 404);
        }
    } else {
        // Si la ruta no existe, lanzamos un error 404.
        throw new Exception("Ruta no encontrada: {$route}", 404);
    }
} catch (Throwable $e) {
    // Modificamos nuestro manejador para que sepa qué hacer con un error 404.
    if ($e->getCode() === 404) {
        http_response_code(404);
        require_once __DIR__ . '/../src/Views/error-404.php';
        exit;
    }
    // Para cualquier otro error, usamos el manejador global.
    globalErrorHandler($e);
}
