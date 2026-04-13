<?php

namespace Database\Factories;

use App\Models\Complaint;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'      => User::factory(),
            'complaint_id' => Complaint::factory(),
            'type'         => fake()->randomElement(['status_updated', 'comment_added', 'officer_assigned']),
            'message'      => fake()->sentence(),
            'is_read'      => fake()->boolean(40),
        ];
    }
}
