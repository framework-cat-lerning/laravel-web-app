<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\InventoryBuyRequest;
use App\Http\Resources\Staff\ProductInventoryResource;
use App\Models\Inventory;
use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class InventoryController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected InventoryService $inventoryService
    ) {}

    /**
     * 商品申請一覧画面
     */
    public function index(): Response
    {
        $this->authorize('viewAny', Inventory::class);

        $products = Product::isApproval()->get();

        // 権限別のダッシュボードを表示
        return Inertia::render('inventory/index', [
            'inventories' => ProductInventoryResource::collection($products),
        ]);
    }

    /**
     * 在庫の更新
     */
    public function buy(InventoryBuyRequest $request, Product $product): RedirectResponse
    {
        $this->authorize('create', Inventory::class);
        $this->inventoryService->buy($request, $product);

        return redirect()->route('staff.inventries.index');
    }
}
