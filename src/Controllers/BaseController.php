<?php

namespace App\Controllers;

use App\Core\ValidationService;

/**
 * Controlador Base
 * Proporciona métodos comunes para otros controladores, como renderizar vistas.
 */
abstract class BaseController
{

    /**
     * Renderiza una vista dentro del layout principal.
     *
     * @param string $view La ruta al archivo de la vista desde la carpeta 'src'.
     * @param array $data Los datos que se pasarán a la vista.
     */
    protected function render(string $view, array $data = [])
    {
        if (isset($data['formId'])) {
            $validationService = new ValidationService();
            $data['validationScript'] = $validationService->generateJQueryValidateScript($data['formId']);
        }

        // Convierte las claves del array en variables (ej: $data['categorias'] se convierte en $categorias)
        extract($data);

        // Inicia el almacenamiento en búfer de salida. El HTML no se envía directamente.
        ob_start();

        // Carga el contenido de la vista específica (ej: inventario/index.php) en el búfer
        require_once "../src/{$view}";

        // Obtiene el contenido del búfer y lo limpia. Ahora $content tiene todo el HTML de la vista.
        $content = ob_get_clean();

        // Carga el layout principal, que usará la variable $content.
        require_once '../src/Views/layout.php';
    }
}
