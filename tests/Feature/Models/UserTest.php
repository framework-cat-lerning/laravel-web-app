<?php

use App\Enums\ProductStatus;
use App\Enums\UserRole;
use App\Models\ConsumptionLog;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('[UserTest]-[001] roleはUserRole Enumにキャストされる', function () {
    $user = User::factory()->create(['role' => UserRole::STAFF]);

    expect($user->fresh()->role)->toBeInstanceOf(UserRole::class)
        ->and($user->fresh()->role)->toBe(UserRole::STAFF);
});

it('[UserTest]-[002] passwordは保存時にハッシュ化される', function () {
    $user = User::factory()->create(['password' => 'plain-password']);

    expect($user->password)->not->toBe('plain-password');
    expect(Hash::check('plain-password', $user->password))->toBeTrue();
});

it('[UserTest]-[003] fillableはname, email, password, roleのみ', function () {
    $user = new User;

    expect($user->getFillable())->toBe(['name', 'email', 'password', 'role']);
});

it('[UserTest]-[004] passwordとremember_tokenはhiddenに設定されている', function () {
    $user = User::factory()->create();

    $array = $user->toArray();

    expect($array)->not->toHaveKey('password')
        ->and($array)->not->toHaveKey('remember_token');
});

it('[UserTest]-[005] requestProductsリレーションでrequest_user_idに紐づく商品を取得できる', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create([
        'request_user_id' => $user->id,
        'status' => ProductStatus::PENDING,
    ]);

    expect($user->requestProducts)->toHaveCount(1)
        ->and($user->requestProducts->first()->id)->toBe($product->id);
});

it('[UserTest]-[006] consumptionLogsリレーションで自身の購入履歴を取得できる', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create();
    ConsumptionLog::factory()->for($user)->for($product)->create();

    expect($user->consumptionLogs)->toHaveCount(1);
});

it('[UserTest]-[007] ソフトデリートが有効になっている', function () {
    $user = User::factory()->create();

    $user->delete();

    expect(User::find($user->id))->toBeNull();
    expect(User::withTrashed()->find($user->id))->not->toBeNull();
});
