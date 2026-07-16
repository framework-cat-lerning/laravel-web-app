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

    /**
     * 商品申請可能か
     */
    public function isApproval(): bool
    {
        return $this === self::STAFF;
    }

    /**
     * 編集・削除可能か
     */
    public function isEditing(): bool
    {
        return $this === self::ADMIN;
    }
}
