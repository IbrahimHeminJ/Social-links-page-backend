<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,              // Create 10 users with role 'user'
            ThemePresetSeeder::class,       // Create 50 theme presets
            UserPageSeeder::class,          // Create user pages (one per user)
            ButtonLinkSeeder::class,        // Create 50 button links
            UserButtonSeeder::class,        // Create 50 user buttons
            DomainBlockSeeder::class,       // Create 50 domain blocks
            ReportSeeder::class,            // Create 50 reports
            TagSeeder::class,               // Create 50 tags
            UserTagSeeder::class,           // Create user tags (one per user)
        ]);
    }
}
