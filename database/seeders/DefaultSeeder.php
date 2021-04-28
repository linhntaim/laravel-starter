<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Utils\ConfigHelper;
use App\Utils\PasswordGenerator;
use App\Vendors\Illuminate\Support\Facades\App;
use App\Vendors\Illuminate\Support\Str;

class DefaultSeeder extends Seeder
{
    protected $systemEmail = 'system@linhntaim.com';
    protected $systemPassword = '123123132';
    protected $superAdminEmail = 'superadmin@linhntaim.com';
    protected $superAdminPassword = '123123132';
    protected $administratorEmail = 'admin@linhntaim.com';
    protected $administratorPassword = '123123132';

    public function run()
    {
        // System users
        $beSystem = Permission::query()->updateOrCreate([
            'name' => Permission::BE_SYSTEM,
        ], [
            'display_name' => 'Be system',
            'description' => 'Be system',
        ]);
        $systemRole = Role::query()->updateOrCreate([
            'name' => Role::SYSTEM,
        ], [
            'display_name' => 'System',
            'description' => 'Representation of the system',
        ]);
        $systemRole->permissions()->syncWithoutDetaching([
            $beSystem->id,
        ]);

        $beSuperAdmin = Permission::query()->updateOrCreate([
            'name' => Permission::BE_SUPER_ADMIN,
        ], [
            'display_name' => 'Be super-admin',
            'description' => 'Be super-admin',
        ]);
        $superAdminRole = Role::query()->updateOrCreate([
            'name' => Role::SUPER_ADMIN,
        ], [
            'display_name' => 'Super-admin',
            'description' => 'Super-admin of the system',
        ]);
        $superAdminRole->permissions()->syncWithoutDetaching([
            $beSuperAdmin->id,
        ]);

        if (ConfigHelper::get('impersonated_by_admin')) {
            $impersonate = Permission::query()->updateOrCreate([
                'name' => Permission::IMPERSONATE,
            ], [
                'display_name' => 'Impersonate',
                'description' => 'Can impersonate',
            ]);
            $superAdminRole->permissions()->syncWithoutDetaching([
                $impersonate->id,
            ]);
        }

        $systemPassword = App::runningInProduction() ? PasswordGenerator::random() : $this->systemPassword;
        $systemId = User::query()->updateOrCreate([
            'email' => $this->systemEmail,
        ], [
            'username' => strtok($this->systemEmail, '@'),
            'password' => Str::hash($systemPassword),
        ])->id;
        Admin::query()->updateOrCreate([
            'user_id' => $systemId,
        ], [
            'role_id' => $systemRole->id,
            'display_name' => 'System',
        ]);
        $this->output()->writeln(sprintf('System: %s / %s', $this->systemEmail, $systemPassword));

        $superAdminPassword = App::runningInProduction() ? PasswordGenerator::random() : $this->superAdminPassword;
        $superAdminId = User::query()->updateOrCreate([
            'email' => $this->superAdminEmail,
        ], [
            'username' => strtok($this->superAdminEmail, '@'),
            'password' => Str::hash($superAdminPassword),
        ])->id;
        Admin::query()->updateOrCreate([
            'user_id' => $superAdminId,
        ], [
            'role_id' => $superAdminRole->id,
            'display_name' => 'Super Administrator',
        ]);
        $this->output()->writeln(sprintf('Super admin: %s / %s', $this->superAdminEmail, $superAdminPassword));

        // Normal administrative users
        $manageRoles = Permission::query()->updateOrCreate([
            'name' => Permission::ROLE_MANAGE,
        ], [
            'display_name' => 'Manage roles',
            'description' => 'Manage roles',
        ]);
        $manageAdmins = Permission::query()->updateOrCreate([
            'name' => Permission::ADMIN_MANAGE,
        ], [
            'display_name' => 'Manage admins',
            'description' => 'Manage admins',
        ]);

        $adminRole = Role::query()->updateOrCreate([
            'name' => Role::ADMIN,
        ], [
            'display_name' => 'Admin',
            'description' => 'Admin',
        ]);
        $adminRole->permissions()->syncWithoutDetaching([
            $manageRoles->id,
            $manageAdmins->id,
        ]);

        if (ConfigHelper::get('activity_log_enabled')) {
            $manageActivityLogs = Permission::query()->updateOrCreate([
                'name' => Permission::ACTIVITY_MANAGE,
            ], [
                'display_name' => 'Manage activity logs',
                'description' => 'Manage activity logs',
            ]);
            $adminRole->permissions()->syncWithoutDetaching([
                $manageActivityLogs->id,
            ]);
        }

        $adminPassword = App::runningInProduction() ? PasswordGenerator::random() : $this->administratorPassword;
        $adminId = User::query()->updateOrCreate([
            'email' => $this->administratorEmail,
        ], [
            'username' => strtok($this->administratorEmail, '@'),
            'password' => Str::hash($adminPassword),
        ])->id;
        Admin::query()->updateOrCreate([
            'user_id' => $adminId,
        ], [
            'role_id' => $adminRole->id,
            'display_name' => 'Administrator',
        ]);
        $this->output()->writeln(sprintf('Admin: %s / %s', $this->administratorEmail, $adminPassword));
    }
}
