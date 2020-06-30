<?php

use App\Models\Admin;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Utils\ConfigHelper;
use App\Utils\StringHelper;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $manageRoles = Permission::query()->create([
            'name' => 'role-manage',
            'display_name' => 'Manage roles',
            'description' => 'Manage roles',
        ]);
        $manageAdmins = Permission::query()->create([
            'name' => 'admin-manage',
            'display_name' => 'Manage admins',
            'description' => 'Manage admins',
        ]);
        $manageWatchers = Permission::query()->create([
            'name' => 'watcher-manage',
            'display_name' => 'Manage watchers',
            'description' => 'Manage watchers',
        ]);
        $manageEvents = Permission::query()->create([
            'name' => 'event-manage',
            'display_name' => 'Manage events',
            'description' => 'Manage events',
        ]);

        $adminRole = Role::query()->create([
            'name' => 'admin',
            'display_name' => 'Admin',
            'description' => 'Admin',
        ]);
        $adminRole->permissions()->attach([
            $manageRoles->id,
            $manageAdmins->id,
            $manageWatchers->id,
            $manageEvents->id,
        ]);

        // Users for each roles

        Admin::query()->create([
            'user_id' => User::query()->create([
                'display_name' => 'Administrator',
                'email' => 'admin@dsquare.com.vn',
                'password' => StringHelper::hash('NNA*Tb3x'),
                'url_avatar' => ConfigHelper::defaultAvatarUrl(),
            ])->id,
            'role_id' => $adminRole->id,
        ]);
    }
}
