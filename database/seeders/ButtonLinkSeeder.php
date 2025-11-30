<?php

namespace Database\Seeders;

use App\Models\ButtonLink;
use Illuminate\Database\Seeder;

class ButtonLinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 50 button links
        ButtonLink::factory(50)->create();
    }
}
