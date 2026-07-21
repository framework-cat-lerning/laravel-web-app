<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductUpdateRequest;
use App\Http\Resources\Admin\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected ProductService $productService
    ) {}

    /**
     * 商品一覧
     */
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Product::class);
        /** @var string */
        $sort = $request->input('sort', 'name');
        /** @var 'asc'|'desc' */
        $direction = $request->input('direction', 'asc');

        // 取得
        $products = Product::query()->orderBy($sort, $direction)->get();

        // 権限別のダッシュボードを表示
        return Inertia::render('products/index', [
            'products' => ProductResource::collection($products),
        ]);
    }

    /**
     * 商品詳細
     */
    public function show(Product $product): Response
    {
        $this->authorize('view', $product);

        // 権限別のダッシュボードを表示
        return Inertia::render('products/show', [
            'product' => ProductResource::make($product),
        ]);
    }

    /**
     * 商品編集画面
     */
    public function edit(Product $product): Response
    {
        $this->authorize('update', $product);

        return Inertia::render('products/form', [
            'product' => $product,
            'form_type' => 'edit',
        ]);
    }

    /**
     * 商品更新
     */
    public function update(Product $product, ProductUpdateRequest $request): RedirectResponse
    {
        $this->authorize('update', $product);

        $this->productService->update($request, $product);

        return redirect()->route('admin.products.index');
    }

    /**
     * 商品削除
     */
    public function delete(Product $product): RedirectResponse
    {
        $this->authorize('delete', $product);

        $this->productService->delete($product);

        return redirect()->route('admin.products.index');
    }

    /**
     * 商品申請許可
     */
    public function approval(Product $product): RedirectResponse
    {
        $this->authorize('approval', $product);

        $this->productService->approval($product);

        return redirect()->route('admin.products.index');
    }
}
