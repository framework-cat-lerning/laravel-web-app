<?php

namespace App\Models;

use App\Enums\ProductStatus;
use App\Policies\ProductPolicy;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $price
 * @property ProductStatus $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $request_user_id
 */
#[Fillable(['name', 'description', 'price', 'status'])]
#[Hidden(['status'])]
#[UseFactory(ProductFactory::class)]
#[UsePolicy(ProductPolicy::class)]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ProductStatus::class,
        ];
    }

    // リレーション
    /**
     * 在庫管理
     *
     * @return HasOne<Inventory, $this>
     */
    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }

    /**
     * 購入履歴
     *
     * @return HasMany<ConsumptionLog, $this>
     */
    public function consumptionLogs()
    {
        return $this->hasMany(ConsumptionLog::class);
    }

    // スコープ
    /**
     * 承認すみか
     *
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopeIsApproval(Builder $query)
    {
        return $query->where('status', ProductStatus::APPROVED);
    }

    /**
     * 在庫数があるか
     *
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopeIsInvetory(Builder $query)
    {
        return $query->whereHas('inventory', function (Builder $query) {
            $query->where('quantity', '>', 0);
        });
    }
}
