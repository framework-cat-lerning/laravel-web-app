<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    use AuthorizesRequests;

    /**
     * 商品一覧
     */
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Product::class);
        // 権限別のダッシュボードを表示
        return Inertia::render('products/index');
    }

    /**
     * 商品詳細
     */
    public function show() {}

    /**
     * 商品編集画面
     */
    public function edit() {}

    /**
     * 商品更新
     */
    public function update() {}

    /**
     * 商品削除
     */
    public function delete() {}

    /**
     * 商品申請許可
     */
    public function approval() {}
}
