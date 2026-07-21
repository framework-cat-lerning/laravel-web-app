<?php

namespace App\Http\Resources\Shop;

use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Product $resource
 */
class ProductConsumptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'description' => $this->resource->description,
            'price' => $this->resource->price,
            'quantity' => $this->getQuantity(),
            'created_at' => $this->resource->created_at?->isoFormat('YYYY/MM/DD'),
            'updated_at' => $this->resource->updated_at?->isoFormat('YYYY/MM/DD'),
        ];
    }

    /**
     * 在庫数チェック
     */
    private function getQuantity(): int
    {
        /** @var Inventory|null */
        $inventory = $this->resource->inventory;
        if ($inventory) {
            return $inventory->quantity;
        } else {
            return 0;
        }
    }
}
