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

    // --- Lógica para la sugerencia automática del número de serie en el formulario de lote ---
    const prefijoSerieInput = document.getElementById('prefijo_serie');
    const numeroInicioSerieInput = document.getElementById('numero_inicio_serie');
    const suggestedSerialText = document.getElementById('current-suggested-serial');

    if (prefijoSerieInput && numeroInicioSerieInput) {
        const getSuggestedSerialNumber = async (prefix) => {
            const url = `index.php?route=inventario&action=showAddForm&form_action=batch&prefijo_serie_sug=${encodeURIComponent(prefix)}`;
            const response = await fetch(url);
            const html = await response.text();

            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;

            const newSuggestedValue = tempDiv.querySelector('#numero_inicio_serie').value;
            return newSuggestedValue;
        };

        prefijoSerieInput.addEventListener('input', async () => {
            const prefix = prefijoSerieInput.value;
            const newSuggestedNum = await getSuggestedSerialNumber(prefix);
            numeroInicioSerieInput.value = newSuggestedNum;
            if (suggestedSerialText) {
                suggestedSerialText.textContent = `Sugerido: ${newSuggestedNum}`;
            }
        });
    }

    // --- NUEVA FUNCIÓN PARA PARSEAR REGLAS DE VALIDACIÓN CON CÓDIGO JS EMBEBIDO ---
    function parseJQueryValidateRules(rules) {
        // Hacemos una copia profunda para no modificar el objeto original en `validationRules`
        // Esto es importante para que si tienes varias instancias de reglas, no se sobrescriban.
        const processedRules = JSON.parse(JSON.stringify(rules));

        for (const fieldName in processedRules) {
            // Buscamos reglas con 'remote' y que tengan la propiedad 'data'
            if (processedRules[fieldName].remote && processedRules[fieldName].remote.data) {
                for (const paramName in processedRules[fieldName].remote.data) {
                    const paramValue = processedRules[fieldName].remote.data[paramName];
                    // Si el valor es una cadena y empieza con nuestro prefijo especial
                    if (typeof paramValue === 'string' && paramValue.startsWith('__JS_FUNCTION__')) {
                        const funcBody = paramValue.substring('__JS_FUNCTION__'.length); // Obtenemos solo el cuerpo de la función (sin el prefijo)
                        // Convertimos la cadena en una función JavaScript real usando el constructor Function
                        // Esto permite que el código JS que viene como string sea ejecutado.
                        processedRules[fieldName].remote.data[paramName] = new Function(funcBody);
                    }
                }
            }
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

    // --- Inicialización de jQuery Validate para los formularios ---
    // Asegúrate de que jQuery y jQuery Validate estén cargados antes que este script.
    // 'validationRules' es una variable global definida por ValidationService.php (se incluye antes)
    if (typeof jQuery !== 'undefined' && typeof jQuery.validator !== 'undefined' && typeof validationRules !== 'undefined') {
        // Añadir métodos de validación personalizados si no existen
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
        if (!jQuery.validator.methods.pattern) { // Si el método 'pattern' no está en el plugin
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
        if (!jQuery.validator.methods.dateISO) { // Asegurarse de que dateISO está disponible
            jQuery.validator.addMethod("dateISO", function(value, element) {
                return this.optional(element) || /^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])$/.test(value);
            }, jQuery.validator.messages.date);
        }

        for (const formId in validationRules) {
            if (document.getElementById(formId)) { // Solo inicializar si el formulario existe en la página
                // Procesa las reglas para convertir las cadenas de función en funciones reales
                const processedRules = parseJQueryValidateRules(validationRules[formId].rules);
                jQuery('#' + formId).validate({
                    rules: processedRules,
                    messages: validationRules[formId].messages,
                    errorElement: 'div',
                    errorClass: 'text-danger form-text', // Añadir form-text para mejor estilo
                    errorPlacement: function(error, element) {
                        if (element.parent().hasClass('input-group')) {
                            error.insertAfter(element.parent());
                        } else if (element.hasClass('form-select')) { // Para selectores
                            error.insertAfter(element.next('span.select2-container')); // Si usas select2
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
                    // Lógica para contraseñas condicionalmente requeridas (form-colaborador, form-usuario)
                    // y notas de donación (form-inventario)
                    // Esta lógica se maneja directamente aquí al configurar las reglas en .validate()
                    // Si el formulario es de colaborador o usuario
                    submitHandler: function(form) {
                        // Guardar mensaje SweetAlert para después de la redirección
                        // sessionStorage.setItem('mensaje_sa2', JSON.stringify({
                        //     title: 'Procesando...',
                        //     text: 'Guardando datos, por favor espera.',
                        //     icon: 'info'
                        // }));
                        form.submit(); // Enviar el formulario
                    }
                });

                // Lógica condicional para campos específicos que dependen del estado del formulario (edición vs. creación)
                // Se aplica a form-colaborador y form-usuario para la contraseña
                const isEditing = jQuery('#' + formId + ' input[name="id"]').val() !== undefined && jQuery('#' + formId + ' input[name="id"]').val() !== '';
                if ((formId === 'form-colaborador' || formId === 'form-usuario') && !isEditing) {
                    // Si estamos creando, la contraseña es obligatoria.
                    jQuery('#' + formId + ' input[name="password"]').rules('add', {
                        required: true,
                        messages: {
                            required: 'La contraseña es obligatoria al crear.'
                        }
                    });
                } else if ((formId === 'form-colaborador' || formId === 'form-usuario') && isEditing) {
                    // Si estamos editando, la contraseña no es obligatoria a menos que se haya rellenado.
                    // Para que no valide si se deja vacío al editar, la regla 'required' debe ser eliminada
                    // o no añadida si el campo está vacío.
                    // Aquí simplemente la regla 'minlength' será suficiente si no es requerido.
                    jQuery('#' + formId + ' input[name="password"]').rules('remove', 'required');
                }

                // Lógica condicional para notas_donacion en form-inventario
                if (formId === 'form-inventario') {
                    const estadoSelect = document.getElementById('estado');
                    if (estadoSelect) {
                        // Cuando el estado cambia
                        jQuery(estadoSelect).on('change', function() {
                            const estado = jQuery(this).val();
                            if (estado === 'Donado' || estado === 'En Descarte') {
                                jQuery('#notas_donacion').rules('add', {
                                    required: true,
                                    messages: {
                                        required: 'Las notas de donación/descarte son obligatorias para el estado seleccionado.'
                                    }
                                });
                            } else {
                                jQuery('#notas_donacion').rules('remove', 'required');
                            }
                        });
                        // Y al cargar la página para el estado inicial
                        const initialEstado = jQuery(estadoSelect).val();
                        if (initialEstado === 'Donado' || initialEstado === 'En Descarte') {
                            jQuery('#notas_donacion').rules('add', {
                                required: true,
                                messages: {
                                    required: 'Las notas de donación/descarte son obligatorias para el estado seleccionado.'
                                }
                            });
                        }
                    }
                }
            }
        }
    }
});