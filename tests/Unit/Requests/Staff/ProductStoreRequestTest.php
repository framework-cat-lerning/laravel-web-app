<?php

use App\Enums\UserRole;
use App\Http\Requests\Staff\ProductStoreRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;

uses(RefreshDatabase::class);

function makeProductStoreValidator(array $data)
{
    $request = new ProductStoreRequest;

    return Validator::make(
        $data,
        $request->rules(),
        $request->messages(),
        $request->attributes()
    );
}

it('[ProductStoreRequestTest]-[001] 正しい形式であればバリデーションを通過する', function () {
    $validator = makeProductStoreValidator([
        'name' => '商品名', 'description' => '説明', 'price' => 100,
    ]);

    expect($validator->passes())->toBeTrue();
});

it('[ProductStoreRequestTest]-[002] descriptionはnullでも通過する', function () {
    $validator = makeProductStoreValidator([
        'name' => '商品名', 'price' => 100,
    ]);

    expect($validator->passes())->toBeTrue();
});

it('[ProductStoreRequestTest]-[003] nameが未入力の場合はバリデーションエラーとなる', function () {
    $validator = makeProductStoreValidator(['price' => 100]);

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->first('name'))->toBe('商品名を入力してください');
});

it('[ProductStoreRequestTest]-[004] priceが0以下の場合はバリデーションエラーとなる', function () {
    $validator = makeProductStoreValidator(['name' => '商品名', 'price' => 0]);

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->first('price'))->toBe('商品価格は1以上で入力してください');
});

it('[ProductStoreRequestTest]-[005] priceが整数でない場合はバリデーションエラーとなる', function () {
    $validator = makeProductStoreValidator(['name' => '商品名', 'price' => 'abc']);

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->first('price'))->toBe('商品価格は整数で入力してください');
});

describe('[ProductStoreRequest]::[authorize]', function () {
    it('[ProductStoreRequestTest]-[006] STAFFはtrueを返す', function () {
        $user = User::factory()->create(['role' => UserRole::STAFF]);

        $request = new ProductStoreRequest;
        $request->setUserResolver(fn () => $user);

        expect($request->authorize())->toBeTrue();
    });

    it('[ProductStoreRequestTest]-[007] ADMIN/SHOPはfalseを返す', function (UserRole $role) {
        $user = User::factory()->create(['role' => $role]);

        $request = new ProductStoreRequest;
        $request->setUserResolver(fn () => $user);

        expect($request->authorize())->toBeFalse();
    })->with([
        'ADMIN' => UserRole::ADMIN,
        'SHOP' => UserRole::SHOP,
    ]);
});