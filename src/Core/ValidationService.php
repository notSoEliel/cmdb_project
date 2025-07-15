<?php

namespace App\Core;

class ValidationService
{

    private $config = [];

    public function __construct()
    {
        $this->config = require '../config/validation_rules_jquery.php';
    }

    public function generateJQueryValidateScript(string $formId): string
    {
        if (!isset($this->config[$formId])) {
            return '';
        }

        $formConfig = $this->config[$formId];
        $rulesJson = json_encode($formConfig['rules']);
        $messagesJson = json_encode($formConfig['messages']);

        // Esta configuración es más simple y se integra mejor con Bootstrap 5
        $script = "<script>\n";
        $script .= "$(document).ready(function() {\n";

        // --- Reglas Personalizadas ---
        $script .= "    $.validator.addMethod('phonePA', function(value, element) { return this.optional(element) || /^\\d{3,4}-\\d{4}$/.test(value); }, 'Formato: XXXX-XXXX.');\n";

        // --- Inicialización del Plugin ---
        $script .= "    $('#$formId').validate({\n";
        $script .= "        rules: $rulesJson,\n";
        $script .= "        messages: $messagesJson,\n";
        $script .= "        errorElement: 'div',\n";
        $script .= "        errorClass: 'invalid-feedback',\n"; // Clase de Bootstrap para errores
        $script .= "        errorPlacement: function(error, element) {\n";
        $script .= "            if (element.parent().hasClass('input-group')) {\n";
        $script .= "                error.insertAfter(element.parent());\n"; // Si está en un input-group, pon el error después del grupo
        $script .= "            } else {\n";
        $script .= "                error.insertAfter(element);\n"; // Comportamiento normal para otros campos
        $script .= "            }\n";
        $script .= "        },\n";
        $script .= "        highlight: function(element) {\n";
        $script .= "            $(element).addClass('is-invalid');\n"; // Clase de Bootstrap para resaltar el campo
        $script .= "        },\n";
        $script .= "        unhighlight: function(element) {\n";
        $script .= "            $(element).removeClass('is-invalid');\n";
        $script .= "        }\n";
        $script .= "    });\n";

        // Aquí va la lógica condicional para la contraseña si es el form de colaborador
        if ($formId === 'form-colaborador') {
            $script .= "    if ($('input[name=\"id\"]').val() !== '') {\n";
            $script .= "        $('input[name=\"password\"]').rules('remove', 'required');\n";
            $script .= "    } else {\n";
            $script .= "        $('input[name=\"password\"]').rules('add', { required: true, messages: { required: 'La contraseña es obligatoria al crear.' } });\n";
            $script .= "    }\n";
        }

        $script .= "});\n";
        $script .= "</script>";

        return $script;
    }
}
