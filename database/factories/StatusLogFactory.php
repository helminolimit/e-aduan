<?php

namespace Database\Factories;

use App\Models\Complaint;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StatusLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'complaint_id' => Complaint::factory(),
            'changed_by'   => User::factory()->officer(),
            'old_status'   => 'pending',
            'new_status'   => 'in_review',
            'remarks'      => null,
        ];
    }
}
