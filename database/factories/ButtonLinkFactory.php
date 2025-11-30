<?php

namespace Database\Factories;

use App\Models\ButtonLink;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ButtonLink>
 */
class ButtonLinkFactory extends Factory
{
    protected $model = ButtonLink::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $socialPlatforms = [
            'Facebook', 'Twitter', 'Instagram', 'LinkedIn', 'YouTube',
            'TikTok', 'Snapchat', 'Pinterest', 'GitHub', 'Behance',
            'Dribbble', 'Medium', 'Reddit', 'Discord', 'Telegram',
            'WhatsApp', 'Spotify', 'Apple Music', 'SoundCloud', 'Twitch'
        ];

        $iconNames = [
            'facebook', 'twitter', 'instagram', 'linkedin', 'youtube',
            'tiktok', 'snapchat', 'pinterest', 'github', 'behance',
            'dribbble', 'medium', 'reddit', 'discord', 'telegram',
            'whatsapp', 'spotify', 'music', 'soundcloud', 'twitch'
        ];

        $index = fake()->numberBetween(0, count($socialPlatforms) - 1);

        return [
            'title' => $socialPlatforms[$index],
            'description' => fake()->optional()->sentence(),
            'icon' => $iconNames[$index],
            'link' => fake()->url(),
            'no_clicks' => fake()->numberBetween(0, 10000),
            'is_visible' => fake()->boolean(90), // 90% chance of being visible
        ];
    }
}
