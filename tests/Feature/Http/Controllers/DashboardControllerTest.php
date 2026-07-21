<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

describe('[DashboardController]', function () {
    it('[DashboardControllerTest]-[001] ログイン中のユーザーはdashboardページを表示できる', function () {
        $user = User::factory()->create();

        actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('dashboard'));
    });

    it('[DashboardControllerTest]-[002] 未ログインの場合はloginページへリダイレクトされる', function () {
        get(route('dashboard'))
            ->assertRedirect(route('login'));
    });

    it('[DashboardControllerTest]-[003] どのroleのユーザーでもdashboardページにアクセスできる', function (UserRole $role) {
        $user = User::factory()->create(['role' => $role]);

        actingAs($user)
            ->get(route('dashboard'))
            ->assertOk();
    })->with([
        'ADMIN' => UserRole::ADMIN,
        'STAFF' => UserRole::STAFF,
        'SHOP' => UserRole::SHOP,
    ]);
});
