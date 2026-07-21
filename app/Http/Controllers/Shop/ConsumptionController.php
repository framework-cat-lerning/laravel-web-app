<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shop\ConsumptionProductRequest;
use App\Http\Resources\Shop\ProductConsumptionResource;
use App\Models\Product;
use App\Services\InventoryService;
use App\Services\ProductService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ConsumptionController extends Controller
{
    public function __construct(
        protected InventoryService $inventoryService
    ) {}

    /**
     * 商品一覧
     */
    public function index(): Response
    {
        $products = Product::isApproval()->isInvetory()->get();

        return Inertia::render('consumption/index', [
            'products' => ProductConsumptionResource::collection($products),
        ]);
    }

    /**
     * 商品の販売
     */
    public function consumption(Product $product, ConsumptionProductRequest $request): RedirectResponse
    {
        $this->inventoryService->consumption($product, $request);

        return redirect()->route('shop.products.index');
    }
}
