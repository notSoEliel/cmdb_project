<?php
// config/validation_rules_jquery.php

return [
    // Reglas para el formulario de Colaboradores
    'form-colaborador' => [
        'rules' => [
            'nombre' => ['required' => true, 'maxlength' => 50],
            'apellido' => ['required' => true, 'maxlength' => 50],
            'identificacion_unica' => ['required' => true, 'maxlength' => 20],
            'email' => ['required' => true, 'email' => true],
            'ubicacion' => ['required' => true], // Ya era obligatorio
            'telefono' => ['required' => true, 'phonePA' => true], // Ya era obligatorio
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
            'marca' => ['required' => true], // <-- AÑADIDO
            'modelo' => ['required' => true], // <-- AÑADIDO
            'serie' => ['required' => true], // <-- AÑADIDO
            'costo' => ['required' => true, 'number' => true, 'min' => 0], // <-- AÑADIDO 'required'
            'tiempo_depreciacion_anios' => ['required' => true, 'digits' => true, 'min' => 0], // <-- AÑADIDO 'required'
            'fecha_ingreso' => ['required' => true, 'date' => true]
        ],
        'messages' => [
            'nombre_equipo' => ['required' => 'El nombre del equipo es obligatorio.'],
            'categoria_id' => ['required' => 'Debes seleccionar una categoría.'],
            'marca' => ['required' => 'La marca es obligatoria.'], // <-- AÑADIDO
            'modelo' => ['required' => 'El modelo es obligatorio.'], // <-- AÑADIDO
            'serie' => ['required' => 'El número de serie es obligatorio.'], // <-- AÑADIDO
            'costo' => ['required' => 'El costo es obligatorio.', 'number' => 'El costo debe ser un número.', 'min' => 'El costo no puede ser negativo.'], // <-- AÑADIDO 'required'
            'tiempo_depreciacion_anios' => ['required' => 'El tiempo de depreciación es obligatorio.', 'digits' => 'Solo se aceptan números enteros.', 'min' => 'No puede ser un valor negativo.'], // <-- AÑADIDO 'required'
            'fecha_ingreso' => ['required' => 'La fecha de ingreso es obligatoria.']
        ]
    ],
];