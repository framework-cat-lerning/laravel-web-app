<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserStoreRequest;
use App\Http\Requests\Admin\UserUpdateRequest;
use App\Http\Resources\Admin\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected UserService $userService
    ) {}

    /**
     * ユーザ一覧
     */
    public function index(): Response
    {
        $this->authorize('viewAny', User::class);

        $users = User::all();

        return Inertia::render('users/index', [
            'users' => UserResource::collection($users),
        ]);
    }

    /**
     * ユーザ詳細
     */
    public function show() {}

    /**
     * ユーザ登録画面
     */
    public function new(): Response
    {
        $this->authorize('create', User::class);

        return Inertia::render('users/form', [
            'user' => new User,
            'form_type' => 'new',
            'options' => [
                'roles' => UserRole::All(),
            ],
        ]);
    }

    /**
     * ユーザ登録
     */
    public function store(UserStoreRequest $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $this->userService->store($request);

        return redirect()->route('admin.users.index');
    }

    /**
     * ユーザ編集画面
     */
    public function edit(User $user): Response
    {
        $this->authorize('update', $user);

        return Inertia::render('users/form', [
            'user' => $user,
            'form_type' => 'edit',
            'options' => [
                'roles' => UserRole::All(),
            ],
        ]);
    }

    /**
     * ユーザ更新
     */
    public function update(UserUpdateRequest $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $this->userService->update($request, $user);

        return redirect()->route('admin.users.index');
    }

    /**
     * ユーザ削除
     */
    public function delete() {}
}
