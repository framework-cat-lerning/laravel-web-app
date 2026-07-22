<?php

namespace App\Services\Dashboard;

use App\Data\Log\ConsumptionLogData;
use App\Data\Log\InventoryLogData;
use App\Models\ConsumptionLog;
use App\Models\Inventory;
use App\Models\Product;
use Carbon\Carbon;

class LogTableService
{
    /**
     * @return array{
     *     columns: array<int, array<string, int|string>>,
     *     rows: array<int, ConsumptionLogData>,
     * }
     */
    public function getConsumptionLogTableData(): array
    {
        $rows = [];

        $consumptionLogs = ConsumptionLog::query()
            ->orderBy('consumption_at', 'desc')
            ->get();

        /** @var ConsumptionLog $consumptionLog */
        foreach ($consumptionLogs as $consumptionLog) {
            /** @var Product|null $product */
            $product = $consumptionLog->product;

            /** @var Carbon $consumptionAt */
            $consumptionAt = $consumptionLog->consumption_at;

            $rows[] = new ConsumptionLogData(
                id: $consumptionLog->id,
                product_name: $this->getProductName($product),
                consumption_at: $consumptionAt->format('Y/m/d H:i:s'),
                quantity: $consumptionLog->quantity,
                total_amount: $this->getTotalAmount($product, $consumptionLog->quantity),
            );
        }

        return [
            'columns' => ConsumptionLogData::getColumns(),
            'rows' => $rows,
        ];
    }

    /**
     * @return array{
     *     columns: array<int, array<string, int|string>>,
     *     rows: array<int, InventoryLogData>,
     * }
     */
    public function getInventoryLogTableData(): array
    {
        $rows = [];

        $inventories = Inventory::query()
            ->orderBy('updated_at', 'desc')
            ->get();

        /** @var Inventory $inventory */
        foreach ($inventories as $inventory) {
            /** @var Product|null $product */
            $product = $inventory->product;

            $rows[] = new InventoryLogData(
                id: $inventory->id,
                product_name: $this->getProductName($product),
                quantity: $inventory->quantity,
                updated_at: empty($inventory->updated_at) ? '' : $inventory->updated_at->format('Y/m/d H:i:s')
            );
        }

        return [
            'columns' => InventoryLogData::getColumns(),
            'rows' => $rows,
        ];
    }

    /**
     * 商品名
     */
    private function getProductName(?Product $product): string
    {
        if ($product) {
            return $product->name;
        }

        return '削除済みアイテム';
    }

    /**
     * 合計金額
     */
    private function getTotalAmount(?Product $product, int $quantity): int
    {
        if ($product) {
            return $product->price * $quantity;
        }

        return 0;
    }
}
