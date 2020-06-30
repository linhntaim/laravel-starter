<?php

use App\Models\Admin;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Utils\ConfigHelper;
use App\Utils\StringHelper;

class DefaultSeeder extends Seeder
{
    public function run()
    {
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

        $beOwner = Permission::query()->create([
            'name' => 'be-owner',
            'display_name' => 'Be owner',
            'description' => 'Be owner',
        ]);
        $ownerRole = Role::query()->create([
            'name' => 'owner',
            'display_name' => 'Owner',
            'description' => 'Owner of the system',
        ]);
        $ownerRole->permissions()->attach([
            $beOwner->id,
        ]);

        Admin::query()->create([
            'user_id' => User::query()->create([
                'display_name' => 'System',
                'email' => 'system@dsquare.com.vn',
                'password' => StringHelper::hash(')^KM$bB-W7:Z@8eG'),
                'url_avatar' => ConfigHelper::defaultAvatarUrl(),
            ])->id,
            'role_id' => $systemRole->id,
        ]);

        Admin::query()->create([
            'user_id' => User::query()->create([
                'display_name' => 'Owner',
                'email' => 'owner@dsquare.com.vn',
                'password' => StringHelper::hash('3sQUJ8yXc@m#3bx3'),
                'url_avatar' => ConfigHelper::defaultAvatarUrl(),
            ])->id,
            'role_id' => $ownerRole->id,
        ]);
    }
}
