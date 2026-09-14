<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake('id_ID')->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => 'password', // otomatis di-hash lewat casts()
            'role' => 'mahasiswa',
            'nim_nip' => fake()->unique()->numerify('##########'),
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => [
            'role' => 'admin',
            'nim_nip' => null,
        ]);
    }

    public function dosen(): static
    {
        return $this->state(fn () => [
            'role' => 'dosen',
            'nim_nip' => fake()->unique()->numerify('####################'), // NIP lebih panjang
        ]);
    }

    public function mahasiswa(): static
    {
        return $this->state(fn () => [
            'role' => 'mahasiswa',
            'nim_nip' => fake()->unique()->numerify('##########'),
        ]);
    }
}
