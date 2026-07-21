<?php

use App\Enums\UserRole;
use App\Models\ConsumptionLog;
use App\Models\Product;
use App\Models\User;
use App\Policies\ConsumptionLogPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('[ConsumptionLogPolicy]::[viewAny / view]', function () {
    it('[ConsumptionLogPolicyTest]-[001] 全roleで常にtrueを返す', function (UserRole $role) {
        $policy = new ConsumptionLogPolicy;

        $user = User::factory()->create(['role' => $role]);
        $product = Product::factory()->create();
        $log = ConsumptionLog::factory()->for($product)->for($user)->create();

        expect($policy->viewAny($user))->toBeTrue()
            ->and($policy->view($user, $log))->toBeTrue();
    })->with([
        'ADMIN' => UserRole::ADMIN,
        'STAFF' => UserRole::STAFF,
        'SHOP' => UserRole::SHOP,
    ]);
});

describe('[ConsumptionLogPolicy]::[create / update]', function () {
    it('[ConsumptionLogPolicyTest]-[001] STAFF/SHOPはtrue、ADMINはfalseを返す', function (UserRole $role, bool $expected) {
        $policy = new ConsumptionLogPolicy;

        $user = User::factory()->create(['role' => $role]);
        $product = Product::factory()->create();
        $log = ConsumptionLog::factory()->for($product)->for($user)->create();

        expect($policy->create($user))->toBe($expected)
            ->and($policy->update($user, $log))->toBe($expected);
    })->with([
        'ADMIN' => [UserRole::ADMIN, false],
        'STAFF' => [UserRole::STAFF, true],
        'SHOP' => [UserRole::SHOP, true],
    ]);
});
