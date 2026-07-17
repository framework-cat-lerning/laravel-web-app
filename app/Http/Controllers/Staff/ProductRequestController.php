<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\ProductStoreRequest;
use App\Http\Resources\Admin\ProductResource;
use App\Models\Product;
use App\Models\User;
use App\Services\ProductService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductRequestController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected ProductService $productService
    ) {}

    /**
     * 商品申請一覧画面
     */
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Product::class);

        /** @var User */
        $user = $request->user();

        // 権限別のダッシュボードを表示
        return Inertia::render('products/index', [
            'products' => ProductResource::collection($user->requestProducts),
        ]);
    }

    /**
     * 商品申請作成画面
     */
    public function new(): Response
    {
        $this->authorize('create', Product::class);

        return Inertia::render('products/form', [
            'product' => new Product,
            'form_type' => 'new',
        ]);
    }

    /**
     * 商品申請作成
     */
    public function store(ProductStoreRequest $request): RedirectResponse
    {
        $this->productService->store($request);

        return redirect()->route('staff.products.index');
    }

    /**
     * 商品申請キャンセル
     */
    public function cancel() {}
}
