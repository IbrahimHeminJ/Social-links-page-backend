<?php

namespace Database\Factories;

use App\Models\ThemePreset;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ThemePreset>
 */
class ThemePresetFactory extends Factory
{
    protected $model = ThemePreset::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $baseThemes = [
            'light', 'dark', 'colorful', 'minimal', 'professional',
            'modern', 'vintage', 'neon', 'gradient', 'pastel',
            'monochrome', 'nature', 'tech', 'artistic', 'elegant',
            'bold', 'subtle', 'warm', 'cool', 'bright',
            'muted', 'vibrant', 'sophisticated', 'casual', 'corporate',
            'creative', 'sleek', 'rustic', 'urban', 'tropical',
            'winter', 'summer', 'autumn', 'spring', 'ocean',
            'forest', 'desert', 'mountain', 'city', 'space',
            'retro', 'futuristic', 'classic', 'contemporary', 'experimental',
            'serene', 'energetic', 'mysterious', 'friendly', 'professional_blue'
        ];

        $baseTheme = fake()->randomElement($baseThemes);
        $suffix = fake()->unique()->numberBetween(1, 10000);
        
        return [
            'theme_type' => $baseTheme . '_' . $suffix,
        ];
    }
}
