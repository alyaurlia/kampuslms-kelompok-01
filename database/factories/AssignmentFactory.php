<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssignmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'created_by' => null, // diisi manual di seeder = lecturer_id course tersebut
            'title' => 'Tugas ' . fake('id_ID')->word(),
            'instructions' => fake('id_ID')->paragraph(),
            'due_at' => fake()->dateTimeBetween('-2 weeks', '+2 weeks'),
            'max_score' => 100,
            'allow_late' => true,
            'status' => 'published',
        ];
    }

    public function past(): static
    {
        return $this->state(fn () => [
            'due_at' => fake()->dateTimeBetween('-3 weeks', '-1 week'),
            'status' => 'published',
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn () => [
            'status' => 'draft',
            'due_at' => fake()->dateTimeBetween('+1 week', '+4 weeks'),
        ]);
    }
}