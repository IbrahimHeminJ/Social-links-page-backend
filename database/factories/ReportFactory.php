<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Report;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Report>
 */
class ReportFactory extends Factory
{
    protected $model = Report::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $reportTypes = [
            'spam',
            'harassment',
            'inappropriate_content',
            'fake_account',
            'copyright_violation',
            'privacy_concern',
            'other'
        ];

        return [
            'email_of_reporter' => fake()->safeEmail(),
            'user_id' => User::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->optional()->paragraph(),
            'report_type' => fake()->randomElement($reportTypes),
            'report_status' => fake()->boolean(30), // 30% chance of being resolved
            'handled_by' => null, // Will be set in seeder if needed
            'reason_of_action' => fake()->paragraph(),
        ];
    }
}
