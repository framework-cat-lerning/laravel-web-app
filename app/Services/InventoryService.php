<?php

namespace App\Services;

use App\Http\Requests\Staff\InventoryBuyRequest;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Throwable;

class InventoryService
{
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
}
