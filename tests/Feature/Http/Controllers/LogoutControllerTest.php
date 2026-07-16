<?php

use App\Models\User;

test('認証済みユーザはログアウトできる', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('logout'));

    $this->assertGuest();
    $response->assertRedirect(route('login'));
});

test('ゲストはログアウトにアクセスするとログイン画面へリダイレクトされる', function () {
    $this->post(route('logout'))->assertRedirect(route('login'));
});
