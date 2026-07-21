<?php

use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Validator;

it('[LoginRequestTest]-[001] 正しい形式であればバリデーションを通過する', function () {
    $validator = Validator::make(
        ['email' => 'test@example.com', 'password' => 'password'],
        (new LoginRequest)->rules()
    );

    expect($validator->passes())->toBeTrue();
});

it('[LoginRequestTest]-[002] emailが未入力の場合はバリデーションエラーとなる', function () {
    $validator = Validator::make(
        ['password' => 'password'],
        (new LoginRequest)->rules()
    );

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('email'))->toBeTrue();
});

it('[LoginRequestTest]-[003] email形式が不正な場合はバリデーションエラーとなる', function () {
    $validator = Validator::make(
        ['email' => 'not-an-email', 'password' => 'password'],
        (new LoginRequest)->rules()
    );

    expect($validator->fails())->toBeTrue();
});

it('[LoginRequestTest]-[004] passwordが未入力の場合はバリデーションエラーとなる', function () {
    $validator = Validator::make(
        ['email' => 'test@example.com'],
        (new LoginRequest)->rules()
    );

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('password'))->toBeTrue();
});

it('[LoginRequestTest]-[005] authorizeは常にtrueを返す', function () {
    expect((new LoginRequest)->authorize())->toBeTrue();
});
