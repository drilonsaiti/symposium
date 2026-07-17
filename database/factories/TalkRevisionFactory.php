<?php

namespace Database\Factories;

use App\Models\Talk;
use App\Models\TalkRevision;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TalkRevision>
 */
class TalkRevisionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'talk_id' => Talk::factory(),
            'abstract' => $this->faker->paragraphs(3, true),
        ];
    }
}
