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
     * @return string El bloque <script> que define la variable global.
     */
    public function generateJQueryValidateScript(): string
    {
        // Codifica todo el array de configuración de reglas como un objeto JSON JavaScript.
        $rulesJson = json_encode($this->config);

        // Envuelve el JSON en una etiqueta <script> y define la variable global.
        $script = "<script>\n";
        $script .= "    var validationRules = $rulesJson;\n"; // Define la variable global
        $script .= "</script>";

        return $script;
    }
}
