<?php

namespace Database\Factories;

use App\Models\Priority;
use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
            'id' => fake()->unique()->numberBetween(1, 1000),
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'status_id' => Status::inRandomOrder()->first()->id,
            'priority_id' => Priority::inRandomOrder()->first()->id,
            'user_id' => User::inRandomOrder()->first()->id,
        ];
    }
}
