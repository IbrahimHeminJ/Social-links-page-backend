<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserButton;
use App\Models\ButtonLink;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserButtonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $buttonLinks = ButtonLink::all();

        // Create 50 user buttons (relationships between users and button links)
        // Ensure unique combinations of user_id and button_id
        $userButtonPairs = [];
        $maxAttempts = 1000;
        $attempts = 0;

        while (count($userButtonPairs) < 50 && $attempts < $maxAttempts) {
            $userId = $users->random()->id;
            $buttonId = $buttonLinks->random()->id;
            $pair = $userId . '-' . $buttonId;
            
            if (!in_array($pair, $userButtonPairs)) {
                // Check if this combination already exists in database
                $exists = DB::table('user_buttons')
                    ->where('user_id', $userId)
                    ->where('button_id', $buttonId)
                    ->exists();

                if (!$exists) {
                    $userButtonPairs[] = $pair;
                    UserButton::factory()->create([
                        'user_id' => $userId,
                        'button_id' => $buttonId,
                        'order' => fake()->numberBetween(0, 20),
                    ]);
                }
            }
            $attempts++;
        }
    }
}
