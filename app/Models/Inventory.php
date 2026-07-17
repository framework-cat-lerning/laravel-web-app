<?php

namespace App\Models;

use App\Policies\InventoryPolicy;
use Database\Factories\InventoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $product_id
 * @property int $quantity
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'product_id', 'quantity'])]
#[UsePolicy(InventoryPolicy::class)]
class Inventory extends Model
{
    /** @use HasFactory<InventoryFactory> */
    use HasFactory, SoftDeletes;

    // リレーション
}
