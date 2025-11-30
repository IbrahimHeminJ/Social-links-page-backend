<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Report;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Please run UserSeeder first.');
            return;
        }

        // Create 50 reports (linking to random users)
        Report::factory(50)->create([
            'user_id' => fn() => $users->random()->id,
        ])->each(function ($report) use ($users) {
            // Randomly assign a handler to some reports (30% chance)
            if (fake()->boolean(30)) {
                $report->update([
                    'handled_by' => $users->random()->id,
                ]);
            }
        });
    }
}
