<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

return [
    \App\Models\Admin::class => [
        'display_name' => 'Display name',
        'role_name' => 'Role',
        'permission_names' => 'Permissions',
    ],
    \App\Models\Role::class => [
        'id' => 'ID',
        'display_name' => 'Display name',
        'name' => 'Name',
        'permission_names' => 'Permissions',
        'description' => 'Description',
    ],
    \App\Models\User::class => [
        'id' => 'ID',
        'email' => 'Email',
        'has_password' => 'Has password',
    ],
    // TODO:

    // TODO
];