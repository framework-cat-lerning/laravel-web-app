<?php

use App\Http\Requests\Shop\ConsumptionProductRequest;
use App\Models\ConsumptionLog;
use App\Models\Product;
use App\Models\User;
use App\Services\ConsumptionLogService;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

uses(RefreshDatabase::class);

describe('[ConsumptionLogServiceTest]::[create]', function () {
    it('[ConsumptionLogServiceTest]-[001] 商品に紐づく購入ログを作成し、trueを返す', function () {
        $service = new ConsumptionLogService;
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $request = Mockery::mock(ConsumptionProductRequest::class);
        $request->shouldReceive('input')->with('count')->andReturn(3);
        $request->shouldReceive('user')->andReturn($user);

        $result = $service->create($product, $request);

        expect($result)->toBeTrue();

        $this->assertDatabaseHas('consumption_logs', [
            'product_id' => $product->id,
            'user_id' => $user->id,
            'quantity' => 3,
        ]);

        expect(ConsumptionLog::count())->toBe(1);
    });

    it('[ConsumptionLogServiceTest]-[002] 未ログインの場合は例外を投げ、ログが作成されない', function () {
        $service = new ConsumptionLogService;
        $product = Product::factory()->create();

        $request = Mockery::mock(ConsumptionProductRequest::class);
        $request->shouldReceive('input')->with('count')->andReturn(2);
        $request->shouldReceive('user')->andReturn(null);

        expect(fn () => $service->create($product, $request))
            ->toThrow(Exception::class, 'Not found.');

        expect(ConsumptionLog::count())->toBe(0);
    });

    it('[ConsumptionLogServiceTest]-[003] quantityがnullの場合はDB制約違反となり、ログが作成されない', function () {
        // quantity は unsignedInteger + NOT NULL 制約のため、
        // count に null を渡すとQueryExceptionが発生する
        $service = new ConsumptionLogService;
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $request = Mockery::mock(ConsumptionProductRequest::class);
        $request->shouldReceive('input')->with('count')->andReturn(null);
        $request->shouldReceive('user')->andReturn($user);

        expect(fn () => $service->create($product, $request))
            ->toThrow(QueryException::class);

        expect(ConsumptionLog::count())->toBe(0);
    });

    it('[ConsumptionLogServiceTest]-[004] 存在しない商品IDの場合は外部キー制約違反となり、ログが作成されない', function () {
        $service = new ConsumptionLogService;
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $product->forceDelete(); // 物理削除してFK制約違反を誘発（Productはソフトデリート対応）

        $request = Mockery::mock(ConsumptionProductRequest::class);
        $request->shouldReceive('input')->with('count')->andReturn(1);
        $request->shouldReceive('user')->andReturn($user);

        expect(fn () => $service->create($product, $request))
            ->toThrow(QueryException::class);

        expect(ConsumptionLog::count())->toBe(0);
    });

    it('[ConsumptionLogServiceTest]-[005] 存在しないユーザーIDの場合は外部キー制約違反となり、ログが作成されない', function () {
        $service = new ConsumptionLogService;
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $user->forceDelete(); // Userがソフトデリート対応でない場合は delete() に変更してください

        $request = Mockery::mock(ConsumptionProductRequest::class);
        $request->shouldReceive('input')->with('count')->andReturn(1);
        $request->shouldReceive('user')->andReturn($user);

        expect(fn () => $service->create($product, $request))
            ->toThrow(QueryException::class);

        expect(ConsumptionLog::count())->toBe(0);
    });
});

describe('[ConsumptionLogServiceTest]::[getUserID]', function () {
    it('[ConsumptionLogServiceTest]-[006] 認証済みユーザーのIDを返す', function () {
        $service = new ConsumptionLogService;
        $user = User::factory()->create();

        $request = Mockery::mock(Request::class);
        $request->shouldReceive('user')->andReturn($user);

        $userId = $service->getUserID($request);

        expect($userId)->toBe($user->id);
    });

    it('[ConsumptionLogServiceTest]-[007] 未ログインの場合は例外を投げる', function () {
        $service = new ConsumptionLogService;
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('user')->andReturn(null);

        expect(fn () => $service->getUserID($request))
            ->toThrow(Exception::class, 'Not found.');
    });
});