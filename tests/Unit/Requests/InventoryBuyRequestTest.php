<?php

use App\Enums\UserRole;
use App\Http\Requests\Staff\InventoryBuyRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;

uses(RefreshDatabase::class);

it('[InventoryBuyRequestTest]-[001] countが1以上の整数であればバリデーションを通過する', function () {
    $validator = Validator::make(['count' => 1], (new InventoryBuyRequest)->rules());

    expect($validator->passes())->toBeTrue();
});

it('[InventoryBuyRequestTest]-[002] countが0の場合はバリデーションエラーとなる', function () {
    $validator = Validator::make(['count' => 0], (new InventoryBuyRequest)->rules());

    expect($validator->fails())->toBeTrue();
});

it('[InventoryBuyRequestTest]-[003] countが未入力の場合はバリデーションエラーとなる', function () {
    $validator = Validator::make([], (new InventoryBuyRequest)->rules());

    expect($validator->fails())->toBeTrue();
});

it('[InventoryBuyRequestTest]-[004] countが整数でない場合はバリデーションエラーとなる', function () {
    $validator = Validator::make(['count' => 'abc'], (new InventoryBuyRequest)->rules());

    expect($validator->fails())->toBeTrue();
});

describe('[InventoryBuyRequest]::[authorize]', function () {
    it('[InventoryBuyRequestTest]-[005] STAFFはtrueを返す', function () {
        $user = User::factory()->create(['role' => UserRole::STAFF]);

        $request = new InventoryBuyRequest;
        $request->setUserResolver(fn () => $user);

        expect($request->authorize())->toBeTrue();
    });

    it('[InventoryBuyRequestTest]-[006] ADMIN/SHOPはfalseを返す', function (UserRole $role) {
        $user = User::factory()->create(['role' => $role]);

        $request = new InventoryBuyRequest;
        $request->setUserResolver(fn () => $user);

        expect($request->authorize())->toBeFalse();
    })->with([
        'ADMIN' => UserRole::ADMIN,
        'SHOP' => UserRole::SHOP,
    ]);
});
