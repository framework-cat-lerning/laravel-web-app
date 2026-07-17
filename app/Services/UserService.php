<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Http\Requests\Admin\UserStoreRequest;
use App\Http\Requests\Admin\UserUpdateRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
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
}
