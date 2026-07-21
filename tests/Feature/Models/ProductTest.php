<?php

use App\Enums\ProductStatus;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('[ProductTest]-[001] statusはProductStatus Enumにキャストされる', function () {
    $product = Product::factory()->create(['status' => ProductStatus::APPROVED]);

    expect($product->fresh()->status)->toBeInstanceOf(ProductStatus::class)
        ->and($product->fresh()->status)->toBe(ProductStatus::APPROVED);
});

it('[ProductTest]-[002] fillableはname, description, price, statusのみ', function () {
    $product = new Product;

    expect($product->getFillable())->toBe(['name', 'description', 'price', 'status']);
});

it('[ProductTest]-[003] statusはhiddenに設定されている', function () {
    $product = Product::factory()->create(['status' => ProductStatus::APPROVED]);

    $array = $product->toArray();

    expect($array)->not->toHaveKey('status');
});

it('[ProductTest]-[004] inventoryリレーションで自身の在庫を取得できる', function () {
    $product = Product::factory()->create();
    $inventory = Inventory::factory()->for($product)->create();

    expect($product->inventory)->toBeInstanceOf(Inventory::class)
        ->and($product->inventory->id)->toBe($inventory->id);
});

it('[ProductTest]-[005] consumptionLogsリレーションで購入履歴を取得できる', function () {
    $product = Product::factory()->create();
    $user = User::factory()->create();
    $product->consumptionLogs()->create([
        'consumption_at' => now(),
        'quantity' => 1,
        'user_id' => $user->id,
    ]);

    expect($product->consumptionLogs)->toHaveCount(1);
});

it('[ProductTest]-[006] scopeIsApprovalは承認済み商品のみ取得する', function () {
    Product::factory()->create(['status' => ProductStatus::PENDING]);
    $approved = Product::factory()->create(['status' => ProductStatus::APPROVED]);

    $result = Product::isApproval()->get();

    expect($result)->toHaveCount(1)
        ->and($result->first()->id)->toBe($approved->id);
});

it('[ProductTest]-[007] scopeIsInvetoryは在庫数が1以上の商品のみ取得する', function () {
    $withStock = Product::factory()->create();
    Inventory::factory()->for($withStock)->create(['quantity' => 5]);

    $withoutStock = Product::factory()->create();
    Inventory::factory()->for($withoutStock)->create(['quantity' => 0]);

    $noInventory = Product::factory()->create();

    $result = Product::isInvetory()->get();

    expect($result)->toHaveCount(1)
        ->and($result->first()->id)->toBe($withStock->id);
});

it('[ProductTest]-[008] ソフトデリートが有効になっている', function () {
    $product = Product::factory()->create();

    $product->delete();

    expect(Product::find($product->id))->toBeNull();
    expect(Product::withTrashed()->find($product->id))->not->toBeNull();
});
