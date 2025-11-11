<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Event;

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
            'type' => fake()->randomElement(['VIP', 'Standard', 'Economy', 'Premium']),
            'price' => fake()->randomFloat(2, 10, 500),
            'quantity' => fake()->numberBetween(50, 500),
            'event_id' => Event::factory(),
        ];
    }
}
