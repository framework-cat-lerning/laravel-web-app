<?php

use App\Models\User;

test('ゲストはログイン画面を表示できる', function () {
    $this->get(route('login'))->assertOk();
});

test('認証済みユーザはログイン画面からリダイレクトされる', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('login'))
        ->assertRedirect(route('dashboard'));
});

test('正しい認証情報でログインできる', function () {
    $user = User::factory()->create();

    $response = $this->post(route('logged-in'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticatedAs($user);
    $response->assertRedirect(route('dashboard'));
});

test('誤ったパスワードではログインできない', function () {
    $user = User::factory()->create();

    $this->post(route('logged-in'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('メールアドレスとパスワードは必須', function () {
    $this->post(route('logged-in'), [])
        ->assertSessionHasErrors(['email', 'password']);

    $this->assertGuest();
});
