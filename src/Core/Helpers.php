<?php
// src/Core/helpers.php

/**
 * Maneja una excepción, loguea el error y muestra la página de error apropiada.
 * @param Throwable $e
 */
function handleException(Throwable $e): void
{
    error_log($e->getFile() . ':' . $e->getLine() . ' - ' . $e->getMessage());
    if (ob_get_level()) ob_end_clean();

    http_response_code(500);
    // Ahora pasamos el mensaje de la excepción a la vista de error.
    $errorMessage = $e->getMessage();
    require_once __DIR__ . '/../Views/error.php';
    exit;
}