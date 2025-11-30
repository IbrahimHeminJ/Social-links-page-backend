<?php

namespace Database\Seeders;

use App\Models\ThemePreset;
use Illuminate\Database\Seeder;

class ThemePresetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 50 theme presets
        ThemePreset::factory(50)->create();
    }
}
