<?php

namespace App\Services;

use App\Http\Requests\Shop\ConsumptionProductRequest;
use App\Models\ConsumptionLog;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

class ConsumptionLogService
{
    /**
     * 購入ログの追加
     */
    public function create(Product $product, ConsumptionProductRequest $request): bool
    {
        try {
            return DB::transaction(function () use ($product, $request): bool {
                // ログの生成
                $consumptionLog = new ConsumptionLog();
                $consumptionLog->fill([
                    'consumption_at' => Carbon::now(),
                    'quantity' => $request->input("count"),
                    'user_id' => $request->user()->id,
                ]);
                $product->consumptionLogs()->save($consumptionLog);

                return true;
            });
        } catch (Throwable $e) {
            report($e);

            throw $e;
        }
    }
}
