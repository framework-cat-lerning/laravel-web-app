<?php

use App\Enums\UserRole;
use App\Http\Resources\Admin\UserResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('[UserResourceTest]-[001] ユーザー情報を正しい構造に変換する', function () {
    $user = User::factory()->create([
        'name' => 'テストユーザー',
        'email' => 'test@example.com',
        'role' => UserRole::STAFF,
    ]);

    $array = (new UserResource($user))->toArray(request());

    expect($array)->toBe([
        'id' => $user->id,
        'name' => 'テストユーザー',
        'email' => 'test@example.com',
        'created_at' => $user->created_at->isoFormat('YYYY/MM/DD'),
        'updated_at' => $user->updated_at->isoFormat('YYYY/MM/DD'),
        'role' => [
            'id' => UserRole::STAFF,
            'label' => '在庫搬入者',
        ],
    ]);
});

it('[UserResourceTest]-[002] passwordやremember_tokenは含まれない', function () {
    $user = User::factory()->create();

    $array = (new UserResource($user))->toArray(request());

    expect($array)->not->toHaveKey('password')
        ->and($array)->not->toHaveKey('remember_token');
});
