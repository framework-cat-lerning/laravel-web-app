<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

uses(RefreshDatabase::class);

describe('[LoginController]::[index]', function () {
    it('[LoginControllerTest]-[001] 未ログイン状態でログインページを表示できる', function () {
        get(route('login'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('login'));
    });

    it('[LoginControllerTest]-[002] ログイン済みの場合はログインページにアクセスできずリダイレクトされる', function () {
        $user = User::factory()->create();

        actingAs($user)
            ->get(route('login'))
            ->assertRedirect();
    });
});

describe('[LoginController]::[loggedIn]', function () {
    beforeEach(function () {
        RateLimiter::clear('test@example.com|127.0.0.1');
    });

    it('[LoginControllerTest]-[003] 正しい認証情報でログインでき、dashboardへリダイレクトされる', function () {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        post(route('logged-in'), [
            'email' => 'test@example.com',
            'password' => 'password123',
        ])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    });

    it('[LoginControllerTest]-[004] 誤ったパスワードの場合はバリデーションエラーとなり、ログインされない', function () {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        post(route('logged-in'), [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    });

    it('[LoginControllerTest]-[005] 存在しないメールアドレスの場合はバリデーションエラーとなる', function () {
        post(route('logged-in'), [
            'email' => 'notfound@example.com',
            'password' => 'password123',
        ])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    });

    it('[LoginControllerTest]-[006] emailが未入力の場合はバリデーションエラーとなる', function () {
        post(route('logged-in'), [
            'password' => 'password123',
        ])
            ->assertSessionHasErrors('email');
    });

    it('[LoginControllerTest]-[007] passwordが未入力の場合はバリデーションエラーとなる', function () {
        post(route('logged-in'), [
            'email' => 'test@example.com',
        ])
            ->assertSessionHasErrors('password');
    });

    it('[LoginControllerTest]-[008] 5回連続でログインに失敗すると6回目はレート制限エラーとなる', function () {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        for ($i = 0; $i < 5; $i++) {
            post(route('logged-in'), [
                'email' => 'test@example.com',
                'password' => 'wrong-password',
            ])->assertSessionHasErrors('email');
        }

        $response = post(route('logged-in'), [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $errors = session('errors');
        expect($errors->first('email'))->toContain('ログイン試行回数が多すぎます');
    });

    it('[LoginControllerTest]-[009] intendedなURLが設定されている場合はそちらへリダイレクトされる', function () {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        // intended URLをセッションに設定
        get(route('login')); // guestとしてアクセスしセッション初期化
        session(['url.intended' => route('admin.products.index')]);

        post(route('logged-in'), [
            'email' => 'test@example.com',
            'password' => 'password123',
        ])
            ->assertRedirect(route('admin.products.index'));
    });
});
