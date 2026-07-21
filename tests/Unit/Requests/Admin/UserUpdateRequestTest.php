<?php

use App\Enums\UserRole;
use App\Http\Requests\Admin\UserUpdateRequest;
use Illuminate\Support\Facades\Validator;

it('[UserUpdateRequestTest]-[001] 正しい形式であればバリデーションを通過する', function () {
    $validator = Validator::make(
        ['name' => 'テストユーザー', 'email' => 'test@example.com', 'role' => UserRole::STAFF->value],
        (new UserUpdateRequest)->rules()
    );

    expect($validator->passes())->toBeTrue();
});

it('[UserUpdateRequestTest]-[002] passwordは未入力でも通過する', function () {
    $validator = Validator::make(
        ['name' => 'テストユーザー', 'email' => 'test@example.com', 'role' => UserRole::STAFF->value],
        (new UserUpdateRequest)->rules()
    );

    expect($validator->passes())->toBeTrue();
});

it('[UserUpdateRequestTest]-[003] passwordを指定する場合は確認が一致しないとバリデーションエラーとなる', function () {
    $validator = Validator::make(
        [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'role' => UserRole::STAFF->value,
            'password' => 'password123',
            'password_confirmation' => 'different',
        ],
        (new UserUpdateRequest)->rules()
    );

    expect($validator->fails())->toBeTrue();
});

it('[UserUpdateRequestTest]-[004] roleが不正な値の場合はバリデーションエラーとなる', function () {
    $validator = Validator::make(
        ['name' => 'テストユーザー', 'email' => 'test@example.com', 'role' => 999],
        (new UserUpdateRequest)->rules()
    );

    expect($validator->fails())->toBeTrue();
});

it('[UserUpdateRequestTest]-[005] authorizeは常にtrueを返す', function () {
    expect((new UserUpdateRequest)->authorize())->toBeTrue();
});
