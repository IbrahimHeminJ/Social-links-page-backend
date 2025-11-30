<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserButton;
use App\Models\ButtonLink;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserButton>
 */
class UserButtonFactory extends Factory
{
    protected $model = UserButton::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'button_id' => ButtonLink::factory(),
            'order' => fake()->numberBetween(0, 20),
        ];
    }
}
