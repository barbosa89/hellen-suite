<?php

declare(strict_types=1);

return [

    'title' => 'Backup and restore',
    'description' => 'Export a complete copy of the local data or restore one you saved earlier.',
    'maintenance_unavailable' => 'The desktop application is temporarily unavailable. Wait for maintenance to finish and try again.',

    'sections' => [
        'export' => [
            'heading' => 'Create a backup',
            'description' => 'Save a single file with the local database, hotel images and payment support documents.',
            'action' => 'Create backup',
            'working' => 'Creating the backup…',
            'hint' => 'The application pauses briefly while the copy is prepared so the database and its attachments match.',
            'overwrite_action' => 'Replace the existing file',
            'overwrite_warning' => 'A file already exists at :path. Replacing it removes the backup that is stored there.',
        ],
        'restore' => [
            'heading' => 'Restore a backup',
            'description' => 'Replace everything stored on this computer with the contents of a backup file.',
            'action' => 'Choose backup file',
            'working' => 'Reading the backup…',
            'confirm_heading' => 'Replace the local data?',
            'confirm_description' => 'The current records, hotel images and payment documents on this computer will be replaced by the selected backup.',
            'confirm_action' => 'Restore now',
            'cancel' => 'Cancel',
            'hint' => 'Restoring replaces the local data entirely. It cannot be undone.',
        ],
    ],

    'warnings' => [
        'plain' => 'Backup files are not encrypted. Anyone with access to the file can read the guest and payment information it contains.',
        'storage' => 'Store the file somewhere safe and separate from this computer.',
        'keys' => 'Encrypted credentials can only be read again if the encryption key of this installation is available.',
    ],

    'details' => [
        'file' => 'File',
        'created_at' => 'Created',
        'application_version' => 'Hellen Suite version',
        'files' => 'Included files',
        'size' => 'Size',
    ],

    'dialogs' => [
        'export' => [
            'title' => 'Save the backup file',
            'button' => 'Save backup',
        ],
        'restore' => [
            'title' => 'Select a backup file',
            'button' => 'Select backup',
        ],
    ],

    'messages' => [
        'export_completed' => 'The backup was saved to :path.',
        'export_cancelled' => 'The backup was cancelled and nothing was changed.',
        'export_exists' => 'A file already exists at :path. Choose a different name to keep it safe.',
        'export_failed' => 'The backup could not be created: :reason',
        'restore_invalid' => 'The selected file is not a usable backup: :reason',
        'restore_cancelled' => 'No backup file was selected and nothing was changed.',
        'restore_missing' => 'The selected backup is no longer available. Choose the file again.',
        'restore_failed' => 'The backup could not be restored: :reason',
        'restore_completed' => 'The backup was restored. The application was reloaded with the restored data.',
    ],

];
