<?php

namespace App\Data\Log;

use Spatie\LaravelData\Data;

class ConsumptionLogData extends Data
{
    public function __construct(
        public int $id,
        public string $product_name,
        public string $consumption_at,
        public int $quantity,
        public int $total_amount
    ) {}

    /**
     * テーブルカラム
     *
     * @return array<int, array<string, int|string>>
     */
    public static function GetColumns()
    {
        return [
            [
                'field' => 'product_name',
                'headerName' => '商品名',
                'headerAlign' => 'left',
                'align' => 'left',
                'flex' => 1,
                'minWidth' => 200,
            ], [
                'field' => 'consumption_at',
                'headerName' => '購入日時',
                'headerAlign' => 'left',
                'align' => 'left',
                'flex' => 1,
                'minWidth' => 200,
            ], [
                'field' => 'quantity',
                'headerName' => '購入数',
                'headerAlign' => 'right',
                'align' => 'right',
                'flex' => 1,
                'minWidth' => 200,
            ], [
                'field' => 'total_amount',
                'headerName' => '購入合計金額（参照）',
                'headerAlign' => 'right',
                'align' => 'right',
                'flex' => 1,
                'minWidth' => 200,
            ],
        ];
    }
}
