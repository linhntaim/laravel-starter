<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;

class BenchRoleSeeder extends Seeder
{
    public function run()
    {
        Role::factory()
            ->has(Permission::factory()->count(3), 'permissions')
            ->count(100000)
            ->create();
    }
}
