// Espera a que todo el contenido de la página (DOM) esté cargado antes de ejecutar cualquier script.
document.addEventListener('DOMContentLoaded', function() {

    // --- Lógica para el modo de edición de UBICACIÓN en el perfil ---
    const viewLocationDiv = document.getElementById('view-location-div');
    const editLocationDiv = document.getElementById('edit-location-div');
    const btnChangeLocation = document.getElementById('btn-change-location');
    const btnCancelLocation = document.getElementById('btn-cancel-location');

    if (viewLocationDiv && editLocationDiv && btnChangeLocation && btnCancelLocation) {
        btnChangeLocation.addEventListener('click', function() {
            viewLocationDiv.style.display = 'none';
            editLocationDiv.style.display = 'block';
        });
        btnCancelLocation.addEventListener('click', function() {
            editLocationDiv.style.display = 'none';
            viewLocationDiv.style.display = 'flex';
        });
    }

    // --- Lógica para el modo de edición de CONTRASEÑA en el perfil ---
    const viewPasswordDiv = document.getElementById('view-password-div');
    const editPasswordDiv = document.getElementById('edit-password-div');
    const btnChangePassword = document.getElementById('btn-change-password');
    const btnCancelPassword = document.getElementById('btn-cancel-password');

    if (viewPasswordDiv && editPasswordDiv && btnChangePassword && btnCancelPassword) {
        btnChangePassword.addEventListener('click', function() {
            viewPasswordDiv.style.display = 'none';
            editPasswordDiv.style.display = 'block';
        });
        btnCancelPassword.addEventListener('click', function() {
            editPasswordDiv.style.display = 'none';
            viewPasswordDiv.style.display = 'flex';
        });
    }

    // --- Lógica REUTILIZABLE para los botones de "ojo" de las contraseñas ---
    function setupPasswordToggle(inputId, buttonId) {
        const passwordInput = document.getElementById(inputId);
        const toggleButton = document.getElementById(buttonId);

        if (passwordInput && toggleButton) {
            toggleButton.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.querySelector('i').classList.toggle('bi-eye-fill');
                this.querySelector('i').classList.toggle('bi-eye-slash-fill');
            });
        }
    }

    setupPasswordToggle('current_password', 'toggleCurrentPassword');
    setupPasswordToggle('new_password', 'toggleNewPassword');
    setupPasswordToggle('confirm_password', 'toggleConfirmPassword');
    setupPasswordToggle('password', 'togglePassword'); // Added for collaborator/user forms

    // --- Lógica para el modal de NOTAS en la tabla de inventario ---
    const notesModal = document.getElementById('notesModal');
    if (notesModal) {
        notesModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const notes = button.getAttribute('data-notes');
            const modalBody = notesModal.querySelector('#notesModalBody');
            modalBody.textContent = notes;
        });
    }

    // --- Lógica para generar una IP aleatoria ---
    const btnGenerateIp = document.getElementById('btn-generate-ip');
    const ipInput = document.getElementById('ip_asignada');

    if (btnGenerateIp && ipInput) {
        btnGenerateIp.addEventListener('click', function() {
            const octet1 = Math.floor(Math.random() * 254) + 1;
            const octet2 = Math.floor(Math.random() * 255);
            const octet3 = Math.floor(Math.random() * 255);
            const octet4 = Math.floor(Math.random() * 254) + 1;
            ipInput.value = `${octet1}.${octet2}.${octet3}.${octet4}`;
        });
    }

    // --- Lógica para la sugerencia automática del número de serie en el formulario unificado ---
    const prefijoSerieInput = document.getElementById('prefijo_serie');
    const numeroInicioSerieInput = document.getElementById('numero_inicio_serie');
    const suggestedSerialText = document.getElementById('current-suggested-serial');

    // Esta función se activará por cambios en prefijo_serie Y numero_inicio_serie
    const updateSuggestedSerialNumber = async () => {
        if (prefijoSerieInput && numeroInicioSerieInput) {
            const prefix = prefijoSerieInput.value;
            const currentNumber = numeroInicioSerieInput.value;

            // Solo si el prefijo o el número actual cambian, hacemos la llamada AJAX
            // Esta es la llamada que obtiene la SUGERENCIA del servidor
            const response = await fetch(`index.php?route=inventario&action=showAddForm&form_action=batch&prefijo_serie_sug=${encodeURIComponent(prefix)}`);
            const html = await response.text();

            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;

            const newSuggestedValue = tempDiv.querySelector('#numero_inicio_serie').value;

            console.log(`DEBUG JS: Valor sugerido obtenido del HTML temporal: ${newSuggestedValue}`);

            // Actualizar solo si la sugerencia es diferente o si el campo está vacío
            if (numeroInicioSerieInput.value === '' || parseFloat(numeroInicioSerieInput.value) < parseFloat(newSuggestedValue)) {
                console.log(`DEBUG JS: Actualizando numeroInicioSerieInput.value a: ${newSuggestedValue}`);
                numeroInicioSerieInput.value = newSuggestedValue;
            }

            if (suggestedSerialText) {
                suggestedSerialText.textContent = `Sugerido: ${newSuggestedValue}`;
            }
        }
    };

    if (prefijoSerieInput) {
        prefijoSerieInput.addEventListener('input', updateSuggestedSerialNumber);
    }
    if (numeroInicioSerieInput) {
        // También se actualiza la sugerencia si el número inicial cambia, pero solo si es un número válido y es menor que la sugerencia.
        numeroInicioSerieInput.addEventListener('input', updateSuggestedSerialNumber);
    }

    // --- NUEVA FUNCIÓN PARA PARSEAR REGLAS DE VALIDACIÓN CON CÓDIGO JS EMBEDIDO ---
    function parseJQueryValidateRules(rules) {
        const processedRules = JSON.parse(JSON.stringify(rules)); // Deep copy para no modificar el original

        // Función auxiliar para procesar una propiedad específica
        const processProperty = (obj, propName) => {
            if (typeof obj[propName] === 'string' && obj[propName].startsWith('__JS_FUNCTION__')) {
                const funcBody = obj[propName].substring('__JS_FUNCTION__'.length);
                obj[propName] = new Function(funcBody); // Convierte la cadena en una función
            }
        };

        for (const fieldName in processedRules) {
            // 1. Procesar la propiedad 'required' directamente si existe
            if (processedRules[fieldName] && processedRules[fieldName].required) { // Added check for processedRules[fieldName] existence
                processProperty(processedRules[fieldName], 'required');
            }

            // 2. Procesar propiedades dentro de 'remote.data'
            if (processedRules[fieldName] && processedRules[fieldName].remote && processedRules[fieldName].remote.data) { // Added check for existence
                for (const paramName in processedRules[fieldName].remote.data) {
                    processProperty(processedRules[fieldName].remote.data, paramName);
                }
            }
            // Si en el futuro añades más propiedades con __JS_FUNCTION__ (ej. 'minlength', 'max'), deberías añadirlas aquí también.
        }
        return processedRules;
    }

    // --- Inicialización de SweetAlert2 si hay un mensaje en la sesión ---
    const mensajeSa2 = JSON.parse(sessionStorage.getItem('mensaje_sa2'));
    if (mensajeSa2) {
        Swal.fire({
            title: mensajeSa2.title,
            text: mensajeSa2.text,
            icon: mensajeSa2.icon,
            confirmButtonText: 'Ok'
        });
        sessionStorage.removeItem('mensaje_sa2');
    }

    const phpMessage = JSON.parse(sessionStorage.getItem('php_message'));
    if (phpMessage) {
        Swal.fire({
            title: phpMessage.title || 'Error',
            text: phpMessage.text || 'Hubo un problema.',
            icon: phpMessage.icon || 'error',
            confirmButtonText: 'Ok'
        });
        sessionStorage.removeItem('php_message');
    }


    // --- Inicialización de jQuery Validate para todos los formularios ---
    if (typeof jQuery !== 'undefined' && typeof jQuery.validator !== 'undefined' && typeof validationRules !== 'undefined') {
        // Añadir métodos de validación personalizados si no existen (deben ser los mismos que antes)
        if (!jQuery.validator.methods.phonePA) {
            jQuery.validator.addMethod('phonePA', function(value, element) {
                let cleanValue = value.replace(/-/g, '');
                return this.optional(element) || /^(6\d{7}|[2-9]\d{6})$/.test(cleanValue);
            }, 'Debe ser un número válido de Panamá.');
        }
        if (!jQuery.validator.methods.ipv4) {
            jQuery.validator.addMethod('ipv4', function(value, element) {
                return this.optional(element) || /^((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/.test(value);
            }, 'Por favor, introduce una dirección IP v4 válida.');
        }
        if (!jQuery.validator.methods.pattern) {
            jQuery.validator.addMethod("pattern", function(value, element, param) {
                if (this.optional(element)) {
                    return true;
                }
                if (typeof param === 'string') {
                    param = new RegExp('^(?:' + param + ')$');
                }
                return param.test(value);
            }, "Formato inválido.");
        }
        if (!jQuery.validator.methods.dateISO) {
            jQuery.validator.addMethod("dateISO", function(value, element) {
                return this.optional(element) || /^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])$/.test(value);
            }, jQuery.validator.messages.date);
        }

        // Loop through all form IDs in validationRules and initialize them
        for (const formKey in validationRules) {
            if (document.getElementById(formKey)) {
                const rulesToApply = parseJQueryValidateRules(validationRules[formKey].rules);
                const messagesToApply = validationRules[formKey].messages;

                // Special handling for 'form-colaborador' password field
                if (formKey === 'form-colaborador') {
                    const isEditingColaborador = jQuery('#' + formKey + ' input[name="id"]').val() !== undefined && jQuery('#' + formKey + ' input[name="id"]').val() !== '';
                    if (!isEditingColaborador) {
                        // If creating a new collaborator, password is required
                        rulesToApply.password = rulesToApply.password || {};
                        rulesToApply.password.required = true;
                        messagesToApply.password = messagesToApply.password || {}
                        messagesToApply.password.required = 'La contraseña es obligatoria al crear un nuevo colaborador.';
                    } else {
                        // If editing, password is not required if left blank
                        if (rulesToApply.password) {
                            delete rulesToApply.password.required;
                        }
                    }
                }

                // Special handling for 'form-usuario' password field
                if (formKey === 'form-usuario') {
                    const isEditingUsuario = jQuery('#' + formKey + ' input[name="id"]').val() !== undefined && jQuery('#' + formKey + ' input[name="id"]').val() !== '';
                    if (!isEditingUsuario) {
                        // If creating a new user, password is required
                        rulesToApply.password = rulesToApply.password || {};
                        rulesToApply.password.required = true;
                        messagesToApply.password = messagesToApply.password || {};
                        messagesToApply.password.required = 'La contraseña es obligatoria al crear un nuevo usuario.';
                    } else {
                        // If editing, password is not required if left blank
                        if (rulesToApply.password) {
                            delete rulesToApply.password.required;
                        }
                    }
                }

                // Lógica condicional ESPECIAL para el formulario de inventario
                if (formKey === 'form-inventario-form') { // Apuntamos al ID correcto del <form>
                    // La miniatura es obligatoria solo si se cumplen ciertas condiciones
                    const cantidad = parseInt($('#cantidad').val()) || 1;
                    const isEditing = $('input[name="id"]').val() !== '';
                    // Es requerido si la cantidad es > 1 Y no estamos editando.
                    rulesToApply.imagen_miniatura = {
                        accept: "image/jpeg, image/png, image/gif",
                        required: function(element) {
                            return cantidad > 1 && !isEditing;
                        }
                    };
                }

                // Inicializa el plugin jQuery Validate para el formulario
                jQuery('#' + formKey).validate({
                    rules: rulesToApply,
                    messages: messagesToApply,
                    errorElement: 'div',
                    errorClass: 'text-danger form-text',
                    errorPlacement: function(error, element) {
                        if (element.parent().hasClass('input-group')) {
                            error.insertAfter(element.parent());
                        } else if (element.hasClass('form-select')) {
                            error.insertAfter(element.next('span.select2-container'));
                        } else {
                            error.insertAfter(element);
                        }
                    },
                    highlight: function(element) {
                        jQuery(element).addClass('is-invalid').removeClass('is-valid');
                    },
                    unhighlight: function(element) {
                        jQuery(element).removeClass('is-invalid').addClass('is-valid');
                    },
                    submitHandler: function(form) {
                        form.submit();
                    }
                });
            }
        }
    }
});