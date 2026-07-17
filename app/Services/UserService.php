<?php

namespace App\Services;

use App\Enums\ProductStatus;
use App\Enums\UserRole;
use App\Http\Requests\Admin\UserStoreRequest;
use App\Http\Requests\Admin\UserUpdateRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class UserService
{
    /**
     * 新規保存処理
     */
    public function store(UserStoreRequest $request): User
    {
        try {
            return DB::transaction(function () use ($request): User {
                $user = new User;
                $user->fill($request->validated());
                $user->save();

                return $user;
            });
        } catch (Throwable $e) {
            report($e);

            throw $e;
        }
    }

    /**
     * 更新保存処理
     */
    public function update(UserUpdateRequest $request, User $user): User
    {
        try {
            return DB::transaction(function () use ($request, $user): User {
                $user->name = $request->string('name');
                $user->email = $request->string('email');
                $user->role = UserRole::from($request->integer('role'));

                /** @var string|null */
                $password = $request->input('password');
                if (! empty($password)) {
                    $user->password = $password;
                }
                $user->save();

                return $user;
            });
        } catch (Throwable $e) {
            report($e);

            throw $e;
        }
    }

    /**
     * 削除処理
     */
    public function delete(User $user): bool
    {
        try {
            return DB::transaction(function () use ($user): bool {
                // ユーザに紐づく申請中のアイテムを削除
                $products = $user->requestProducts()->where([
                    'status' => ProductStatus::PENDING,
                ]);
                Log::debug('申請無効化アイテム：'.print_r($products->get(), true));
                $products->delete();

                // ユーザの削除
                $user->delete();

                return true;
            });
        } catch (Throwable $e) {
            report($e);

            throw $e;
        }
    }
}
