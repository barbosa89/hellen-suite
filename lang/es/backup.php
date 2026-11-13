<?php

declare(strict_types=1);

return [

    'title' => 'Copias de seguridad',
    'description' => 'Exporta una copia completa de los datos locales o restaura una guardada anteriormente.',
    'maintenance_unavailable' => 'La aplicación de escritorio no está disponible temporalmente. Espera a que termine el mantenimiento e inténtalo de nuevo.',

    'sections' => [
        'export' => [
            'heading' => 'Crear una copia de seguridad',
            'description' => 'Guarda un único archivo con la base de datos local, las imágenes de hoteles y los soportes de pago.',
            'action' => 'Crear copia de seguridad',
            'working' => 'Creando la copia de seguridad…',
            'hint' => 'La aplicación se pausa un momento mientras prepara la copia para que la base de datos y sus adjuntos coincidan.',
            'overwrite_action' => 'Reemplazar el archivo existente',
            'overwrite_warning' => 'Ya existe un archivo en :path. Reemplazarlo elimina la copia de seguridad guardada allí.',
        ],
        'restore' => [
            'heading' => 'Restaurar una copia de seguridad',
            'description' => 'Reemplaza todo lo almacenado en este equipo por el contenido de un archivo de copia de seguridad.',
            'action' => 'Elegir archivo de copia',
            'working' => 'Leyendo la copia de seguridad…',
            'confirm_heading' => '¿Reemplazar los datos locales?',
            'confirm_description' => 'Los registros, las imágenes de hoteles y los soportes de pago actuales de este equipo serán reemplazados por la copia de seguridad seleccionada.',
            'confirm_action' => 'Restaurar ahora',
            'cancel' => 'Cancelar',
            'hint' => 'Restaurar reemplaza por completo los datos locales. No se puede deshacer.',
        ],
    ],

    'warnings' => [
        'plain' => 'Los archivos de copia de seguridad no están cifrados. Cualquiera que tenga acceso al archivo puede leer la información de huéspedes y pagos que contiene.',
        'storage' => 'Guarda el archivo en un lugar seguro y separado de este equipo.',
        'keys' => 'Las credenciales cifradas solo pueden volver a leerse si la clave de cifrado de esta instalación está disponible.',
    ],

    'details' => [
        'file' => 'Archivo',
        'created_at' => 'Creada',
        'application_version' => 'Versión de Hellen Suite',
        'files' => 'Archivos incluidos',
        'size' => 'Tamaño',
    ],

    'dialogs' => [
        'export' => [
            'title' => 'Guardar el archivo de copia de seguridad',
            'button' => 'Guardar copia',
        ],
        'restore' => [
            'title' => 'Seleccionar un archivo de copia de seguridad',
            'button' => 'Seleccionar copia',
        ],
    ],

    'messages' => [
        'export_completed' => 'La copia de seguridad se guardó en :path.',
        'export_cancelled' => 'Se canceló la copia de seguridad y no se modificó nada.',
        'export_exists' => 'Ya existe un archivo en :path. Elige otro nombre para conservarlo a salvo.',
        'export_failed' => 'No se pudo crear la copia de seguridad: :reason',
        'restore_invalid' => 'El archivo seleccionado no es una copia de seguridad utilizable: :reason',
        'restore_cancelled' => 'No se seleccionó ningún archivo y no se modificó nada.',
        'restore_missing' => 'La copia de seguridad seleccionada ya no está disponible. Vuelve a elegir el archivo.',
        'restore_failed' => 'No se pudo restaurar la copia de seguridad: :reason',
        'restore_completed' => 'La copia de seguridad se restauró. La aplicación se recargó con los datos restaurados.',
    ],

];
