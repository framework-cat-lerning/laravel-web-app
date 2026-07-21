<?php

use App\Enums\ProductStatus;
use App\Enums\UserRole;
use App\Models\Product;
use App\Models\User;
use App\Policies\ProductPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('[ProductPolicy]::[viewAny / view]', function () {
    it('[ProductPolicy]-[001] 全roleで常にtrueを返す', function (UserRole $role) {
        $policy = new ProductPolicy;

        $user = User::factory()->create(['role' => $role]);
        $product = Product::factory()->create();

        expect($policy->viewAny($user))->toBeTrue()
            ->and($policy->view($user, $product))->toBeTrue();
    })->with([
        'ADMIN' => UserRole::ADMIN,
        'STAFF' => UserRole::STAFF,
        'SHOP' => UserRole::SHOP,
    ]);
});

describe('[ProductPolicy]::[create]', function () {
    it('[ProductPolicy]-[002] STAFFのみtrueを返す', function (UserRole $role, bool $expected) {
        $policy = new ProductPolicy;

        $user = User::factory()->create(['role' => $role]);

        expect($policy->create($user))->toBe($expected);
    })->with([
        'ADMIN' => [UserRole::ADMIN, false],
        'STAFF' => [UserRole::STAFF, true],
        'SHOP' => [UserRole::SHOP, false],
    ]);
});

describe('[ProductPolicy]::[update]', function () {
    it('[ProductPolicy]-[003] ADMINのみtrueを返す', function (UserRole $role, bool $expected) {
        $policy = new ProductPolicy;

        $user = User::factory()->create(['role' => $role]);
        $product = Product::factory()->create();

        expect($policy->update($user, $product))->toBe($expected);
    })->with([
        'ADMIN' => [UserRole::ADMIN, true],
        'STAFF' => [UserRole::STAFF, false],
        'SHOP' => [UserRole::SHOP, false],
    ]);
});

describe('[ProductPolicy]::[delete]', function () {
    it('[ProductPolicy]-[004] ADMINは商品の所有者でなくてもtrueを返す', function () {
        $policy = new ProductPolicy;

        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $other = User::factory()->create();
        $product = Product::factory()->create(['request_user_id' => $other->id]);

        expect($policy->delete($admin, $product))->toBeTrue();
    });

    it('[ProductPolicy]-[005] 申請者本人であればtrueを返す', function () {
        $policy = new ProductPolicy;

        $requester = User::factory()->create(['role' => UserRole::STAFF]);
        $product = Product::factory()->create(['request_user_id' => $requester->id]);

        expect($policy->delete($requester, $product))->toBeTrue();
    });

    it('[ProductPolicy]-[006] ADMINでも申請者本人でもない場合はfalseを返す', function () {
        $policy = new ProductPolicy;

        $user = User::factory()->create(['role' => UserRole::STAFF]);
        $other = User::factory()->create();
        $product = Product::factory()->create(['request_user_id' => $other->id]);

        expect($policy->delete($user, $product))->toBeFalse();
    });
});

describe('[ProductPolicy]::[restore / forceDelete]', function () {
    it('[ProductPolicy]-[007] 常にfalseを返す', function () {
        $policy = new ProductPolicy;

        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $product = Product::factory()->create();

        expect($policy->restore($admin, $product))->toBeFalse()
            ->and($policy->forceDelete($admin, $product))->toBeFalse();
    });
});

describe('[ProductPolicy]::[approval]', function () {
    it('[ProductPolicy]-[008] ADMINのみtrueを返す', function (UserRole $role, bool $expected) {
        $policy = new ProductPolicy;

        $user = User::factory()->create(['role' => $role]);
        $product = Product::factory()->create(['status' => ProductStatus::PENDING]);

        expect($policy->approval($user, $product))->toBe($expected);
    })->with([
        'ADMIN' => [UserRole::ADMIN, true],
        'STAFF' => [UserRole::STAFF, false],
        'SHOP' => [UserRole::SHOP, false],
    ]);
});
