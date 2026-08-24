<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'              => fake()->name(),
            'email'             => fake()->unique()->safeEmail(),
            'password'          => bcrypt('password'),
            'role'              => 'staff',
            'status'            => 'active',
            'remember_token'    => Str::random(10),
        ];
    }
}
