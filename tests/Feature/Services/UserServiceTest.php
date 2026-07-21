<?php

use App\Enums\ProductStatus;
use App\Enums\UserRole;
use App\Http\Requests\Admin\UserStoreRequest;
use App\Http\Requests\Admin\UserUpdateRequest;
use App\Models\Product;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('[UserService]::[store]', function () {
    it('[UserServiceTest]-[001] 新規ユーザーを作成する', function () {
        $service = new UserService;

        $request = Mockery::mock(UserStoreRequest::class);
        $request->shouldReceive('validated')->andReturn([
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'role' => UserRole::STAFF->value,
        ]);

        $user = $service->store($request);

        expect($user)->toBeInstanceOf(User::class)
            ->and($user->name)->toBe('テストユーザー')
            ->and($user->email)->toBe('test@example.com')
            ->and($user->role)->toBe(UserRole::STAFF);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'role' => UserRole::STAFF->value,
        ]);
    });

    it('[UserServiceTest]-[002] メールアドレスが重複する場合は例外を投げる', function () {
        $service = new UserService;

        User::factory()->create(['email' => 'dup@example.com']);

        $request = Mockery::mock(UserStoreRequest::class);
        $request->shouldReceive('validated')->andReturn([
            'name' => 'テストユーザー2',
            'email' => 'dup@example.com',
            'password' => 'password123',
            'role' => UserRole::STAFF->value,
        ]);

        expect(fn () => $service->store($request))
            ->toThrow(QueryException::class);

        expect(User::where('email', 'dup@example.com')->count())->toBe(1);
    });
});

describe('[UserService]::[update]', function () {
    it('[UserServiceTest]-[003] パスワードを指定した場合は変更される', function () {
        $service = new UserService;

        $user = User::factory()->create([
            'name' => '旧名前',
            'email' => 'old@example.com',
            'role' => UserRole::STAFF,
            'password' => 'old_password',
        ]);
        $oldPasswordHash = $user->password;

        $request = Mockery::mock(UserUpdateRequest::class);
        $request->shouldReceive('string')->with('name')->andReturn('新名前');
        $request->shouldReceive('string')->with('email')->andReturn('new@example.com');
        $request->shouldReceive('integer')->with('role')->andReturn(UserRole::ADMIN->value);
        $request->shouldReceive('input')->with('password')->andReturn('new_password123');

        $result = $service->update($request, $user);

        expect($result->name)->toBe('新名前')
            ->and($result->email)->toBe('new@example.com')
            ->and($result->role)->toBe(UserRole::ADMIN)
            ->and($result->password)->not->toBe($oldPasswordHash);
    });

    it('[UserServiceTest]-[004] パスワードが空の場合は変更されない', function () {
        $service = new UserService;

        $user = User::factory()->create([
            'name' => '旧名前',
            'email' => 'old@example.com',
            'role' => UserRole::STAFF,
            'password' => 'old_password',
        ]);
        $oldPasswordHash = $user->password;

        $request = Mockery::mock(UserUpdateRequest::class);
        $request->shouldReceive('string')->with('name')->andReturn('新名前');
        $request->shouldReceive('string')->with('email')->andReturn('new@example.com');
        $request->shouldReceive('integer')->with('role')->andReturn(UserRole::ADMIN->value);
        $request->shouldReceive('input')->with('password')->andReturn(null);

        $result = $service->update($request, $user);

        expect($result->name)->toBe('新名前')
            ->and($result->password)->toBe($oldPasswordHash);
    });

    it('[UserServiceTest]-[005] 不正なroleの値の場合は例外を投げる', function () {
        $service = new UserService;

        $user = User::factory()->create(['role' => UserRole::STAFF]);

        $request = Mockery::mock(UserUpdateRequest::class);
        $request->shouldReceive('string')->with('name')->andReturn('新名前');
        $request->shouldReceive('string')->with('email')->andReturn('new@example.com');
        $request->shouldReceive('integer')->with('role')->andReturn(999);
        $request->shouldReceive('input')->with('password')->andReturn(null);

        expect(fn () => $service->update($request, $user))
            ->toThrow(ValueError::class);
    });
});

describe('[UserService]::[delete]', function () {
    it('[UserServiceTest]-[006] 申請中の商品を削除し、ユーザーも削除する', function () {
        $service = new UserService;

        $user = User::factory()->create();
        $pendingProduct = Product::factory()->create([
            'status' => ProductStatus::PENDING,
            'request_user_id' => $user->id,
        ]);
        $approvedProduct = Product::factory()->create([
            'status' => ProductStatus::APPROVED,
            'request_user_id' => $user->id,
        ]);

        $result = $service->delete($user);

        expect($result)->toBeTrue();

        $this->assertSoftDeleted('products', ['id' => $pendingProduct->id]);
        $this->assertDatabaseHas('products', [
            'id' => $approvedProduct->id,
            'deleted_at' => null,
        ]);
        $this->assertSoftDeleted('users', ['id' => $user->id]);
    });

    it('[UserServiceTest]-[007] 申請商品が存在しない場合もユーザーは削除される', function () {
        $service = new UserService;

        $user = User::factory()->create();

        $result = $service->delete($user);

        expect($result)->toBeTrue();
        $this->assertSoftDeleted('users', ['id' => $user->id]);
    });
});
