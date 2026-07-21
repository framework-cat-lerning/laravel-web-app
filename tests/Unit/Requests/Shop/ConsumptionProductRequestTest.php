<?php

use App\Http\Requests\Shop\ConsumptionProductRequest;
use Illuminate\Support\Facades\Validator;

it('[ConsumptionProductRequestTest]-[001] countが1以上の整数であればバリデーションを通過する', function () {
    $validator = Validator::make(['count' => 1], (new ConsumptionProductRequest)->rules());

    expect($validator->passes())->toBeTrue();
});

it('[ConsumptionProductRequestTest]-[002] countが0の場合はバリデーションエラーとなる', function () {
    $validator = Validator::make(['count' => 0], (new ConsumptionProductRequest)->rules());

    expect($validator->fails())->toBeTrue();
});

it('[ConsumptionProductRequestTest]-[003] countが未入力の場合はバリデーションエラーとなる', function () {
    $validator = Validator::make([], (new ConsumptionProductRequest)->rules());

    expect($validator->fails())->toBeTrue();
});
