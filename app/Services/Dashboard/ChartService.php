<?php

namespace App\Services\Dashboard;

use App\Data\Chart\ProductData;
use App\Models\ConsumptionLog;
use App\Models\Product;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class ChartService
{
    /**
     * 商品ごとの日別消費数量チャートデータを取得する
     *
     * @return array<int, ProductData>
     */
    public function getProductData(): array
    {
        $endDate = CarbonImmutable::today();
        $startDate = $endDate->subDays(6);

        $products = Product::isApproval()->get();

        $dataList = [];

        /** @var Product $product */
        foreach ($products as $product) {
            $dailySum = $this->getDailyQuantitySum($product->id, $startDate, $endDate);
            /** @var string */
            $sumVal = $dailySum->sum();

            $dataList[] = new ProductData(
                title: $product->name,
                value: $sumVal,
                interval: 'Last 7 days',
                trend: $this->calculateTrend($dailySum),
                data: $dailySum->values()->all(),
            );
        }

        return $dataList;
    }

    /**
     * 指定商品・期間内の日別消費数量合計を取得する（欠損日は0で補完）
     *
     * @return Collection<string, int> key: 'Y-m-d', value: quantity合計
     */
    private function getDailyQuantitySum(
        int $productId,
        CarbonImmutable $startDate,
        CarbonImmutable $endDate,
    ): Collection {
        /** @var Collection<int, object{date: string, total_quantity: string}> $rows */
        $rows = ConsumptionLog::query()
            ->toBase()
            ->selectRaw('DATE(consumption_at) as date, SUM(quantity) as total_quantity')
            ->where('product_id', $productId)
            ->whereBetween('consumption_at', [
                $startDate->startOfDay(),
                $endDate->endOfDay(),
            ])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        /** @var Collection<string, int> $results */
        $results = collect();

        foreach ($rows as $row) {
            $key = CarbonImmutable::parse($row->date)->format('Y-m-d');
            $results->put($key, (int) $row->total_quantity);
        }

        return $this->fillMissingDates($results, $startDate, $endDate);
    }

    /**
     * 期間内の日付のうちデータがない日を0で埋める
     *
     * @param  Collection<string, int>  $results
     * @return Collection<string, int>
     */
    private function fillMissingDates(
        Collection $results,
        CarbonImmutable $startDate,
        CarbonImmutable $endDate,
    ): Collection {
        /** @var Collection<string, int> $filled */
        $filled = collect();

        $period = $startDate->toPeriod($endDate);

        foreach ($period as $date) {
            /** @var CarbonImmutable $date */
            $key = $date->format('Y-m-d');
            $filled->put($key, $results->get($key, 0));
        }

        return $filled;
    }

    /**
     * 前半期間と後半期間の合計を比較してトレンドを判定する
     *
     * @param  Collection<string, int>  $dailySum
     */
    private function calculateTrend(Collection $dailySum): string
    {
        $values = $dailySum->values();
        $half = (int) ceil($values->count() / 2);

        $firstHalf = $values->take($half)->sum();
        $secondHalf = $values->skip($half)->sum();

        return $secondHalf >= $firstHalf ? 'up' : 'down';
    }
}
