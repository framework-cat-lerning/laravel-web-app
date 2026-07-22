<?php

use App\Data\Log\ConsumptionLogData;
use App\Enums\ProductStatus;
use App\Models\ConsumptionLog;
use App\Models\Product;
use App\Models\User;
use App\Services\Dashboard\LogTableService;
use Carbon\CarbonImmutable;

it('[LogTableServiceTest]-[001] columnsにMUI DataGrid用の列定義が返却される', function () {
    $service = new LogTableService;
    $result = $service->getConsumptionLogTableData();

    expect($result['columns'])->toBe(ConsumptionLogData::GetColumns());
});

it('[LogTableServiceTest]-[002] rowsに消費ログの件数分のConsumptionLogDataが返却される', function () {
    $service = new LogTableService;
    $product = Product::factory()->create(['status' => ProductStatus::APPROVED]);
    $user = User::factory()->create();

    ConsumptionLog::factory()->count(3)->create([
        'product_id' => $product->id,
        'user_id' => $user->id,
    ]);

    $result = $service->getConsumptionLogTableData();

    expect($result['rows'])->toHaveCount(3)
        ->and($result['rows'][0])->toBeInstanceOf(ConsumptionLogData::class);
});

it('[LogTableServiceTest]-[003] rowsは消費日時の降順で並んでいる', function () {
    $service = new LogTableService;
    $product = Product::factory()->create(['status' => ProductStatus::APPROVED]);
    $user = User::factory()->create();

    $old = ConsumptionLog::factory()->create([
        'product_id' => $product->id,
        'user_id' => $user->id,
        'consumption_at' => CarbonImmutable::parse('2026-07-01 10:00:00'),
    ]);

    $new = ConsumptionLog::factory()->create([
        'product_id' => $product->id,
        'user_id' => $user->id,
        'consumption_at' => CarbonImmutable::parse('2026-07-20 10:00:00'),
    ]);

    $result = $service->getConsumptionLogTableData();

    expect($result['rows'][0]->id)->toBe($new->id)
        ->and($result['rows'][1]->id)->toBe($old->id);
});

it('[LogTableServiceTest]-[004] idと商品名と合計金額が正しく設定される', function () {
    $service = new LogTableService;
    $product = Product::factory()->create([
        'status' => ProductStatus::APPROVED,
        'name' => 'テスト商品',
        'price' => 500,
    ]);
    $user = User::factory()->create();

    $log = ConsumptionLog::factory()->create([
        'product_id' => $product->id,
        'user_id' => $user->id,
        'quantity' => 3,
    ]);

    $result = $service->getConsumptionLogTableData();

    expect($result['rows'][0]->id)->toBe($log->id)
        ->and($result['rows'][0]->product_name)->toBe('テスト商品')
        ->and($result['rows'][0]->quantity)->toBe(3)
        ->and($result['rows'][0]->total_amount)->toBe(1500); // 500 * 3
});

it('[LogTableServiceTest]-[005] 消費日時が文字列としてフォーマットされる', function () {
    $service = new LogTableService;
    $product = Product::factory()->create(['status' => ProductStatus::APPROVED]);
    $user = User::factory()->create();

    ConsumptionLog::factory()->create([
        'product_id' => $product->id,
        'user_id' => $user->id,
        'consumption_at' => CarbonImmutable::parse('2026-07-20 10:30:00'),
    ]);

    $result = $service->getConsumptionLogTableData();

    expect($result['rows'][0]->consumption_at)->toBeString()
        ->and($result['rows'][0]->consumption_at)->toBe('2026/07/20 10:30:00');
});

it('[LogTableServiceTest]-[006] 商品が削除済みの場合、商品名は削除済みアイテムとなり合計金額は0になる', function () {
    $service = new LogTableService;
    $product = Product::factory()->create(['status' => ProductStatus::APPROVED]);
    $user = User::factory()->create();

    ConsumptionLog::factory()->create([
        'product_id' => $product->id,
        'user_id' => $user->id,
        'quantity' => 5,
    ]);

    $product->delete(); // 商品を削除（論理削除想定）

    $result = $service->getConsumptionLogTableData();

    expect($result['rows'][0]->product_name)->toBe('削除済みアイテム')
        ->and($result['rows'][0]->total_amount)->toBe(0);
});

it('[LogTableServiceTest]-[007] 消費ログが存在しない場合、rowsは空配列になる', function () {
    $service = new LogTableService;
    $result = $service->getConsumptionLogTableData();

    expect($result['rows'])->toBe([]);
});
