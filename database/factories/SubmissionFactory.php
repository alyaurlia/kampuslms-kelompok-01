<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SubmissionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'assignment_id' => null, // diisi manual di seeder
            'user_id' => null,       // diisi manual di seeder
            'file_path' => 'submissions/' . fake()->uuid() . '.pdf',
            'original_name' => 'tugas_' . fake()->word() . '.pdf',
            'file_size' => fake()->numberBetween(50_000, 3_000_000),
            'note' => fake()->boolean(30) ? fake('id_ID')->sentence() : null,
            'submitted_at' => now(),
            'is_late' => false,
        ];
    }
}