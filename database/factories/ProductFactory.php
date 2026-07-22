<?php

namespace Database\Factories;

use App\Enums\ProductStatus;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->optional()->sentence(),
            'price' => $this->faker->numberBetween(100, 10000),
            'status' => ProductStatus::PENDING->value,
            'request_user_id' => null,
        ];
    }

    /**
     * 申請者ユーザーを紐付ける場合の状態
     */
    public function withRequestUser(?User $user = null): static
    {
        return $this->state(fn(array $attributes) => [
            'request_user_id' => ! empty($user) ? $user->id : User::factory(),
        ]);
    }

    /**
     * 商品の申請済み状態
     */
    public function approved(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 2,
        ]);
    }
}
