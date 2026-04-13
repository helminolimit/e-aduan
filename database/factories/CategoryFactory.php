<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Jalan Raya & Lebuh Raya',
                'Kebersihan & Pengurusan Sisa',
                'Infrastruktur & Pembetungan',
                'Taman & Landskap',
                'Bangunan & Premis',
                'Alam Sekitar & Pencemaran',
                'Lampu Jalan & Utiliti',
                'Keselamatan & Jenayah',
            ]),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
