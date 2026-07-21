<?php

use App\Enums\ProductStatus;
use App\Enums\UserRole;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

describe('[Staff\InventoryController]::[index]', function () {
    it('[InventoryControllerTest]-[001] ADMIN/STAFFユーザーは承認済み商品の在庫一覧を表示できる', function (UserRole $role) {
        $user = User::factory()->create(['role' => $role]);
        $approved = Product::factory()->create(['status' => ProductStatus::APPROVED]);
        Inventory::factory()->for($approved)->create(['quantity' => 10]);
        Product::factory()->create(['status' => ProductStatus::PENDING]);

        actingAs($user)
            ->get(route('staff.inventries.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('inventory/index')
                ->has('inventories.data', 1)
                ->where('inventories.data.0.id', $approved->id)
                ->where('inventories.data.0.quantity', 10)
            );
    })->with([
        'ADMIN' => UserRole::ADMIN,
        'STAFF' => UserRole::STAFF,
    ]);

    it('[InventoryControllerTest]-[002] SHOPユーザーは在庫一覧にアクセスできず403となる', function () {
        $user = User::factory()->create(['role' => UserRole::SHOP]);

        actingAs($user)
            ->get(route('staff.inventries.index'))
            ->assertForbidden();
    });

    it('[InventoryControllerTest]-[003] 未ログインの場合はloginページへリダイレクトされる', function () {
        get(route('staff.inventries.index'))
            ->assertRedirect(route('login'));
    });

    it('[InventoryControllerTest]-[004] 在庫が存在しない承認済み商品はquantityが0として表示される', function () {
        $user = User::factory()->create(['role' => UserRole::STAFF]);
        $approved = Product::factory()->create(['status' => ProductStatus::APPROVED]);
        // 在庫レコードなし

        actingAs($user)
            ->get(route('staff.inventries.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('inventories.data.0.id', $approved->id)
                ->where('inventories.data.0.quantity', 0)
            );
    });
});

describe('[Staff\InventoryController]::[buy]', function () {
    it('[InventoryControllerTest]-[005] STAFFユーザーは在庫を追加でき、一覧へリダイレクトされる', function () {
        $user = User::factory()->create(['role' => UserRole::STAFF]);
        $product = Product::factory()->create(['status' => ProductStatus::APPROVED]);
        Inventory::factory()->for($product)->create(['quantity' => 5]);

        actingAs($user)
            ->post(route('staff.inventries.buy', $product), ['count' => 3])
            ->assertRedirect(route('staff.inventries.index'));

        $this->assertDatabaseHas('inventories', [
            'product_id' => $product->id,
            'quantity' => 8,
        ]);
    });

    it('[InventoryControllerTest]-[006] 在庫レコードが無い商品は新規作成される', function () {
        $user = User::factory()->create(['role' => UserRole::STAFF]);
        $product = Product::factory()->create(['status' => ProductStatus::APPROVED]);

        actingAs($user)
            ->post(route('staff.inventries.buy', $product), ['count' => 10])
            ->assertRedirect(route('staff.inventries.index'));

        $this->assertDatabaseHas('inventories', [
            'product_id' => $product->id,
            'quantity' => 10,
        ]);
    });

    it('[InventoryControllerTest]-[007] ADMIN/SHOPユーザーは在庫を追加できず403となる', function (UserRole $role) {
        $user = User::factory()->create(['role' => $role]);
        $product = Product::factory()->create(['status' => ProductStatus::APPROVED]);

        actingAs($user)
            ->post(route('staff.inventries.buy', $product), ['count' => 3])
            ->assertForbidden();

        expect(Inventory::count())->toBe(0);
    })->with([
        'ADMIN' => UserRole::ADMIN,
        'SHOP' => UserRole::SHOP,
    ]);

    it('[InventoryControllerTest]-[008] countが0の場合はバリデーションエラーとなる', function () {
        $user = User::factory()->create(['role' => UserRole::STAFF]);
        $product = Product::factory()->create(['status' => ProductStatus::APPROVED]);

        actingAs($user)
            ->post(route('staff.inventries.buy', $product), ['count' => 0])
            ->assertSessionHasErrors('count');
    });

    it('[InventoryControllerTest]-[009] countが未入力の場合はバリデーションエラーとなる', function () {
        $user = User::factory()->create(['role' => UserRole::STAFF]);
        $product = Product::factory()->create(['status' => ProductStatus::APPROVED]);

        actingAs($user)
            ->post(route('staff.inventries.buy', $product), [])
            ->assertSessionHasErrors('count');
    });
});
