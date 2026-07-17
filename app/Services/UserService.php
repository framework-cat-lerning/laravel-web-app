<?php

namespace App\Services;

use App\Http\Requests\Admin\UserStoreRequest;
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
                /** @var User $user */
                $user = $request->user();

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
}
