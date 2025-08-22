<?php

namespace Database\Seeders;

use App\Models\Priority;
use App\PriorityLevel;
use Illuminate\Database\Seeder;

class PrioritySeeder extends Seeder
{
    public function run(): void
    {
        foreach (PriorityLevel::cases() as $priority) {
            Priority::updateOrCreate(
                ['name' => $priority->value],
                ['color' => $priority->color()]
            );
        }
    }
}
