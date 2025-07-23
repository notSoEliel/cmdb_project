<?php

return [
    // Reglas para el formulario de Colaboradores
    'form-colaborador' => [
        'rules' => [
            'nombre' => ['required' => true, 'maxlength' => 50],
            'apellido' => ['required' => true, 'maxlength' => 50],
            'identificacion_unica' => ['required' => true, 'maxlength' => 20],
            'email' => ['required' => true, 'email' => true],
            'ubicacion' => ['required' => true],
            'telefono' => ['required' => true, 'phonePA' => true],
            // La regla 'required' para 'password' se maneja dinámicamente en app.js
            'password' => ['minlength' => 8],
            'departamento' => ['required' => true],
            'ip_asignada' => ['ipv4' => true],
            'foto_perfil' => [
                'accept' => 'image/jpeg, image/png, image/gif'
            ],
        ],
        'messages' => [
            'nombre' => ['required' => 'El nombre es obligatorio.'],
            'apellido' => ['required' => 'El apellido es obligatorio.'],
            'identificacion_unica' => ['required' => 'La identificación es obligatoria.'],
            'email' => ['required' => 'El email es obligatorio.', 'email' => 'Por favor, introduce un email válido.'],
            'ubicacion' => ['required' => 'La ubicación es obligatoria.'],
            'telefono' => ['required' => 'El teléfono es obligatorio.'],
            'password' => ['minlength' => 'La contraseña debe tener al menos 8 caracteres.'],
            'departamento' => ['required' => 'El departamento es obligatorio.'],
            'ip_asignada' => ['ipv4' => 'La IP asignada debe ser una dirección IPv4 válida.'],
            'foto_perfil' => [
                'accept' => 'Solo se permiten archivos de imagen (jpg, png, gif).'
            ]
        ]
    ],

    // Reglas para el formulario de Inventario (EQUIPO INDIVIDUAL)
    'form-inventario' => [
        'rules' => [
            'nombre_equipo' => ['required' => true, 'maxlength' => 150],
            'categoria_id' => ['required' => true],
            'marca' => ['required' => true],
            'modelo' => ['required' => true],
            'serie' => [
                'required' => true,
                'minlength' => 3, // Mínimo 3 para la parte numérica de 4 dígitos (ej. 001)
                'maxlength' => 50, // Máximo 50 caracteres (prefijo + numero)
                'pattern' => '^[a-zA-Z0-9\\s\\-_]+$', // Alfanumérico, espacios, guiones, guiones bajos
                'remote' => [ // Validación de unicidad via AJAX
                    'url' => BASE_URL . 'index.php?route=inventario&action=checkSerialUniqueness',
                    'type' => 'get',
                    'data' => [
                        'id' => 'function() { return jQuery("#form-inventario input[name=\\"id\\"]").val(); }' // Pasa el ID del equipo para excluirlo en edición
                    ]
                ]
            ],
            'costo' => ['required' => true, 'number' => true, 'min' => 0],
            'tiempo_depreciacion_anios' => ['required' => true, 'digits' => true, 'min' => 0],
            'fecha_ingreso' => ['required' => true, 'dateISO' => true] // dateISO para formato YYYY-MM-DD
            // 'estado' y 'notas_donacion' se manejan con reglas condicionales en app.js para 'form-inventario'
        ],
        'messages' => [
            'nombre_equipo' => 'El nombre del equipo es obligatorio.',
            'categoria_id' => 'La categoría es obligatoria.',
            'marca' => 'La marca es obligatoria.',
            'modelo' => 'El modelo es obligatorio.',
            'serie' => [
                'required' => 'El número de serie es obligatorio.',
                'minlength' => 'El número de serie debe tener al menos 3 caracteres.',
                'maxlength' => 'El número de serie es demasiado largo (máximo 50 caracteres).',
                'pattern' => 'El número de serie contiene caracteres no permitidos. Solo letras, números, guiones, guiones bajos y espacios.',
                'remote' => 'Este número de serie ya existe.'
            ],
            'costo' => [
                'required' => 'El costo es obligatorio.',
                'number' => 'El costo debe ser un número.',
                'min' => 'El costo no puede ser negativo.'
            ],
            'tiempo_depreciacion_anios' => [
                'required' => 'El tiempo de depreciación es obligatorio.',
                'digits' => 'Solo se aceptan números enteros.',
                'min' => 'No puede ser un valor negativo.'
            ],
            'fecha_ingreso' => 'La fecha de ingreso es obligatoria.'
        ]
    ],

    // Reglas para el formulario de Inventario (EQUIPOS POR LOTE)
    'form-inventario-lote' => [
        'rules' => [
            'cantidad' => ['required' => true, 'digits' => true, 'min' => 1],
            'prefijo_serie' => [
                'maxlength' => 46, // 50 (max serie) - 4 (digitos) = 46
                'pattern' => '^[a-zA-Z0-9\\s\\-_]*$' // Puede ser vacío, o alfanumérico
            ],
            'numero_inicio_serie' => ['required' => true, 'digits' => true, 'min' => 0],
            'nombre_equipo' => ['required' => true],
            'categoria_id' => ['required' => true],
            'marca' => ['required' => true],
            'modelo' => ['required' => true],
            'costo' => ['required' => true, 'number' => true, 'min' => 0],
            'fecha_ingreso' => ['required' => true, 'dateISO' => true],
            'tiempo_depreciacion_anios' => ['required' => true, 'digits' => true, 'min' => 0]
            // notas_donacion_lote no se valida aquí porque es opcional. La validación del lado del servidor es suficiente.
        ],
        'messages' => [
            'cantidad' => [
                'required' => 'La cantidad es obligatoria.',
                'digits' => 'La cantidad debe ser un número entero.',
                'min' => 'La cantidad mínima es 1.'
            ],
            'prefijo_serie' => [
                'maxlength' => 'El prefijo de serie es demasiado largo (máximo 46 caracteres).',
                'pattern' => 'El prefijo de serie contiene caracteres no permitidos.'
            ],
            'numero_inicio_serie' => [
                'required' => 'El número inicial de serie es obligatorio.',
                'digits' => 'El número inicial debe ser un número entero.',
                'min' => 'El número inicial no puede ser negativo.'
            ],
            'nombre_equipo' => 'El nombre del equipo es obligatorio.',
            'categoria_id' => 'La categoría es obligatoria.',
            'marca' => 'La marca es obligatoria.',
            'modelo' => 'El modelo es obligatorio.',
            'costo' => [
                'required' => 'El costo es obligatorio.',
                'number' => 'El costo debe ser un número válido.',
                'min' => 'El costo no puede ser negativo.'
            ],
            'fecha_ingreso' => [
                'required' => 'La fecha de ingreso es obligatoria.',
                'dateISO' => 'Por favor, introduce una fecha válida (YYYY-MM-DD).'
            ],
            'tiempo_depreciacion_anios' => [
                'required' => 'La depreciación en años es obligatoria.',
                'digits' => 'La depreciación debe ser un número entero.',
                'min' => 'La depreciación no puede ser negativa.'
            ]
        ]
    ],

    // Reglas para cambio de contraseña de perfil de colaboradores
    'form-password' => [
        'rules' => [
            'current_password' => [
                'required' => true
            ],
            'new_password' => [
                'required' => true,
                'minlength' => 8
            ],
            'confirm_password' => [
                'required' => true,
                'equalTo' => '#new_password'
            ]
        ],
        'messages' => [
            'current_password' => [
                'required' => 'Por favor, introduce tu contraseña actual.'
            ],
            'new_password' => [
                'required' => 'Por favor, introduce una nueva contraseña.',
                'minlength' => 'Tu contraseña debe tener al menos 8 caracteres.'
            ],
            'confirm_password' => [
                'required' => 'Por favor, confirma tu nueva contraseña.',
                'equalTo' => 'Las contraseñas no coinciden. Por favor, verifica.'
            ]
        ]
    ],

    // Reglas para cambio de ubicación en form colaborador
    'form-location' => [
        'rules' => [
            'ubicacion' => ['required' => true]
        ],
        'messages' => [
            'ubicacion' => ['required' => 'La ubicación no puede estar vacía.']
        ]
    ],

    // Reglas para el form de Usuarios (admins)
    'form-usuario' => [
        'rules' => [
            'nombre' => ['required' => true, 'maxlength' => 50],
            'email' => ['required' => true, 'email' => true],
            'password' => ['minlength' => 8] // No es requerido al editar
        ],
        'messages' => [
            'nombre' => ['required' => 'El nombre es obligatorio.'],
            'email' => ['required' => 'El email es obligatorio.', 'email' => 'Introduce un email válido.'],
            'password' => ['minlength' => 'La contraseña debe tener al menos 8 caracteres.']
        ]
    ],

    // Reglas para cambio de contraseña de perfil de administradores
    'form-admin-password' => [
        'rules' => [
            'current_password' => ['required' => true],
            'new_password' => ['required' => true, 'minlength' => 8],
            'confirm_password' => [
                'required' => true,
                'equalTo' => '#new_password'
            ]
        ],
        'messages' => [
            'new_password' => ['minlength' => 'La contraseña debe tener al menos 8 caracteres.'],
            'confirm_password' => ['equalTo' => 'Las contraseñas no coinciden.']
        ]
    ],

    // Reglas para el formulario de restablecimiento de contraseña
    'form-forgot-password' => [
        'rules' => ['email' => ['required' => true, 'email' => true]],
        'messages' => ['email' => ['required' => 'El correo es obligatorio.', 'email' => 'Introduce un correo válido.']]
    ],
    'form-reset-password' => [
        'rules' => [
            'new_password' => ['required' => true, 'minlength' => 8],
            'confirm_password' => ['required' => true, 'equalTo' => '#new_password']
        ],
        'messages' => [
            'new_password' => ['minlength' => 'La contraseña debe tener al menos 8 caracteres.'],
            'confirm_password' => ['equalTo' => 'Las contraseñas no coinciden.']
        ]
    ],
];
