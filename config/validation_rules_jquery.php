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
            'ip_asignada' => ['required' => true, 'ipv4' => true],
            'foto_perfil' => [
                'accept' => 'image/jpeg, image/png'
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
            'ip_asignada' => ['required' => 'La IP asignada es obligatoria.', 'ipv4' => 'La IP asignada debe ser una dirección IPv4 válida.'],
            'foto_perfil' => [
                'accept' => 'Solo se permiten archivos de imagen (jpg, png).'
            ]
        ]
    ],

    'form-inventario-form' => [ // Nuevo ID de formulario
        'rules' => [
            'cantidad' => ['required' => true, 'digits' => true, 'min' => 1],
            'prefijo_serie' => [
                'maxlength' => 46,
                'pattern' => '^[a-zA-Z0-9\\s\\-_]+-$', // Prefijo debe terminar con guion
            ],
            'numero_inicio_serie' => [
                'required' => true,
                'digits' => true,
                'min' => 0,
                // Validación remota para la serie COMPLETA (prefijo + número)
                'remote' => [
                    'url' => BASE_URL . 'index.php?route=inventario&action=checkSerialUniqueness',
                    'type' => 'get',
                    'data' => [
                        // Pasamos tanto el prefijo como el número inicial, y el ID si es edición
                        'prefijo' => '__JS_FUNCTION__return jQuery("#prefijo_serie").val();',
                        'numero' => '__JS_FUNCTION__return jQuery("#numero_inicio_serie").val();',
                        'id' => '__JS_FUNCTION__return jQuery("[name=\\"id\\"]").val();' // ID del equipo editando
                    ]
                ]
            ],
            'nombre_equipo' => ['required' => true],
            'categoria_id' => ['required' => true],
            'marca' => ['required' => true],
            'modelo' => ['required' => true],
            'costo' => ['required' => true, 'number' => true, 'min' => 0],
            'fecha_ingreso' => ['required' => true, 'dateISO' => true],
            'tiempo_depreciacion_anios' => ['required' => true, 'digits' => true, 'min' => 0],
            'imagen_miniatura' => [
                'accept' => 'image/png, image/jpeg', // Solo permite estos tipos de archivo,
                'required' => '__JS_FUNCTION__return jQuery("#cantidad").val() > 1 && jQuery("[name=\\"id\\"]").val() === "";'
            ]
        ],
        'messages' => [
            'cantidad' => [
                'required' => 'La cantidad es obligatoria.',
                'digits' => 'La cantidad debe ser un número entero.',
                'min' => 'La cantidad mínima es 1.'
            ],
            'prefijo_serie' => [
                'maxlength' => 'El prefijo de serie es demasiado largo (máximo 46 caracteres).',
                'pattern' => 'El prefijo de serie debe contener caracteres válidos y terminar con un guion (-).'
            ],
            'numero_inicio_serie' => [
                'required' => 'El número inicial de serie es obligatorio.',
                'digits' => 'El número inicial debe ser un número entero.',
                'min' => 'El número inicial no puede ser negativo.',
                'remote' => 'Este número de serie completo ya existe.' // Mensaje para la validación remota
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
            ],
            'imagen_miniatura' => [
                'accept' => 'Solo se permiten archivos de imagen (jpg, png) para la miniatura.',
                'required' => 'La miniatura es obligatoria para la creación de lotes.'
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
