<?php

namespace App\Services;

use App\Http\Requests\Shop\ConsumptionProductRequest;
use App\Models\ConsumptionLog;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
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
                $consumptionLog = new ConsumptionLog;
                $consumptionLog->fill([
                    'consumption_at' => Carbon::now(),
                    'quantity' => $request->input('count'),
                    'user_id' => $this->getUserID($request),
                ]);
                $product->consumptionLogs()->save($consumptionLog);

                return true;
            });
        } catch (Throwable $e) {
            report($e);

            throw $e;
        }
    }

    /**
     * ユーザID
     */
    public function getUserID(Request $request): int
    {
        /** @var User|null */
        $user = $request->user();
        if (empty($user)) {
            throw new Exception('Not found.');
        }

        return $user->id;
    }
}
