<?php

use App\Enums\UserRole;
use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('[UserPolicy]::[viewAny / view]', function () {
    it('[UserPolicyTest]-[001] ログイン中であれば常にtrueを返す', function () {
        $policy = new UserPolicy;

        $user = User::factory()->create();
        $model = User::factory()->create();

        $this->actingAs($user);

        expect($policy->viewAny($user))->toBeTrue()
            ->and($policy->view($user, $model))->toBeTrue();
    });
});

describe('[UserPolicy]::[create]', function () {
    it('[UserPolicyTest]-[002] ADMINならtrueを返す', function () {
        $policy = new UserPolicy;

        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $this->actingAs($admin);

        expect($policy->create($admin))->toBeTrue();
    });

    it('[UserPolicyTest]-[003] ADMIN以外はfalseを返す', function (UserRole $role) {
        $policy = new UserPolicy;

        $user = User::factory()->create(['role' => $role]);
        $this->actingAs($user);

        expect($policy->create($user))->toBeFalse();
    })->with([
        'STAFF' => UserRole::STAFF,
        'SHOP' => UserRole::SHOP,
    ]);
});

describe('[UserPolicy]::[update / delete]', function () {
    it('[UserPolicyTest]-[004] ADMINは他人でもtrueを返す', function () {
        $policy = new UserPolicy;

        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $other = User::factory()->create();
        $this->actingAs($admin);

        expect($policy->update($admin, $other))->toBeTrue()
            ->and($policy->delete($admin, $other))->toBeTrue();
    });

    it('[UserPolicyTest]-[005] 本人であればtrueを返す', function () {
        $policy = new UserPolicy;

        $user = User::factory()->create(['role' => UserRole::STAFF]);
        $this->actingAs($user);

        expect($policy->update($user, $user))->toBeTrue()
            ->and($policy->delete($user, $user))->toBeTrue();
    });

    it('[UserPolicyTest]-[006] ADMINでも本人でもない場合はfalseを返す', function () {
        $policy = new UserPolicy;

        $user = User::factory()->create(['role' => UserRole::STAFF]);
        $other = User::factory()->create();
        $this->actingAs($user);

        expect($policy->update($user, $other))->toBeFalse()
            ->and($policy->delete($user, $other))->toBeFalse();
    });
});

describe('[UserPolicy]::[restore / forceDelete]', function () {
    it('[UserPolicyTest]-[007] 常にfalseを返す', function () {
        $policy = new UserPolicy;

        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $this->actingAs($admin);

        expect($policy->restore($admin, $admin))->toBeFalse()
            ->and($policy->forceDelete($admin, $admin))->toBeFalse();
    });
});
