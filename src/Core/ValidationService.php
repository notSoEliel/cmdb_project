<?php

namespace App\Core;

class ValidationService
{
    private $config = [];

    public function __construct()
    {
        $this->config = require '../config/validation_rules_jquery.php';
    }

    /**
     * Genera el script de jQuery Validate para uno o más formularios.
     * @param array $formIds Un array con los IDs de los formularios a validar.
     * @return string El bloque <script> completo.
     */
    public function generateJQueryValidateScript(array $formIds): string
    {
        $finalScript = ''; // Inicia un string para los scripts individuales

        // Itera sobre cada ID de formulario que nos pasaron
        foreach ($formIds as $formId) {
            // Si no hay reglas para este formId, lo salta y continúa con el siguiente
            if (!isset($this->config[$formId])) {
                continue;
            }

            $formConfig = $this->config[$formId];
            $rulesJson = json_encode($formConfig['rules']);
            $messagesJson = json_encode($formConfig['messages']);

            // Genera el bloque .validate() para este formulario específico
            $script = "\n";
            $script .= "    $('#$formId').validate({\n";
            $script .= "        rules: $rulesJson,\n";
            $script .= "        messages: $messagesJson,\n";
            $script .= "        errorElement: 'div',\n";
            $script .= "        errorClass: 'text-danger form-text',\n";
            $script .= "        errorPlacement: function(error, element) {\n";
            $script .= "            if (element.parent().hasClass('input-group')) {\n";
            $script .= "                error.insertAfter(element.parent());\n";
            $script .= "            } else {\n";
            $script .= "                error.insertAfter(element);\n";
            $script .= "            }\n";
            $script .= "        },\n";
            $script .= "        highlight: function(element) { $(element).addClass('is-invalid'); },\n";
            $script .= "        unhighlight: function(element) { $(element).removeClass('is-invalid'); }\n";
            $script .= "    });\n";

            // Lógica condicional para la contraseña en el form de colaborador
            // Se aplica la lógica condicional de la contraseña a ambos formularios
            if ($formId === 'form-colaborador' || $formId === 'form-usuario') {
                // Si el campo 'id' tiene un valor (estamos editando), la contraseña no es obligatoria.
                if (!empty($_GET['editar_id'])) {
                    $script .= "    $('input[name=\"password\"]').rules('remove', 'required');\n";
                } else {
                    // Si no (estamos creando), la contraseña SÍ es obligatoria.
                    $script .= "    $('input[name=\"password\"]').rules('add', { required: true, messages: { required: 'La contraseña es obligatoria al crear.' } });\n";
                }
            }

            // Lógica condicional para la contraseña en el form de perfil
            if ($formId === 'form-password' && !empty($_SESSION['user_id'])) {
                // No es necesario añadir la regla 'required' aquí porque ya está en la config.
                // Esta lógica sería para casos más complejos.
            }

            $finalScript .= $script; // Añade el script de este formulario al total
        }

        // Si se generó algún script, lo envuelve en las etiquetas necesarias.
        if (!empty($finalScript)) {
            $fullScript = "<script>\n$(document).ready(function() {\n";
            // Se añaden todas las reglas personalizadas aquí
            $fullScript .= "    $.validator.addMethod('phonePA', function(value, element) { let cleanValue = value.replace(/-/g, ''); return this.optional(element) || /^(6\\d{7}|[2-9]\\d{6})$/.test(cleanValue); }, 'Debe ser un número válido de Panamá.');\n";
            $fullScript .= "    $.validator.addMethod('ipv4', function(value, element) { return this.optional(element) || /^((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\\.){3}(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/.test(value); }, 'Por favor, introduce una dirección IP v4 válida.');\n";
            // Fin de las reglas personalizadas
            $fullScript .= $finalScript;
            $fullScript .= "});\n</script>";
            return $fullScript;
        }

        return ''; // Devuelve vacío si no había formularios que validar
    }
}
