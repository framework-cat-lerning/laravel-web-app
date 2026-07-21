<?php

use App\Http\Resources\Shop\ProductConsumptionResource;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('[ProductConsumptionResourceTest]-[001] 在庫が存在する場合はその数量を返す', function () {
    $product = Product::factory()->create();
    Inventory::factory()->for($product)->create(['quantity' => 7]);

    $array = (new ProductConsumptionResource($product->fresh()))->toArray(request());

    expect($array['quantity'])->toBe(7);
});

it('[ProductConsumptionResourceTest]-[002] 在庫が存在しない場合は0を返す', function () {
    $product = Product::factory()->create();

    $array = (new ProductConsumptionResource($product))->toArray(request());

    expect($array['quantity'])->toBe(0);
});
