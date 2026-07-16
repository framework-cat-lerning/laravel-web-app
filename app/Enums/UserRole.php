<?php

namespace App\Enums;

enum UserRole: int
{
    case ADMIN = 1;
    case STAFF = 2;
    case SHOP = 3;

    /**
     * ラベル
     */
    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'システム管理者',
            self::STAFF => '在庫搬入者',
            self::SHOP => '店舗側ユーザー',
        };
    }
}
