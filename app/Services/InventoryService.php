<?php

namespace App\Services;

use App\Http\Requests\Shop\ConsumptionProductRequest;
use App\Http\Requests\Staff\InventoryBuyRequest;
use App\Models\Inventory;
use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

class InventoryService
{
    public function __construct(
        protected ConsumptionLogService $consumptionLogService
    )
    {}

    /**
     * 在庫の購入
     */
    public function buy(InventoryBuyRequest $request, Product $product): bool
    {
        try {
            return DB::transaction(function () use ($request, $product): bool {
                /** @var int|null */
                $count = $request->input('count');
                if (! $count) {
                    return false;
                }

                /** @var Inventory|null */
                $inventory = $product->inventory;
                if ($inventory) {
                    $inventory->quantity += $count;
                } else {
                    $inventory = new Inventory;
                    $inventory->quantity = $count;
                }
                $product->inventory()->save($inventory);

                return true;

            });
        } catch (Throwable $e) {
            report($e);

            throw $e;
        }
    }

    /**
     * 商品の販売
     */
    public function consumption(Product $product, ConsumptionProductRequest $request): bool
    {
        try {
            return DB::transaction(function () use ($product, $request): bool {
                $count = $request->input("count");
                /** @var Inventory */
                $inventory = $product->inventory;
                if ($inventory->quantity < $count) {
                    throw new Exception("在庫数が不足してます");
                }
                $inventory->quantity -= $count;
                $inventory->save();

                $this->consumptionLogService->create($product, $request);

                return true;
            });
        } catch (Throwable $e) {
            report($e);

            throw $e;
        }
    }
}
