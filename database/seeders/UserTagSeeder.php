<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserTag;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $tags = Tag::all();

        if ($users->isEmpty() || $tags->isEmpty()) {
            $this->command->warn('No users or tags found. Please run UserSeeder and TagSeeder first.');
            return;
        }

        // Create user tags (one per user since user_id is unique in user_tags table)
        foreach ($users as $user) {
            // Check if user already has a tag
            $exists = DB::table('user_tags')->where('user_id', $user->id)->exists();
            
            if (!$exists) {
                UserTag::factory()->create([
                    'user_id' => $user->id,
                    'tag_id' => $tags->random()->id,
                ]);
            }
        }
    }
}
