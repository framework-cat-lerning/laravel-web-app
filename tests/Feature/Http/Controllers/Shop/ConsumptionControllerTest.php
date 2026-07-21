<?php

use App\Enums\ProductStatus;
use App\Enums\UserRole;
use App\Models\ConsumptionLog;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

describe('[Shop\ConsumptionController]::[index]', function () {
    it('[ConsumptionControllerTest]-[001] ログイン中であればどのroleでも承認済み・在庫ありの商品一覧を表示できる', function (UserRole $role) {
        $user = User::factory()->create(['role' => $role]);

        $target = Product::factory()->create(['status' => ProductStatus::APPROVED]);
        Inventory::factory()->for($target)->create(['quantity' => 5]);

        // 承認済みだが在庫0（対象外）
        $noStock = Product::factory()->create(['status' => ProductStatus::APPROVED]);
        Inventory::factory()->for($noStock)->create(['quantity' => 0]);

        // 未承認（対象外）
        $pending = Product::factory()->create(['status' => ProductStatus::PENDING]);
        Inventory::factory()->for($pending)->create(['quantity' => 10]);

        actingAs($user)
            ->get(route('shop.products.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('consumption/index')
                ->has('products.data', 1)
                ->where('products.data.0.id', $target->id)
                ->where('products.data.0.quantity', 5)
            );
    })->with([
        'ADMIN' => UserRole::ADMIN,
        'STAFF' => UserRole::STAFF,
        'SHOP' => UserRole::SHOP,
    ]);

    it('[ConsumptionControllerTest]-[002] 未ログインの場合はloginページへリダイレクトされる', function () {
        get(route('shop.products.index'))
            ->assertRedirect(route('login'));
    });
});

describe('[Shop\ConsumptionController]::[consumption]', function () {
    it('[ConsumptionControllerTest]-[003] STAFF/SHOPユーザーは商品を購入でき、一覧へリダイレクトされる', function (UserRole $role) {
        $user = User::factory()->create(['role' => $role]);
        $product = Product::factory()->create(['status' => ProductStatus::APPROVED]);
        $inventory = Inventory::factory()->for($product)->create(['quantity' => 10]);

        actingAs($user)
            ->post(route('shop.products.consumption', $product), ['count' => 3])
            ->assertRedirect(route('shop.products.index'));

        $this->assertDatabaseHas('inventories', [
            'id' => $inventory->id,
            'quantity' => 7,
        ]);
        $this->assertDatabaseHas('consumption_logs', [
            'product_id' => $product->id,
            'user_id' => $user->id,
            'quantity' => 3,
        ]);
    })->with([
        'STAFF' => UserRole::STAFF,
        'SHOP' => UserRole::SHOP,
    ]);

    it('[ConsumptionControllerTest]-[004] ADMINユーザーは購入できず403となる', function () {
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        $product = Product::factory()->create(['status' => ProductStatus::APPROVED]);
        Inventory::factory()->for($product)->create(['quantity' => 10]);

        actingAs($user)
            ->post(route('shop.products.consumption', $product), ['count' => 3])
            ->assertForbidden();

        expect(ConsumptionLog::count())->toBe(0);
    });

    it('[ConsumptionControllerTest]-[005] 在庫レコードが存在しない商品は購入できず403となる', function () {
        $user = User::factory()->create(['role' => UserRole::STAFF]);
        $product = Product::factory()->create(['status' => ProductStatus::APPROVED]);
        // 在庫レコードなし

        actingAs($user)
            ->post(route('shop.products.consumption', $product), ['count' => 1])
            ->assertForbidden();

        expect(ConsumptionLog::count())->toBe(0);
    });

    it('[ConsumptionControllerTest]-[006] countが未入力の場合はバリデーションエラーとなる', function () {
        $user = User::factory()->create(['role' => UserRole::STAFF]);
        $product = Product::factory()->create(['status' => ProductStatus::APPROVED]);
        Inventory::factory()->for($product)->create(['quantity' => 10]);

        actingAs($user)
            ->post(route('shop.products.consumption', $product), [])
            ->assertSessionHasErrors('count');
    });

    it('在庫数が不足している場合は例外が発生し、ログも在庫も変更されない', function () {
        $user = User::factory()->create(['role' => UserRole::STAFF]);
        $product = Product::factory()->create(['status' => ProductStatus::APPROVED]);
        $inventory = Inventory::factory()->for($product)->create(['quantity' => 2]);

        $this->withoutExceptionHandling();

        expect(fn () => actingAs($user)->post(route('shop.products.consumption', $product), ['count' => 5]))
            ->toThrow(Exception::class, '在庫数が不足してます');

        $this->assertDatabaseHas('inventories', [
            'id' => $inventory->id,
            'quantity' => 2,
        ]);
        expect(ConsumptionLog::count())->toBe(0);
    });
});
