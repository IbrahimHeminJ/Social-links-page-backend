<?php

namespace Database\Factories;

use App\Models\DomainBlock;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DomainBlock>
 */
class DomainBlockFactory extends Factory
{
    protected $model = DomainBlock::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'domain' => fake()->unique()->domainName(),
        ];
    }
}
