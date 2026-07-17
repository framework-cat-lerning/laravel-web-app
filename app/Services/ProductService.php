<?php

namespace App\Services;

use App\Enums\ProductStatus;
use App\Http\Requests\Staff\ProductStoreRequest;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Throwable;

class ProductService
{
    /**
     * 新規保存処理
     */
    public function store(ProductStoreRequest $request): Product
    {
        try {
            return DB::transaction(function () use ($request): Product {
                /** @var User $user */
                $user = $request->user();

                $product = new Product;
                $product->fill($request->validated());
                $product->status = ProductStatus::PENDING;

                $user->requestProducts()->save($product);

                return $product;
            });
        } catch (Throwable $e) {
            report($e);

            throw $e;
        }
    }

    /**
     * 商品の承認
     */
    public  function approval(Product $product): bool
    {
        try {
            return DB::transaction(function () use ($product): bool {
                $product->status = ProductStatus::APPROVED;
                $product->save();

                return true;
            });
        } catch (Throwable $e) {
            report($e);

            throw $e;
        }
    }
}
