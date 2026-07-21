<?php

use App\Enums\ProductStatus;
use App\Enums\UserRole;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

describe('[Staff\ProductRequestController]::[index]', function () {
    it('[ProductRequestControllerTest]-[001] ログイン中のユーザーは自分が申請した商品一覧のみ表示できる', function () {
        $user = User::factory()->create();
        $ownProduct = Product::factory()->create(['request_user_id' => $user->id]);
        $otherProduct = Product::factory()->create(['request_user_id' => User::factory()->create()->id]);

        actingAs($user)
            ->get(route('staff.products.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('products/index')
                ->has('products.data', 1)
                ->where('products.data.0.id', $ownProduct->id)
            );
    });

    it('[ProductRequestControllerTest]-[002] 未ログインの場合はloginページへリダイレクトされる', function () {
        get(route('staff.products.index'))
            ->assertRedirect(route('login'));
    });
});

describe('[Staff\ProductRequestController]::[new]', function () {
    it('[ProductRequestControllerTest]-[003] STAFFユーザーは申請作成画面を表示できる', function () {
        $user = User::factory()->create(['role' => UserRole::STAFF]);

        actingAs($user)
            ->get(route('staff.products.new'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('products/form')
                ->where('form_type', 'new')
            );
    });

    it('[ProductRequestControllerTest]-[004] STAFF以外のユーザーは申請作成画面にアクセスできず403となる', function (UserRole $role) {
        $user = User::factory()->create(['role' => $role]);

        actingAs($user)
            ->get(route('staff.products.new'))
            ->assertForbidden();
    })->with([
        'ADMIN' => UserRole::ADMIN,
        'SHOP' => UserRole::SHOP,
    ]);
});

describe('[Staff\ProductRequestController]::[store]', function () {
    it('[ProductRequestControllerTest]-[005] STAFFユーザーは商品申請を作成でき、一覧へリダイレクトされる', function () {
        $user = User::factory()->create(['role' => UserRole::STAFF]);

        actingAs($user)
            ->post(route('staff.products.store'), [
                'name' => '新規申請商品',
                'description' => '説明',
                'price' => 3000,
            ])
            ->assertRedirect(route('staff.products.index'));

        $this->assertDatabaseHas('products', [
            'name' => '新規申請商品',
            'request_user_id' => $user->id,
            'status' => ProductStatus::PENDING->value,
        ]);
    });

    it('[ProductRequestControllerTest]-[006] STAFF以外のユーザーは作成できず403となる', function (UserRole $role) {
        $user = User::factory()->create(['role' => $role]);

        actingAs($user)
            ->post(route('staff.products.store'), [
                'name' => '新規申請商品',
                'price' => 3000,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('products', ['name' => '新規申請商品']);
    })->with([
        'ADMIN' => UserRole::ADMIN,
        'SHOP' => UserRole::SHOP,
    ]);

    it('[ProductRequestControllerTest]-[007] バリデーションエラーの場合は作成されない', function () {
        $user = User::factory()->create(['role' => UserRole::STAFF]);

        actingAs($user)
            ->post(route('staff.products.store'), [
                'name' => '',
                'price' => 3000,
            ])
            ->assertSessionHasErrors('name');

        expect(Product::count())->toBe(0);
    });
});

describe('[Staff\ProductRequestController]::[cancel]', function () {
    it('[ProductRequestControllerTest]-[008] 申請者本人はPENDING状態の商品をキャンセルでき、一覧へリダイレクトされる', function () {
        $user = User::factory()->create(['role' => UserRole::STAFF]);
        $product = Product::factory()->create([
            'request_user_id' => $user->id,
            'status' => ProductStatus::PENDING,
        ]);

        actingAs($user)
            ->delete(route('staff.products.cancel', $product))
            ->assertRedirect(route('staff.products.index'));

        $this->assertSoftDeleted('products', ['id' => $product->id]);
    });

    it('[ProductRequestControllerTest]-[009] ADMINユーザーも他者の申請をキャンセルできる', function () {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $requester = User::factory()->create(['role' => UserRole::STAFF]);
        $product = Product::factory()->create([
            'request_user_id' => $requester->id,
            'status' => ProductStatus::PENDING,
        ]);

        actingAs($admin)
            ->delete(route('staff.products.cancel', $product))
            ->assertRedirect(route('staff.products.index'));

        $this->assertSoftDeleted('products', ['id' => $product->id]);
    });

    it('[ProductRequestControllerTest]-[010] 申請者本人でもADMINでもない場合はキャンセルできず403となる', function () {
        $user = User::factory()->create(['role' => UserRole::STAFF]);
        $requester = User::factory()->create(['role' => UserRole::STAFF]);
        $product = Product::factory()->create([
            'request_user_id' => $requester->id,
            'status' => ProductStatus::PENDING,
        ]);

        actingAs($user)
            ->delete(route('staff.products.cancel', $product))
            ->assertForbidden();

        $this->assertDatabaseHas('products', ['id' => $product->id, 'deleted_at' => null]);
    });
});
