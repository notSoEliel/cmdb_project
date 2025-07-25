<?php

namespace App\Controllers;

use App\Models\Inventario;

// Nota: Este controlador NO extiende de BaseController para evitar la protección de login.
class PublicController
{
    public function showEquipo()
    {
        $id = (int)($_GET['id'] ?? 0);
        $equipo = (new Inventario())->findById($id);

        if (!$equipo) {
            // Si no se encuentra, podemos mostrar un 404 simple.
            http_response_code(404);
            include '../src/Views/error-404.php';
            exit;
        }

        // Carga una vista simple, sin el layout del dashboard.
        require_once '../src/Views/public/details.php';
    }

    /**
     * Muestra la página de mercadeo/noticias.
     */
    public function showMarketingPage()
    {
        require_once '../src/Views/public/marketing.php';
    }
}
