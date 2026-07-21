<?php

namespace App\Http\Requests\Shop;

use App\Models\ConsumptionLog;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ConsumptionProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var Product $product */
        $product = $this->route('product');

        $inventory = $product->inventory;

        if ($inventory === null) {
            return false;
        }

        $inventoryPolicy = $this->user()?->can('update', $inventory) ?? false;
        $consumptionPolicy = $this->user()?->can('create', ConsumptionLog::class) ?? false;

        return $inventoryPolicy && $consumptionPolicy;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'count' => ['required', 'integer', 'min:1'],
        ];
    }
}