<?php

use App\Models\User;

test('[LoginControllerTest]-[001] ゲストはログイン画面を表示できる', function () {
    $this->get(route('login'))->assertOk();
});

test('[LoginControllerTest]-[002] 認証済みユーザはログイン画面からリダイレクトされる', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('login'))
        ->assertRedirect(route('dashboard'));
});

test('[LoginControllerTest]-[003] 正しい認証情報でログインできる', function () {
    $user = User::factory()->create();

    $response = $this->post(route('logged-in'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticatedAs($user);
    $response->assertRedirect(route('dashboard'));
});

test('[LoginControllerTest]-[004] 誤ったパスワードではログインできない', function () {
    $user = User::factory()->create();

    $this->post(route('logged-in'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('[LoginControllerTest]-[005] メールアドレスとパスワードは必須', function () {
    $this->post(route('logged-in'), [])
        ->assertSessionHasErrors(['email', 'password']);

    $this->assertGuest();
});
