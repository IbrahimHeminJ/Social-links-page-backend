<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserPage;
use App\Models\ThemePreset;
use Illuminate\Database\Seeder;

class UserPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $themePresets = ThemePreset::all();

        // Create user pages (one per user, since user_id is unique)
        foreach ($users as $user) {
            UserPage::factory()->create([
                'user_id' => $user->id,
                'theme_id' => $themePresets->random()->id,
            ]);
        }
    }
}
