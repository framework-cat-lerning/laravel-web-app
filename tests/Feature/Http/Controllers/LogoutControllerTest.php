<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

describe('LogoutController', function () {
    it('[LogoutControllerTest]-[001] ログイン中のユーザーがログアウトでき、loginページへリダイレクトされる', function () {
        $user = User::factory()->create();

        actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    });

    it('[LogoutControllerTest]-[002] 未ログインの場合はauthミドルウェアによりloginページへリダイレクトされる', function () {
        post(route('logout'))
            ->assertRedirect(route('login'));
    });
});