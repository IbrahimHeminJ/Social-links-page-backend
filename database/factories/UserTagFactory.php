<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserTag;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserTag>
 */
class UserTagFactory extends Factory
{
    protected $model = UserTag::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'tag_id' => Tag::factory(),
        ];
    }
}
