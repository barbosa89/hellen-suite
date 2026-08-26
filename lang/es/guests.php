<?php

declare(strict_types=1);

return [
    'title' => 'Huéspedes',
    'actions' => ['create' => 'Registrar huésped', 'edit' => 'Editar huésped', 'view' => 'Ver ficha'],
    'fields' => [
        'first_name' => ['label' => 'Nombres'],
        'last_name' => ['label' => 'Apellidos'],
        'identification_type' => ['label' => 'Tipo de documento'],
        'identification_number' => ['label' => 'Número de documento'],
        'mobile' => ['label' => 'Celular'],
        'email' => ['label' => 'Correo electrónico'],
    ],
    'pages' => [
        'index' => ['heading' => 'Huéspedes', 'description' => 'Consulta y actualiza las fichas administrativas de :hotel.', 'search' => 'Buscar por nombre, documento o celular', 'empty_title' => 'Aún no hay huéspedes registrados', 'empty' => 'Los huéspedes creados durante un check-in aparecerán aquí para su administración.'],
        'create' => ['heading' => 'Registrar huésped', 'description' => 'Crea una ficha administrativa para una futura estancia.'],
        'edit' => ['heading' => 'Editar huésped', 'description' => 'Corrige los datos administrativos de esta ficha.'],
        'show' => ['heading' => 'Ficha de huésped', 'stays' => 'Estancias', 'empty_stays' => 'Este huésped aún no tiene estancias registradas.'],
    ],
    'form' => ['identity' => ['title' => 'Identificación', 'description' => 'La identificación evita duplicados en este hotel.'], 'contact' => ['title' => 'Contacto', 'description' => 'Estos datos son opcionales y se usan para la atención del huésped.'], 'select_identification_type' => 'Selecciona un tipo de documento'],
    'messages' => ['created' => 'Huésped registrado con éxito.', 'updated' => 'Ficha de huésped actualizada con éxito.'],
];
