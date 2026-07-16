<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    const array USERS = [
        [
            'email' => 'admin@fw-cat.jp',
            'password' => 'password',
            'role' => UserRole::SYSTEM_ADMIN,
        ], [
            'email' => 'importer@fw-cat.jp',
            'password' => 'password',
            'role' => UserRole::IMPORTER,
        ], [
            'email' => 'shop@fw-cat.jp',
            'password' => 'password',
            'role' => UserRole::SHOP_USER,
        ],
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach (self::USERS as $user) {
            User::createOrRestore([
                'email' => $user['email'],
            ], [
                'name' => $user['role']->label(),
                'password' => $user['password'],
                'role' => $user['role'],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (self::USERS as $user) {
            User::where([
                'email' => $user['email'],
            ])->first()->delete();
        }
    }
};
