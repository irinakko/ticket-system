<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Role as AppRole;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (AppRole::cases() as $role) {
            Role::updateOrCreate(
                ['name' => $role->value]
            );
        }
    }
}
