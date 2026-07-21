<?php

use App\Enums\UserRole;
use App\Http\Requests\Admin\UserStoreRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;

uses(RefreshDatabase::class);

it('[UserStoreRequestTest]-[001] 正しい形式であればバリデーションを通過する', function () {
    $validator = Validator::make(
        [
            'name' => 'テストユーザー',
            'email' => 'new@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => UserRole::STAFF,
        ],
        (new UserStoreRequest)->rules()
    );

    expect($validator->passes())->toBeTrue();
});

it('[UserStoreRequestTest]-[002] emailが既に使われている場合はバリデーションエラーとなる', function () {
    User::factory()->create(['email' => 'dup@example.com']);

    $validator = Validator::make(
        [
            'name' => 'テストユーザー',
            'email' => 'dup@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => UserRole::STAFF->value,
        ],
        (new UserStoreRequest)->rules()
    );

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('email'))->toBeTrue();
});

it('[UserStoreRequestTest]-[003] passwordの確認が一致しない場合はバリデーションエラーとなる', function () {
    $validator = Validator::make(
        [
            'name' => 'テストユーザー',
            'email' => 'new@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different',
            'role' => UserRole::STAFF->value,
        ],
        (new UserStoreRequest)->rules()
    );

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('password'))->toBeTrue();
});

it('[UserStoreRequestTest]-[004] roleが不正な値の場合はバリデーションエラーとなる', function () {
    $validator = Validator::make(
        [
            'name' => 'テストユーザー',
            'email' => 'new@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 999,
        ],
        (new UserStoreRequest)->rules()
    );

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('role'))->toBeTrue();
});

describe('[UserStoreRequest]::[authorize]', function () {
    it('[UserStoreRequestTest]-[005] ADMINはtrueを返す', function () {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        $request = new UserStoreRequest;
        $request->setUserResolver(fn () => $admin);

        expect($request->authorize())->toBeTrue();
    });

    it('[UserStoreRequestTest]-[006] ADMIN以外はfalseを返す', function (UserRole $role) {
        $user = User::factory()->create(['role' => $role]);

        $request = new UserStoreRequest;
        $request->setUserResolver(fn () => $user);

        expect($request->authorize())->toBeFalse();
    })->with([
        'STAFF' => UserRole::STAFF,
        'SHOP' => UserRole::SHOP,
    ]);
});
