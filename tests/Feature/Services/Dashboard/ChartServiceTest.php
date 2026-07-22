<?php

use App\Data\Chart\ProductData;
use App\Enums\ProductStatus;
use App\Models\ConsumptionLog;
use App\Models\Product;
use App\Models\User;
use App\Services\Dashboard\ChartService;
use Carbon\CarbonImmutable;

beforeEach(function () {
    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-07-22'));
});

afterEach(function () {
    CarbonImmutable::setTestNow();
});

it('[ChartServiceTest]-[001] 承認済み商品ごとにProductDataが返却される', function () {
    $service = new ChartService;
    Product::factory()->count(2)->create(['status' => ProductStatus::APPROVED]);
    Product::factory()->create(['status' => ProductStatus::PENDING]); // 申請中商品（除外される想定）

    $result = $service->getProductData();

    expect($result)->toHaveCount(2)
        ->and($result[0])->toBeInstanceOf(ProductData::class);
});

it('[ChartServiceTest]-[002] 直近7日間のquantityが日別に合算される', function () {
    $service = new ChartService;
    $product = Product::factory()->create(['status' => ProductStatus::APPROVED]);
    $user = User::factory()->create();

    // 対象期間内：2026-07-22 の 7日前 = 2026-07-16 〜 2026-07-22
    ConsumptionLog::factory()->create([
        'product_id' => $product->id,
        'user_id' => $user->id,
        'quantity' => 5,
        'consumption_at' => CarbonImmutable::parse('2026-07-20 10:00:00'),
    ]);

    ConsumptionLog::factory()->create([
        'product_id' => $product->id,
        'user_id' => $user->id,
        'quantity' => 3,
        'consumption_at' => CarbonImmutable::parse('2026-07-20 15:00:00'),
    ]);

    $result = $service->getProductData();

    $productData = collect($result)->firstWhere('title', $product->name);

    expect($productData)->not->toBeNull()
        ->and($productData->value)->toBe('8') // 5 + 3
        ->and($productData->data)->toHaveCount(7); // 7日分の配列
});

it('[ChartServiceTest]-[003] 7日間の範囲外の消費ログは集計に含まれない', function () {
    $service = new ChartService;
    $product = Product::factory()->create(['status' => ProductStatus::APPROVED]);
    $user = User::factory()->create();

    // 範囲外（8日前）
    ConsumptionLog::factory()->create([
        'product_id' => $product->id,
        'user_id' => $user->id,
        'quantity' => 100,
        'consumption_at' => CarbonImmutable::parse('2026-07-14 10:00:00'),
    ]);

    $result = $service->getProductData();

    $productData = collect($result)->firstWhere('title', $product->name);

    expect($productData->value)->toBe('0');
});

it('[ChartServiceTest]-[004] 消費ログが存在しない日は0で補完される', function () {
    $service = new ChartService;
    $product = Product::factory()->create(['status' => ProductStatus::APPROVED]);
    $user = User::factory()->create();

    // 1日分だけデータを作成
    ConsumptionLog::factory()->create([
        'product_id' => $product->id,
        'user_id' => $user->id,
        'quantity' => 10,
        'consumption_at' => CarbonImmutable::parse('2026-07-22 09:00:00'),
    ]);

    $result = $service->getProductData();

    $productData = collect($result)->firstWhere('title', $product->name);

    expect($productData->data)->toHaveCount(7)
        ->and(array_sum($productData->data))->toBe(10)
        ->and(collect($productData->data)->filter(fn ($v) => $v === 0))->toHaveCount(6);
});

it('[ChartServiceTest]-[005] 論理削除された消費ログは集計から除外される', function () {
    $service = new ChartService;
    $product = Product::factory()->create(['status' => ProductStatus::APPROVED]);
    $user = User::factory()->create();

    $log = ConsumptionLog::factory()->create([
        'product_id' => $product->id,
        'user_id' => $user->id,
        'quantity' => 20,
        'consumption_at' => CarbonImmutable::parse('2026-07-22 09:00:00'),
    ]);

    $log->delete(); // 論理削除

    $result = $service->getProductData();

    $productData = collect($result)->firstWhere('title', $product->name);

    expect($productData->value)->toBe('0');
});

it('[ChartServiceTest]-[006] 後半期間の消費量が前半より多い場合trendがupになる', function () {
    $service = new ChartService;
    $product = Product::factory()->create(['status' => ProductStatus::APPROVED]);
    $user = User::factory()->create();

    // 前半（7/16〜7/18）: 少ない
    ConsumptionLog::factory()->create([
        'product_id' => $product->id,
        'user_id' => $user->id,
        'quantity' => 1,
        'consumption_at' => CarbonImmutable::parse('2026-07-16 09:00:00'),
    ]);

    // 後半（7/20〜7/22）: 多い
    ConsumptionLog::factory()->create([
        'product_id' => $product->id,
        'user_id' => $user->id,
        'quantity' => 50,
        'consumption_at' => CarbonImmutable::parse('2026-07-22 09:00:00'),
    ]);

    $result = $service->getProductData();

    $productData = collect($result)->firstWhere('title', $product->name);

    expect($productData->trend)->toBe('up');
});

it('[ChartServiceTest]-[007] 承認済み商品が存在しない場合は空配列が返却される', function () {
    $service = new ChartService;
    Product::factory()->create(['status' => ProductStatus::PENDING]);

    $result = $service->getProductData();

    expect($result)->toBe([]);
});
