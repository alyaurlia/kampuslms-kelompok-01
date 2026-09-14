<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory
{
    public function definition(): array
    {
        $names = [
            'Basis Data', 'Pemrograman Web', 'Jaringan Komputer',
            'Struktur Data', 'Algoritma dan Pemrograman', 'Sistem Operasi',
        ];

        return [
            'code' => 'SI' . fake()->unique()->numerify('#######'),
            'name' => fake()->randomElement($names),
            'description' => fake('id_ID')->sentence(12),
            'sks' => fake()->numberBetween(2, 4),
            'lecturer_id' => User::factory()->dosen(),
            'status' => 'active',
        ];
    }
}