<?php

use App\Enums\ProductStatus;
use App\Http\Resources\Admin\ProductResource;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('[ProductResourceTest]-[001] 商品情報を正しい構造に変換する', function () {
    $product = Product::factory()->create([
        'name' => 'テスト商品',
        'description' => '説明文',
        'price' => 1500,
        'status' => ProductStatus::APPROVED,
    ]);

    $array = (new ProductResource($product))->toArray(request());

    expect($array)->toBe([
        'id' => $product->id,
        'name' => 'テスト商品',
        'description' => '説明文',
        'price' => 1500,
        'status' => [
            'id' => ProductStatus::APPROVED,
            'label' => '承認済み',
        ],
        'created_at' => $product->created_at->isoFormat('YYYY/MM/DD'),
        'updated_at' => $product->updated_at->isoFormat('YYYY/MM/DD'),
    ]);
});

it('[ProductResourceTest]-[002] descriptionがnullの場合もそのまま変換される', function () {
    $product = Product::factory()->create(['description' => null]);

    $array = (new ProductResource($product))->toArray(request());

    expect($array['description'])->toBeNull();
});
