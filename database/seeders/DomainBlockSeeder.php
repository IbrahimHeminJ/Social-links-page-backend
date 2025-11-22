<?php

namespace Database\Seeders;

use App\Models\DomainBlock;
use Illuminate\Database\Seeder;

class DomainBlockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 50 domain blocks
        DomainBlock::factory(50)->create();
    }
}
