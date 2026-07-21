<?php

namespace Database\Factories;

use App\Models\ConsumptionLog;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ConsumptionLog>
 */
class ConsumptionLogFactory extends Factory
{
    protected $model = ConsumptionLog::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'consumption_at' => $this->faker->dateTimeBetween('-1 month'),
            'quantity' => $this->faker->numberBetween(1, 10),
            'user_id' => User::factory(),
        ];
    }
}