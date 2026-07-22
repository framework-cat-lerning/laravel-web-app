<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Dashboard\ChartService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        protected ChartService $chartService
    ) {}

    public function __invoke(Request $request): Response
    {
        /** @var User */
        $user = $request->user();

        $chartResourse = [];
        if ($user->role->isAdmin()) {
            $chartResourse['consumptions'] = [];

        } elseif ($user->role->isStaff()) {
            $chartResourse['products'] = $this->chartService->getProductData();

        } elseif ($user->role->isShop()) {
            $chartResourse['consumptions'] = [];
        }

        // 権限別のダッシュボードを表示
        return Inertia::render('dashboard', [
            'charts' => $chartResourse,
        ]);
    }
}
