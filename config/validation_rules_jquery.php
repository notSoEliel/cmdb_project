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
            'password' => ['minlength' => 8]
        ],
        'messages' => [
            'nombre' => ['required' => 'El nombre es obligatorio.'],
            'apellido' => ['required' => 'El apellido es obligatorio.'],
            'identificacion_unica' => ['required' => 'La identificación es obligatoria.'],
            'email' => ['required' => 'El email es obligatorio.', 'email' => 'Por favor, introduce un email válido.'],
            'ubicacion' => ['required' => 'La ubicación es obligatoria.'],
            'telefono' => ['required' => 'El teléfono es obligatorio.'],
            'password' => ['minlength' => 'La contraseña debe tener al menos 8 caracteres.']
        ]
    ],

    // Reglas para el formulario de Inventario
    'form-inventario' => [
        'rules' => [
            'nombre_equipo' => ['required' => true, 'maxlength' => 150],
            'categoria_id' => ['required' => true],
            'marca' => ['required' => true],
            'modelo' => ['required' => true],
            'serie' => ['required' => true],
            'costo' => ['required' => true, 'number' => true, 'min' => 0],
            'tiempo_depreciacion_anios' => ['required' => true, 'digits' => true, 'min' => 0],
            'fecha_ingreso' => ['required' => true, 'date' => true]
        ],
        'messages' => [
            'nombre_equipo' => ['required' => 'El nombre del equipo es obligatorio.'],
            'categoria_id' => ['required' => 'Debes seleccionar una categoría.'],
            'marca' => ['required' => 'La marca es obligatoria.'],
            'modelo' => ['required' => 'El modelo es obligatorio.'],
            'serie' => ['required' => 'El número de serie es obligatorio.'],
            'costo' => ['required' => 'El costo es obligatorio.', 'number' => 'El costo debe ser un número.', 'min' => 'El costo no puede ser negativo.'],
            'tiempo_depreciacion_anios' => ['required' => 'El tiempo de depreciación es obligatorio.', 'digits' => 'Solo se aceptan números enteros.', 'min' => 'No puede ser un valor negativo.'],
            'fecha_ingreso' => ['required' => 'La fecha de ingreso es obligatoria.']
        ]
    ],

    // Reglas para cambio de contraseña
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
                'equalTo' => '#new_password' // Compara con el campo que tiene el ID 'new_password'
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
];
