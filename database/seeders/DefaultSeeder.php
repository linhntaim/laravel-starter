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
use App\Utils\Helper;
use App\Utils\PasswordGenerator;
use App\Utils\StringHelper;

class DefaultSeeder extends Seeder
{
    protected $systemEmail = 'system@dsquare.com.vn';
    protected $systemPassword = 'Gbu@190708';
    protected $superAdminEmail = 'superadmin@dsquare.com.vn';
    protected $superAdminPassword = 'Gbu@190708';
    protected $administratorEmail = 'admin@dsquare.com.vn';
    protected $administratorPassword = 'Gbu@190708';

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

        $systemPassword = Helper::runInProductionMode() ? PasswordGenerator::random() : $this->systemPassword;
        $systemId = User::query()->updateOrCreate([
            'email' => $this->systemEmail,
        ], [
            'username' => strtok($this->systemEmail, '@'),
            'password' => StringHelper::hash($systemPassword),
        ])->id;
        Admin::query()->updateOrCreate([
            'user_id' => $systemId,
        ], [
            'role_id' => $systemRole->id,
            'display_name' => 'System',
        ]);
        $this->output()->writeln(sprintf('System: %s / %s', $this->systemEmail, $systemPassword));

        $superAdminPassword = Helper::runInProductionMode() ? PasswordGenerator::random() : $this->superAdminPassword;
        $superAdminId = User::query()->updateOrCreate([
            'email' => $this->superAdminEmail,
        ], [
            'username' => strtok($this->superAdminEmail, '@'),
            'password' => StringHelper::hash($superAdminPassword),
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

        $adminPassword = Helper::runInProductionMode() ? PasswordGenerator::random() : $this->administratorPassword;
        $adminId = User::query()->updateOrCreate([
            'email' => $this->administratorEmail,
        ], [
            'username' => strtok($this->administratorEmail, '@'),
            'password' => StringHelper::hash($adminPassword),
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
