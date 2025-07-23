<?php

namespace App\Core;

/**
 * Clase ValidationService
 *
 * Se encarga de cargar las reglas de validación de jQuery desde un archivo de configuración
 * y de generar el script JavaScript necesario para inicializar jQuery Validate.
 */
class ValidationService
{
    private $config = [];

    public function __construct()
    {
        // Carga las reglas de validación desde el archivo de configuración.
        $this->config = require '../config/validation_rules_jquery.php';
    }

    /**
     * Genera un script JavaScript que define una variable global 'validationRules'
     * con todas las reglas y mensajes de validación cargados.
     * Esta variable será usada por 'app.js' para inicializar jQuery Validate.
     *
     * @param array $formIds Los IDs de los formularios que necesitan validación.
     * @return string El bloque <script> que define la variable global.
     */
    public function generateJQueryValidateScript(array $formIds): string
    {
        // 1. Filtramos el gran array de reglas para quedarnos solo con las que necesitamos.
        $rulesForPage = array_filter(
            $this->config,
            fn($key) => in_array($key, $formIds),
            ARRAY_FILTER_USE_KEY
        );

        // Si no hay reglas para los formularios de esta página, no hacemos nada.
        if (empty($rulesForPage)) {
            return '';
        }
        // 2. Convertimos a JSON solo el subconjunto de reglas necesarias.
        $rulesJson = json_encode($rulesForPage);

        // 3. Generamos el script final.
        // La responsabilidad de inicializar .validate() se mueve a app.js
        return "<script>var validationRules = {$rulesJson};</script>";
    }
}
