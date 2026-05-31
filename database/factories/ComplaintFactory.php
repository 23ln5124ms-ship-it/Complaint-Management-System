<?php

namespace Database\Factories;

use App\Models\Complaint;
use Illuminate\Database\Eloquent\Factories\Factory;

class ComplaintFactory extends Factory
{
    protected $model = Complaint::class;

    public function definition(): array
    {
        return [
            'ticket_number' => 'CMS-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
            'user_id'       => 1,   // overridden by seeder
            'category_id'   => 1,   // overridden by seeder
            'title'         => $this->faker->sentence(6),
            'description'   => $this->faker->paragraphs(2, true),
            'status'        => 'pending',    // overridden by seeder
            'priority'      => 'medium',     // overridden by seeder
            'resolved_at'   => null,
        ];
    }
}