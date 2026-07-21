<?php

use App\Http\Requests\Shop\ConsumptionProductRequest;
use App\Http\Requests\Staff\InventoryBuyRequest;
use App\Models\ConsumptionLog;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\User;
use App\Services\ConsumptionLogService;
use App\Services\InventoryService;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('[InventoryService]::[buy]', function () {
    it('[InventoryServiceTest]-[001] 在庫が存在しない場合は新規作成され、trueを返す', function () {
        $service = new InventoryService(new ConsumptionLogService);

        $product = Product::factory()->create();

        $request = Mockery::mock(InventoryBuyRequest::class);
        $request->shouldReceive('input')->with('count')->andReturn(10);

        $result = $service->buy($request, $product);

        expect($result)->toBeTrue();

        $this->assertDatabaseHas('inventories', [
            'product_id' => $product->id,
            'quantity' => 10,
        ]);
    });

    it('[InventoryServiceTest]-[002] 在庫が既に存在する場合は数量が加算され、trueを返す', function () {
        $service = new InventoryService(new ConsumptionLogService);

        $product = Product::factory()->create();
        $inventory = Inventory::factory()->for($product)->create(['quantity' => 5]);

        $request = Mockery::mock(InventoryBuyRequest::class);
        $request->shouldReceive('input')->with('count')->andReturn(3);

        $result = $service->buy($request, $product);

        expect($result)->toBeTrue();

        $this->assertDatabaseHas('inventories', [
            'id' => $inventory->id,
            'quantity' => 8,
        ]);
    });

    it('[InventoryServiceTest]-[003] countが0の場合はfalseを返し、在庫は変更されない', function () {
        $service = new InventoryService(new ConsumptionLogService);

        $product = Product::factory()->create();
        Inventory::factory()->for($product)->create(['quantity' => 5]);

        $request = Mockery::mock(InventoryBuyRequest::class);
        $request->shouldReceive('input')->with('count')->andReturn(0);

        $result = $service->buy($request, $product);

        expect($result)->toBeFalse();

        $this->assertDatabaseHas('inventories', [
            'product_id' => $product->id,
            'quantity' => 5,
        ]);
    });

    it('[InventoryServiceTest]-[004] countがnullの場合はfalseを返し、在庫は作成されない', function () {
        $service = new InventoryService(new ConsumptionLogService);

        $product = Product::factory()->create();

        $request = Mockery::mock(InventoryBuyRequest::class);
        $request->shouldReceive('input')->with('count')->andReturn(null);

        $result = $service->buy($request, $product);

        expect($result)->toBeFalse();

        expect(Inventory::count())->toBe(0);
    });

    it('[InventoryServiceTest]-[005] 存在しない商品の場合は外部キー制約違反となり、在庫が作成されない', function () {
        $service = new InventoryService(new ConsumptionLogService);

        $product = Product::factory()->create();
        $product->forceDelete();

        $request = Mockery::mock(InventoryBuyRequest::class);
        $request->shouldReceive('input')->with('count')->andReturn(5);

        expect(fn () => $service->buy($request, $product))
            ->toThrow(QueryException::class);

        expect(Inventory::count())->toBe(0);
    });
});

describe('[InventoryService]::[consumption]', function () {
    it('[InventoryServiceTest]-[006] 在庫が十分な場合は在庫を減らし、購入ログを作成してtrueを返す', function () {
        $service = new InventoryService(new ConsumptionLogService);

        $user = User::factory()->create();
        $product = Product::factory()->create();
        $inventory = Inventory::factory()->for($product)->create(['quantity' => 10]);

        $request = Mockery::mock(ConsumptionProductRequest::class);
        $request->shouldReceive('input')->with('count')->andReturn(4);
        $request->shouldReceive('user')->andReturn($user);

        $result = $service->consumption($product, $request);

        expect($result)->toBeTrue();

        $this->assertDatabaseHas('inventories', [
            'id' => $inventory->id,
            'quantity' => 6,
        ]);

        $this->assertDatabaseHas('consumption_logs', [
            'product_id' => $product->id,
            'user_id' => $user->id,
            'quantity' => 4,
        ]);

        expect(ConsumptionLog::count())->toBe(1);
    });

    it('[InventoryServiceTest]-[007] 在庫が不足している場合は例外を投げ、在庫もログも変更されない', function () {
        $service = new InventoryService(new ConsumptionLogService);

        $user = User::factory()->create();
        $product = Product::factory()->create();
        $inventory = Inventory::factory()->for($product)->create(['quantity' => 2]);

        $request = Mockery::mock(ConsumptionProductRequest::class);
        $request->shouldReceive('input')->with('count')->andReturn(5);
        $request->shouldReceive('user')->andReturn($user);

        expect(fn () => $service->consumption($product, $request))
            ->toThrow(Exception::class, '在庫数が不足してます');

        $this->assertDatabaseHas('inventories', [
            'id' => $inventory->id,
            'quantity' => 2,
        ]);

        expect(ConsumptionLog::count())->toBe(0);
    });

    it('[InventoryServiceTest]-[008] 在庫が存在しない場合は在庫不足として例外を投げる', function () {
        $service = new InventoryService(new ConsumptionLogService);

        $user = User::factory()->create();
        $product = Product::factory()->create();
        // 在庫レコードなし

        $request = Mockery::mock(ConsumptionProductRequest::class);
        $request->shouldReceive('input')->with('count')->andReturn(1);
        $request->shouldReceive('user')->andReturn($user);

        expect(fn () => $service->consumption($product, $request))
            ->toThrow(Exception::class, '在庫数が不足してます');

        expect(ConsumptionLog::count())->toBe(0);
    });

    it('[InventoryServiceTest]-[009] DB例外発生時はロールバックされ、在庫もログも変更されない', function () {
        $service = new InventoryService(new ConsumptionLogService);

        $user = User::factory()->create();
        $product = Product::factory()->create();
        $inventory = Inventory::factory()->for($product)->create(['quantity' => 10]);
        $user->forceDelete();

        $request = Mockery::mock(ConsumptionProductRequest::class);
        $request->shouldReceive('input')->with('count')->andReturn(4);
        $request->shouldReceive('user')->andReturn($user);

        expect(fn () => $service->consumption($product, $request))
            ->toThrow(QueryException::class);

        $this->assertDatabaseHas('inventories', [
            'id' => $inventory->id,
            'quantity' => 10,
        ]);

        expect(ConsumptionLog::count())->toBe(0);
    });
});
