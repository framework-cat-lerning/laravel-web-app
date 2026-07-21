<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

describe('[Admin\UserController]::[index]', function () {
    it('[UserControllerTest]-[001] ログイン中のユーザーはユーザー一覧を表示できる', function () {
        $user = User::factory()->create();
        User::factory()->count(2)->create();

        // 新規ユーザが3人、migrationで3人なので合計6人
        actingAs($user)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('users/index')
                ->has('users.data', 6)
            );
    });

    it('[UserControllerTest]-[002] 未ログインの場合はloginページへリダイレクトされる', function () {
        get(route('admin.users.index'))
            ->assertRedirect(route('login'));
    });
});

describe('[Admin\UserController]::[show]', function () {
    it('[UserControllerTest]-[003] ログイン中のユーザーは他のユーザー詳細を表示できる', function () {
        $user = User::factory()->create();
        $target = User::factory()->create();

        actingAs($user)
            ->get(route('admin.users.show', $target))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('users/show')
                ->where('user.data.id', $target->id)
                ->where('user.data.email', $target->email)
            );
    });

    it('[UserControllerTest]-[004] 未ログインの場合はloginページへリダイレクトされる', function () {
        $target = User::factory()->create();

        get(route('admin.users.show', $target))
            ->assertRedirect(route('login'));
    });
});

describe('[Admin\UserController]::[new]', function () {
    it('[UserControllerTest]-[005] ADMINユーザーは登録画面を表示できる', function () {
        $user = User::factory()->create(['role' => UserRole::ADMIN]);

        actingAs($user)
            ->get(route('admin.users.new'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('users/form')
                ->where('form_type', 'new')
                ->has('options.roles', 3)
            );
    });

    it('[UserControllerTest]-[006] ADMIN以外のユーザーは登録画面にアクセスできず403となる', function (UserRole $role) {
        $user = User::factory()->create(['role' => $role]);

        actingAs($user)
            ->get(route('admin.users.new'))
            ->assertForbidden();
    })->with([
        'STAFF' => UserRole::STAFF,
        'SHOP' => UserRole::SHOP,
    ]);
});

describe('[Admin\UserController]::[store]', function () {
    it('[UserControllerTest]-[007] ADMINユーザーは新規ユーザーを作成でき、一覧へリダイレクトされる', function () {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => '新規ユーザー',
                'email' => 'new-user@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => UserRole::STAFF->value,
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'name' => '新規ユーザー',
            'email' => 'new-user@example.com',
            'role' => UserRole::STAFF->value,
        ]);
    });

    it('[UserControllerTest]-[008] ADMIN以外のユーザーは作成できず403となる', function (UserRole $role) {
        $user = User::factory()->create(['role' => $role]);

        actingAs($user)
            ->post(route('admin.users.store'), [
                'name' => '新規ユーザー',
                'email' => 'new-user@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => UserRole::STAFF->value,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('users', ['email' => 'new-user@example.com']);
    })->with([
        'STAFF' => UserRole::STAFF,
        'SHOP' => UserRole::SHOP,
    ]);

    it('[UserControllerTest]-[009] メールアドレスが重複する場合はバリデーションエラーとなる', function () {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        User::factory()->create(['email' => 'dup@example.com']);

        actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => '新規ユーザー',
                'email' => 'dup@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => UserRole::STAFF->value,
            ])
            ->assertSessionHasErrors('email');
    });

    it('[UserControllerTest]-[010] パスワード確認が一致しない場合はバリデーションエラーとなる', function () {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => '新規ユーザー',
                'email' => 'new-user@example.com',
                'password' => 'password123',
                'password_confirmation' => 'different',
                'role' => UserRole::STAFF->value,
            ])
            ->assertSessionHasErrors('password');
    });
});

describe('[Admin\UserController]::[edit]', function () {
    it('[UserControllerTest]-[011] ADMINユーザーは他ユーザーの編集画面を表示できる', function () {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $target = User::factory()->create();

        actingAs($admin)
            ->get(route('admin.users.edit', $target))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('users/form')
                ->where('form_type', 'edit')
            );
    });

    it('[UserControllerTest]-[012] 本人は自分の編集画面を表示できる', function () {
        $user = User::factory()->create(['role' => UserRole::STAFF]);

        actingAs($user)
            ->get(route('admin.users.edit', $user))
            ->assertOk();
    });

    it('[UserControllerTest]-[013] ADMINでも本人でもない場合は編集画面にアクセスできず403となる', function () {
        $user = User::factory()->create(['role' => UserRole::STAFF]);
        $other = User::factory()->create(['role' => UserRole::SHOP]);

        actingAs($user)
            ->get(route('admin.users.edit', $other))
            ->assertForbidden();
    });
});

describe('[Admin\UserController]::[update]', function () {
    it('[UserControllerTest]-[014] ADMINユーザーは他ユーザーを更新でき、一覧へリダイレクトされる', function () {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $target = User::factory()->create(['name' => '旧名前']);

        actingAs($admin)
            ->put(route('admin.users.update', $target), [
                'name' => '新名前',
                'email' => $target->email,
                'role' => UserRole::STAFF->value,
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', ['id' => $target->id, 'name' => '新名前']);
    });

    it('[UserControllerTest]-[015] 本人は自分自身を更新できる', function () {
        $user = User::factory()->create(['role' => UserRole::STAFF, 'name' => '旧名前']);

        actingAs($user)
            ->put(route('admin.users.update', $user), [
                'name' => '新名前',
                'email' => $user->email,
                'role' => UserRole::STAFF->value,
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => '新名前']);
    });

    it('[UserControllerTest]-[016] ADMINでも本人でもない場合は更新できず403となる', function () {
        $user = User::factory()->create(['role' => UserRole::STAFF]);
        $other = User::factory()->create(['role' => UserRole::SHOP, 'name' => '旧名前']);

        actingAs($user)
            ->put(route('admin.users.update', $other), [
                'name' => '新名前',
                'email' => $other->email,
                'role' => UserRole::SHOP->value,
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $other->id, 'name' => '旧名前']);
    });

    it('[UserControllerTest]-[017] バリデーションエラーの場合は更新されない', function () {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $target = User::factory()->create(['name' => '旧名前']);

        actingAs($admin)
            ->put(route('admin.users.update', $target), [
                'name' => '',
                'email' => $target->email,
                'role' => UserRole::STAFF->value,
            ])
            ->assertSessionHasErrors('name');

        $this->assertDatabaseHas('users', ['id' => $target->id, 'name' => '旧名前']);
    });
});

describe('[Admin\UserController]::[delete]', function () {
    it('[UserControllerTest]-[018] ADMINユーザーは他ユーザーを削除でき、一覧へリダイレクトされる', function () {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $target = User::factory()->create();

        actingAs($admin)
            ->delete(route('admin.users.delete', $target))
            ->assertRedirect(route('admin.users.index'));

        $this->assertSoftDeleted('users', ['id' => $target->id]);
    });

    it('[UserControllerTest]-[019] 本人は自分自身を削除できる', function () {
        $user = User::factory()->create(['role' => UserRole::STAFF]);

        actingAs($user)
            ->delete(route('admin.users.delete', $user))
            ->assertRedirect(route('admin.users.index'));

        $this->assertSoftDeleted('users', ['id' => $user->id]);
    });

    it('[UserControllerTest]-[020] ADMINでも本人でもない場合は削除できず403となる', function () {
        $user = User::factory()->create(['role' => UserRole::STAFF]);
        $other = User::factory()->create(['role' => UserRole::SHOP]);

        actingAs($user)
            ->delete(route('admin.users.delete', $other))
            ->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $other->id, 'deleted_at' => null]);
    });
});
