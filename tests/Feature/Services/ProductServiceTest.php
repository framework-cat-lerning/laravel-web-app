<?php

use App\Enums\ProductStatus;
use App\Http\Requests\Admin\ProductUpdateRequest;
use App\Http\Requests\Staff\ProductStoreRequest;
use App\Models\Product;
use App\Models\User;
use App\Services\ConsumptionLogService;
use App\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('[ProductService]::[store]', function () {
    it('[ProductServiceTest]-[001] 新規商品を作成し、ステータスはPENDING、request_user_idにログインユーザーが設定される', function () {
        $service = new ProductService(new ConsumptionLogService);

        $user = User::factory()->create();

        $request = Mockery::mock(ProductStoreRequest::class);
        $request->shouldReceive('validated')->andReturn([
            'name' => 'テスト商品',
            'description' => '説明文',
            'price' => 1000,
        ]);
        $request->shouldReceive('user')->andReturn($user);

        $product = $service->store($request);

        expect($product)->toBeInstanceOf(Product::class)
            ->and($product->name)->toBe('テスト商品')
            ->and($product->description)->toBe('説明文')
            ->and($product->price)->toBe(1000)
            ->and($product->status)->toBe(ProductStatus::PENDING)
            ->and($product->request_user_id)->toBe($user->id);

        $this->assertDatabaseHas('products', [
            'name' => 'テスト商品',
            'status' => ProductStatus::PENDING->value,
            'request_user_id' => $user->id,
        ]);
    });
});

describe('[ProductService]::[update]', function () {
    it('[ProductServiceTest]-[002] 商品情報を更新できる', function () {
        $service = new ProductService(new ConsumptionLogService);

        $product = Product::factory()->create([
            'name' => '旧商品名',
            'price' => 500,
        ]);

        $request = Mockery::mock(ProductUpdateRequest::class);
        $request->shouldReceive('validated')->andReturn([
            'name' => '新商品名',
            'description' => '新しい説明',
            'price' => 2000,
        ]);

        $result = $service->update($request, $product);

        expect($result->name)->toBe('新商品名')
            ->and($result->price)->toBe(2000);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => '新商品名',
            'price' => 2000,
        ]);
    });
});

describe('[ProductService]::[cancel]', function () {
    it('[ProductServiceTest]-[003] ステータスがPENDINGの場合は削除され、trueを返す', function () {
        $service = new ProductService(new ConsumptionLogService);

        $product = Product::factory()->create(['status' => ProductStatus::PENDING]);

        $result = $service->cancel($product);

        expect($result)->toBeTrue();
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    });

    it('[ProductServiceTest]-[004] ステータスがAPPROVEDの場合は削除されず、trueを返す', function () {
        $service = new ProductService(new ConsumptionLogService);

        $product = Product::factory()->create(['status' => ProductStatus::APPROVED]);

        $result = $service->cancel($product);

        expect($result)->toBeTrue();
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'deleted_at' => null,
        ]);
    });
});

describe('[ProductService]::[delete]', function () {
    it('[ProductServiceTest]-[005] ステータスがAPPROVEDの場合は削除され、trueを返す', function () {
        $service = new ProductService(new ConsumptionLogService);

        $product = Product::factory()->create(['status' => ProductStatus::APPROVED]);

        $result = $service->delete($product);

        expect($result)->toBeTrue();
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    });

    it('[ProductServiceTest]-[006] ステータスがPENDINGの場合は削除されず、trueを返す', function () {
        $service = new ProductService(new ConsumptionLogService);

        $product = Product::factory()->create(['status' => ProductStatus::PENDING]);

        $result = $service->delete($product);

        expect($result)->toBeTrue();
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'deleted_at' => null,
        ]);
    });
});

describe('[ProductService]::[approval]', function () {
    it('[ProductServiceTest]-[007] ステータスをAPPROVEDに更新し、trueを返す', function () {
        $service = new ProductService(new ConsumptionLogService);

        $product = Product::factory()->create(['status' => ProductStatus::PENDING]);

        $result = $service->approval($product);

        expect($result)->toBeTrue();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'status' => ProductStatus::APPROVED->value,
        ]);
    });

    it('[ProductServiceTest]-[008] 既にAPPROVEDの商品でも問題なく実行できる', function () {
        $service = new ProductService(new ConsumptionLogService);

        $product = Product::factory()->create(['status' => ProductStatus::APPROVED]);

        $result = $service->approval($product);

        expect($result)->toBeTrue();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'status' => ProductStatus::APPROVED->value,
        ]);
    });
});