<?php

use App\Enums\UserRole;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\User;
use App\Policies\InventoryPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('[InventoryPolicy]::[viewAny / view]', function () {
    it('[InventoryPolicyTest]-[001] ADMIN/STAFFはtrue、SHOPはfalseを返す', function (UserRole $role, bool $expected) {
        $policy = new InventoryPolicy;
        $user = User::factory()->create(['role' => $role]);
        $product = Product::factory()->create();
        $inventory = Inventory::factory()->for($product)->create();

        expect($policy->viewAny($user))->toBe($expected)
            ->and($policy->view($user, $inventory))->toBe($expected);
    })->with([
        'ADMIN' => [UserRole::ADMIN, true],
        'STAFF' => [UserRole::STAFF, true],
        'SHOP' => [UserRole::SHOP, false],
    ]);
});

describe('InventoryPolicy::create', function () {
    it('[InventoryPolicyTest]-[002] STAFFのみtrueを返す', function (UserRole $role, bool $expected) {
        $policy = new InventoryPolicy;

        $user = User::factory()->create(['role' => $role]);

        expect($policy->create($user))->toBe($expected);
    })->with([
        'ADMIN' => [UserRole::ADMIN, false],
        'STAFF' => [UserRole::STAFF, true],
        'SHOP' => [UserRole::SHOP, false],
    ]);
});

describe('InventoryPolicy::update', function () {
    it('[InventoryPolicyTest]-[003] STAFF/SHOPはtrue、ADMINはfalseを返す', function (UserRole $role, bool $expected) {
        $policy = new InventoryPolicy;

        $user = User::factory()->create(['role' => $role]);
        $product = Product::factory()->create();
        $inventory = Inventory::factory()->for($product)->create();

        expect($policy->update($user, $inventory))->toBe($expected);
    })->with([
        'ADMIN' => [UserRole::ADMIN, false],
        'STAFF' => [UserRole::STAFF, true],
        'SHOP' => [UserRole::SHOP, true],
    ]);
});
