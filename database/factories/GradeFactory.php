<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class GradeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'submission_id' => null, // diisi manual di seeder
            'graded_by' => null,     // diisi manual di seeder
            'score' => fake()->randomFloat(2, 60, 100),
            'feedback' => fake('id_ID')->sentence(8),
            'graded_at' => now(),
        ];
    }
}