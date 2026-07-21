<?php

use App\Models\ConsumptionLog;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('[ConsumptionLogTest]-[001] fillableはproduct_id, consumption_at, quantity, user_id', function () {
    $consumptionLog = new ConsumptionLog;

    expect($consumptionLog->getFillable())->toBe(['product_id', 'consumption_at', 'quantity', 'user_id']);
});

it('[ConsumptionLogTest]-[002] 必要な属性を保存できる', function () {
    $product = Product::factory()->create();
    $user = User::factory()->create();

    $consumptionLog = ConsumptionLog::factory()->for($product)->for($user)->create([
        'quantity' => 3,
    ]);

    $this->assertDatabaseHas('consumption_logs', [
        'id' => $consumptionLog->id,
        'product_id' => $product->id,
        'user_id' => $user->id,
        'quantity' => 3,
    ]);
});