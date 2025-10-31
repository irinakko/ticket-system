<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Ticket;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Ticket::factory(50)->create();

        $this->call(PrioritySeeder::class);
        $this->call(RoleSeeder::class);
    }
}
