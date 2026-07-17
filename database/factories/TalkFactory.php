<?php

namespace Database\Factories;

use App\Enum\TalkType;
use App\Models\Talk;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Talk>
 */
class TalkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(),
            'type' => fake()->randomElement(TalkType::cases())->value,
            'length' => rand(15, 60),
            'organizer_notes' => fake()->paragraph(),
        ];
    }
}
