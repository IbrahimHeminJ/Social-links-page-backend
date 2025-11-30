<?php

namespace Database\Factories;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tag>
 */
class TagFactory extends Factory
{
    protected $model = Tag::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tagCategories = [
            'technology', 'design', 'business', 'education', 'health',
            'fitness', 'music', 'art', 'photography', 'travel',
            'food', 'fashion', 'sports', 'gaming', 'programming',
            'web_development', 'mobile_apps', 'ai', 'marketing', 'finance',
            'entrepreneur', 'blogger', 'influencer', 'creator', 'developer',
            'designer', 'writer', 'photographer', 'artist', 'teacher',
            'student', 'engineer', 'consultant', 'coach', 'therapist',
            'doctor', 'lawyer', 'freelancer', 'startup', 'nonprofit',
            'volunteer', 'activist', 'researcher', 'scientist', 'architect',
            'musician', 'actor', 'director', 'producer', 'filmmaker'
        ];

        $baseTag = fake()->randomElement($tagCategories);
        $suffix = fake()->unique()->numberBetween(1, 10000);
        
        return [
            'tag' => $baseTag . '_' . $suffix,
        ];
    }
}
