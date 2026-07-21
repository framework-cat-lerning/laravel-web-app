<?php

use App\Http\Resources\Staff\ProductInventoryResource;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('[ProductInventoryResourceTest]-[001] 在庫が存在する場合はその数量を返す', function () {
    $product = Product::factory()->create();
    Inventory::factory()->for($product)->create(['quantity' => 42]);

    $array = (new ProductInventoryResource($product->fresh()))->toArray(request());

    expect($array['quantity'])->toBe(42);
});

it('[ProductInventoryResourceTest]-[002] 在庫が存在しない場合は0を返す', function () {
    $product = Product::factory()->create();

    $array = (new ProductInventoryResource($product))->toArray(request());

    expect($array['quantity'])->toBe(0);
});

it('[ProductInventoryResourceTest]-[003] 商品の基本情報も含まれる', function () {
    $product = Product::factory()->create([
        'name' => '在庫テスト商品',
        'price' => 800,
    ]);

    $array = (new ProductInventoryResource($product))->toArray(request());

    expect($array['id'])->toBe($product->id)
        ->and($array['name'])->toBe('在庫テスト商品')
        ->and($array['price'])->toBe(800);
});
