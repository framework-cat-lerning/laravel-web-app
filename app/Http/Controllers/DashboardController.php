<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        /** @var User */
        $user = $request->user();

        // 権限別のダッシュボードを表示
        return Inertia::render("{$user->role->dir()}/dashboard");
    }
}
