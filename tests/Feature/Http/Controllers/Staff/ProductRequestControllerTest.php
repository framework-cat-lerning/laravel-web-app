<?php

use App\Enums\ProductStatus;
use App\Enums\UserRole;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;

test('[ProductRequestControllerTest]-[001] 在庫搬入者は商品申請作成画面を表示できる', function () {
    $user = User::factory()->create(['role' => UserRole::STAFF]);

    actingAs($user)
        ->get(route('staff.products.new'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('products/form')
            ->where('form_type', 'new'));
});

test('[ProductRequestControllerTest]-[002] 申請権限のないユーザは商品申請作成画面を表示できない', function () {
    $user = User::factory()->create(['role' => UserRole::SHOP]);

    actingAs($user)
        ->get(route('staff.products.new'))
        ->assertForbidden();
});

test('[ProductRequestControllerTest]-[003] 在庫搬入者は商品申請を保存できる', function () {
    $user = User::factory()->create(['role' => UserRole::STAFF]);

    $response = actingAs($user)->post(route('staff.products.store'), [
        'name' => 'テスト商品',
        'description' => 'テスト商品の説明',
        'price' => 1500,
    ]);

    $response->assertRedirect(route('staff.products.index'));

    assertDatabaseHas('products', [
        'name' => 'テスト商品',
        'description' => 'テスト商品の説明',
        'price' => 1500,
        'status' => ProductStatus::PENDING->value,
        'request_user_id' => $user->id,
    ]);
});

test('[ProductRequestControllerTest]-[004] 申請権限のないユーザは保存できない', function () {
    $user = User::factory()->create(['role' => UserRole::SHOP]);

    $response = actingAs($user)->post(route('staff.products.store'), [
        'name' => 'テスト商品',
        'price' => 1500,
    ]);

    $response->assertForbidden();
    assertDatabaseCount('products', 0);
});

test('[ProductRequestControllerTest]-[005] 必須項目が無い場合はバリデーションエラーになる', function () {
    $user = User::factory()->create(['role' => UserRole::STAFF]);

    $response = actingAs($user)
        ->from(route('staff.products.new'))
        ->post(route('staff.products.store'), [
            'name' => '',
            'price' => 0,
        ]);

    $response->assertRedirect(route('staff.products.new'));
    $response->assertSessionHasErrors([
        'name' => '商品名を入力してください',
        'price' => '商品価格は1以上で入力してください',
    ]);
    assertDatabaseCount('products', 0);
});

test('[ProductRequestControllerTest]-[006] 価格が整数でない場合はバリデーションエラーになる', function () {
    $user = User::factory()->create(['role' => UserRole::STAFF]);

    $response = actingAs($user)
        ->from(route('staff.products.new'))
        ->post(route('staff.products.store'), [
            'name' => 'テスト商品',
            'price' => '1.5',
        ]);

    $response->assertRedirect(route('staff.products.new'));
    $response->assertSessionHasErrors([
        'price' => '商品価格は整数で入力してください',
    ]);
    assertDatabaseCount('products', 0);
});
