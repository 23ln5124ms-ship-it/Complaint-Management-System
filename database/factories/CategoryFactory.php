<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        // Use a unique name so the slug is always unique too
        $name = $this->faker->unique()->randomElement([
            'Technical Issue', 'Billing & Payment', 'Customer Service',
            'Facilities', 'HR & Policies', 'Other', 'Safety', 'Security',
            'Network', 'Software', 'Hardware', 'Accounting',
        ]);

        return [
            'name'        => $name,
            'slug'        => Str::slug($name),
            'description' => $this->faker->sentence(),
            'color'       => $this->faker->hexColor(),
            'is_active'   => true,
        ];
    }

    public function inactive()
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => false,
        ]);
    }
}