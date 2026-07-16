<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class LoginController extends Controller
{
    /**
     * ログインフォーム
     */
    public function index(): Response
    {
        return Inertia::render('login');
    }

    /**
     * ログイン処理
     */
    public function loggedIn(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('home'));
    }
}
