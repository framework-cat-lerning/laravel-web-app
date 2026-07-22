<?php

namespace App\Data\Log;

use Spatie\LaravelData\Data;

class InventoryLogData extends Data
{
    public function __construct(
        public int $id,
        public string $product_name,
        public int $quantity,
        public string $updated_at
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
                'field' => 'quantity',
                'headerName' => '在庫数',
                'headerAlign' => 'right',
                'align' => 'right',
                'flex' => 1,
                'minWidth' => 200,
            ], [
                'field' => 'updated_at',
                'headerName' => '更新日時',
                'headerAlign' => 'center',
                'align' => 'right',
                'flex' => 1,
                'minWidth' => 200,
            ],
        ];
    }
}
