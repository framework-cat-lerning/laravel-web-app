<?php

use App\Http\Requests\Admin\ProductUpdateRequest;
use Illuminate\Support\Facades\Validator;

it('[ProductUpdateRequestTest]-[001] 正しい形式であればバリデーションを通過する', function () {
    $validator = Validator::make(
        ['name' => '商品名', 'description' => '説明', 'price' => 100],
        (new ProductUpdateRequest)->rules()
    );

    expect($validator->passes())->toBeTrue();
});

it('[ProductUpdateRequestTest]-[002] nameが未入力の場合はバリデーションエラーとなる', function () {
    $validator = Validator::make(
        ['price' => 100],
        (new ProductUpdateRequest)->rules()
    );

    expect($validator->fails())->toBeTrue();
});

it('[ProductUpdateRequestTest]-[003] priceが1未満の場合はバリデーションエラーとなる', function () {
    $validator = Validator::make(
        ['name' => '商品名', 'price' => 0],
        (new ProductUpdateRequest)->rules()
    );

    expect($validator->fails())->toBeTrue();
});
