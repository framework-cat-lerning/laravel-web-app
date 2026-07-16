<?php

namespace App\Enums;

enum UserRole: int
{
    case SYSTEM_ADMIN = 1;
    case IMPORTER = 2;
    case SHOP_USER = 3;

    /**
     * ラベル
     */
    public function label(): string
    {
        return match ($this) {
            self::SYSTEM_ADMIN => 'システム管理者',
            self::IMPORTER => '搬入者',
            self::SHOP_USER => '店舗ユーザ',
        };
    }

    /**
     * ページのディレクトリ
     */
    public function dir(): string
    {
        return match ($this) {
            self::SYSTEM_ADMIN => 'systemAdmin',
            self::IMPORTER => 'impoter',
            self::SHOP_USER => 'shop',
        };
    }
}
