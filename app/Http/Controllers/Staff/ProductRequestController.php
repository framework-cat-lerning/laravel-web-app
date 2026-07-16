<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\ProductResource;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductRequestController extends Controller
{
    use AuthorizesRequests;

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
            'products' => ProductResource::collection($user->requestProducts)->resource,
        ]);
    }

    /**
     * 商品申請作成画面
     */
    public function new() {}

    /**
     * 商品申請作成
     */
    public function store() {}

    /**
     * 商品申請キャンセル
     */
    public function cancel() {}
}
