<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Resources\Shop\ProductConsumptionResource;
use App\Models\Product;
use App\Services\ProductService;
use Inertia\Inertia;
use Inertia\Response;

class ConsumptionController extends Controller
{
    public function __construct(
        protected ProductService $productService
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
    public function consumption() {}
}
