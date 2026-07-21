<?php

namespace App\Models;

use App\Policies\ConsumptionLogPolicy;
use Database\Factories\ConsumptionLogFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $product_id
 * @property Carbon $consumption_at
 * @property int $quantity
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable('product_id', 'consumption_at', 'quantity', 'user_id')]
#[UseFactory(ConsumptionLogFactory::class)]
#[UsePolicy(ConsumptionLogPolicy::class)]
class ConsumptionLog extends Model
{
    /** @use HasFactory<ConsumptionLogFactory> */
    use HasFactory;

    // リレーション
    /**
     * 商品
     *
     * @return BelongsTo<User, $this>
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    /**
     * 商品
     *
     * @return BelongsTo<Product, $this>
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
