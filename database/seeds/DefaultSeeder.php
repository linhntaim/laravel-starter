<?php

use App\Models\Admin;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Utils\StringHelper;

class DefaultSeeder extends Seeder
{
    protected $systemPassword = ')^KM$bB-W7:Z@8eG';
    protected $superAdminPassword = '3sQUJ8yXc@m#3bx3';
    protected $administratorPassword = 'NNA*Tb3x';

    public function run()
    {
        // System users
        $beSystem = Permission::query()->create([
            'name' => 'be-system',
            'display_name' => 'Be system',
            'description' => 'Be system',
        ]);
        $systemRole = Role::query()->create([
            'name' => 'system',
            'display_name' => 'System',
            'description' => 'Representation of the system',
        ]);
        $systemRole->permissions()->attach([
            $beSystem->id,
        ]);

        $beSuperAdmin = Permission::query()->create([
            'name' => 'be-super-admin',
            'display_name' => 'Be super-admin',
            'description' => 'Be super-admin',
        ]);
        $superAdminRole = Role::query()->create([
            'name' => 'superadmin',
            'display_name' => 'Super-admin',
            'description' => 'Super-admin of the system',
        ]);
        $superAdminRole->permissions()->attach([
            $beSuperAdmin->id,
        ]);

        Admin::query()->create([
            'user_id' => User::query()->create([
                'email' => 'system@dsquare.com.vn',
                'password' => StringHelper::hash($this->systemPassword),
            ])->id,
            'role_id' => $systemRole->id,
            'display_name' => 'System',
        ]);

        Admin::query()->create([
            'user_id' => User::query()->create([
                'email' => 'superadmin@dsquare.com.vn',
                'password' => StringHelper::hash($this->superAdminPassword),
            ])->id,
            'role_id' => $superAdminRole->id,
            'display_name' => 'Super Administrator',
        ]);

        // Normal administrative users
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

        $adminRole = Role::query()->create([
            'name' => 'admin',
            'display_name' => 'Admin',
            'description' => 'Admin',
        ]);
        $adminRole->permissions()->attach([
            $manageRoles->id,
            $manageAdmins->id,
        ]);

        Admin::query()->create([
            'user_id' => User::query()->create([
                'email' => 'admin@dsquare.com.vn',
                'password' => StringHelper::hash($this->administratorPassword),
            ])->id,
            'role_id' => $adminRole->id,
            'display_name' => 'Administrator',
        ]);
    }
}
