<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Permissions to sync
    |--------------------------------------------------------------------------
    |
    | Permission names to ensure exist when running `php artisan permission:sync`.
    | Add or remove names as needed; the command will create missing ones only.
    |
    */

    'sync' => [
        'view_property',
        'create_property',
        'update_property',
        'delete_property',
        'view_role',
        'create_role',
        'update_role',
        'delete_role',
        'view_permission',
        'create_permission',
        'update_permission',
        'delete_permission',
    ],

];
