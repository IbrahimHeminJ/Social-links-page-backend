<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserPage;
use App\Models\ThemePreset;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserPage>
 */
class UserPageFactory extends Factory
{
    protected $model = UserPage::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'theme_id' => ThemePreset::factory(),
        ];
    }
}
