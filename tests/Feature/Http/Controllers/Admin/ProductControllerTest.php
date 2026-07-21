<?php

use App\Enums\ProductStatus;
use App\Enums\UserRole;
use App\Models\Product;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\put;
use function Pest\Laravel\patch;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

describe('Admin\ProductController::index', function () {
    it('ログイン中のユーザーは商品一覧を表示できる', function () {
        $user = User::factory()->create();
        Product::factory()->count(3)->create();

        actingAs($user)
            ->get(route('admin.products.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('products/index')
                ->has('products.data', 3)
            );
    });

    it('未ログインの場合はloginページへリダイレクトされる', function () {
        get(route('admin.products.index'))
            ->assertRedirect(route('login'));
    });

    it('sort, directionパラメータを指定して並び替えできる', function () {
        $user = User::factory()->create();
        $productA = Product::factory()->create(['name' => 'Aaa']);
        $productB = Product::factory()->create(['name' => 'Bbb']);

        actingAs($user)
            ->get(route('admin.products.index', ['sort' => 'name', 'direction' => 'desc']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('products/index')
                ->where('products.data.0.id', $productB->id)
            );
    });
});

describe('Admin\ProductController::show', function () {
    it('商品詳細を表示できる', function () {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        actingAs($user)
            ->get(route('admin.products.show', $product))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('products/show')
                ->where('product.data.id', $product->id)
                ->where('product.data.name', $product->name)
                ->where('product.data.status.id', $product->status->value)
                ->where('product.data.status.label', $product->status->label())
            );
    });

    it('未ログインの場合はloginページへリダイレクトされる', function () {
        $product = Product::factory()->create();

        get(route('admin.products.show', $product))
            ->assertRedirect(route('login'));
    });
});

describe('Admin\ProductController::edit', function () {
    it('ADMINユーザーは編集画面を表示できる', function () {
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        $product = Product::factory()->create();

        actingAs($user)
            ->get(route('admin.products.edit', $product))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('products/form')
                ->where('form_type', 'edit')
            );
    });

    it('ADMIN以外のユーザーは編集画面にアクセスできず403となる', function (UserRole $role) {
        $user = User::factory()->create(['role' => $role]);
        $product = Product::factory()->create();

        actingAs($user)
            ->get(route('admin.products.edit', $product))
            ->assertForbidden();
    })->with([
        'STAFF' => UserRole::STAFF,
        'SHOP' => UserRole::SHOP,
    ]);
});

describe('Admin\ProductController::update', function () {
    it('ADMINユーザーは商品を更新でき、一覧へリダイレクトされる', function () {
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        $product = Product::factory()->create(['name' => '旧商品名', 'price' => 500]);

        actingAs($user)
            ->put(route('admin.products.update', $product), [
                'name' => '新商品名',
                'description' => '説明',
                'price' => 2000,
            ])
            ->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => '新商品名',
            'price' => 2000,
        ]);
    });

    it('ADMIN以外のユーザーは更新できず403となる', function (UserRole $role) {
        $user = User::factory()->create(['role' => $role]);
        $product = Product::factory()->create(['name' => '旧商品名']);

        actingAs($user)
            ->put(route('admin.products.update', $product), [
                'name' => '新商品名',
                'price' => 2000,
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => '旧商品名']);
    })->with([
        'STAFF' => UserRole::STAFF,
        'SHOP' => UserRole::SHOP,
    ]);

    it('バリデーションエラーの場合は更新されない', function () {
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        $product = Product::factory()->create(['name' => '旧商品名']);

        actingAs($user)
            ->put(route('admin.products.update', $product), [
                'name' => '',
                'price' => 2000,
            ])
            ->assertSessionHasErrors('name');

        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => '旧商品名']);
    });
});

describe('Admin\ProductController::delete', function () {
    it('ADMINユーザーは承認済み商品を削除でき、admin.products一覧へリダイレクトされる', function () {
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        $product = Product::factory()->create(['status' => ProductStatus::APPROVED]);

        actingAs($user)
            ->delete(route('admin.products.delete', $product))
            ->assertRedirect(route('admin.products.index'));

        $this->assertSoftDeleted('products', ['id' => $product->id]);
    });

    it('商品の申請者本人は削除できる', function () {
        $requester = User::factory()->create(['role' => UserRole::STAFF]);
        $product = Product::factory()->create([
            'status' => ProductStatus::APPROVED,
            'request_user_id' => $requester->id,
        ]);

        actingAs($requester)
            ->delete(route('admin.products.delete', $product))
            ->assertRedirect(route('admin.products.index'));

        $this->assertSoftDeleted('products', ['id' => $product->id]);
    });

    it('ADMINでも申請者本人でもない場合は削除できず403となる', function () {
        $other = User::factory()->create(['role' => UserRole::STAFF]);
        $product = Product::factory()->create([
            'status' => ProductStatus::APPROVED,
            'request_user_id' => User::factory()->create()->id,
        ]);

        actingAs($other)
            ->delete(route('admin.products.delete', $product))
            ->assertForbidden();

        $this->assertDatabaseHas('products', ['id' => $product->id, 'deleted_at' => null]);
    });
});

describe('Admin\ProductController::approval', function () {
    it('ADMINユーザーは商品を承認でき、一覧へリダイレクトされる', function () {
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        $product = Product::factory()->create(['status' => ProductStatus::PENDING]);

        actingAs($user)
            ->patch(route('admin.products.approval', $product))
            ->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'status' => ProductStatus::APPROVED->value,
        ]);
    });

    it('ADMIN以外のユーザーは承認できず403となる', function (UserRole $role) {
        $user = User::factory()->create(['role' => $role]);
        $product = Product::factory()->create(['status' => ProductStatus::PENDING]);

        actingAs($user)
            ->patch(route('admin.products.approval', $product))
            ->assertForbidden();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'status' => ProductStatus::PENDING->value,
        ]);
    })->with([
        'STAFF' => UserRole::STAFF,
        'SHOP' => UserRole::SHOP,
    ]);
});