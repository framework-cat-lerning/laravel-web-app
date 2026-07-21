<?php

use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('[InventoryTest]-[001] fillableはname, product_id, quantity', function () {
    $inventory = new Inventory;

    expect($inventory->getFillable())->toBe(['name', 'product_id', 'quantity']);
});

it('[InventoryTest]-[002] productリレーションで紐づく商品を取得できる', function () {
    $product = Product::factory()->create();
    $inventory = Inventory::factory()->for($product)->create();

    expect($inventory->product)->toBeInstanceOf(Product::class)
        ->and($inventory->product->id)->toBe($product->id);
});

it('[InventoryTest]-[003] ソフトデリートが有効になっている', function () {
    $product = Product::factory()->create();
    $inventory = Inventory::factory()->for($product)->create();

    $inventory->delete();

    expect(Inventory::find($inventory->id))->toBeNull();
    expect(Inventory::withTrashed()->find($inventory->id))->not->toBeNull();
});