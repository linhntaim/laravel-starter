<?php

use App\Models\Admin;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Utils\StringHelper;

class DefaultSeeder extends Seeder
{
    protected $systemEmail = 'system@dsquare.com.vn';
    protected $systemPassword = ')^KM$bB-W7:Z@8eG';
    protected $superAdminEmail = 'superadmin@dsquare.com.vn';
    protected $superAdminPassword = '3sQUJ8yXc@m#3bx3';
    protected $administratorEmail = 'admin@dsquare.com.vn';
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
                'email' => $this->systemEmail,
                'password' => StringHelper::hash($this->systemPassword),
            ])->id,
            'role_id' => $systemRole->id,
            'display_name' => 'System',
        ]);
        $this->output()->writeln(sprintf('System: %s / %s', $this->systemEmail, $this->systemPassword));

        Admin::query()->create([
            'user_id' => User::query()->create([
                'email' => $this->superAdminEmail,
                'password' => StringHelper::hash($this->superAdminPassword),
            ])->id,
            'role_id' => $superAdminRole->id,
            'display_name' => 'Super Administrator',
        ]);
        $this->output()->writeln(sprintf('Super admin: %s / %s', $this->superAdminEmail, $this->superAdminPassword));

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
                'email' => $this->administratorEmail,
                'password' => StringHelper::hash($this->administratorPassword),
            ])->id,
            'role_id' => $adminRole->id,
            'display_name' => 'Administrator',
        ]);
        $this->output()->writeln(sprintf('Admin: %s / %s', $this->administratorEmail, $this->administratorPassword));
    }
}
