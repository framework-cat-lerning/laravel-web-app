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

    /**
     * 管理者ユーザか
     */
    public function isAdmin(): bool
    {
        return $this === self::ADMIN;
    }

    /**
     * 全ケースを取得
     * @return array{
     *          array{
     *              id: int, label: string
     *          }
     *         }
     */
    public static function All(): array
    {
        return array_map(
            fn (self $case) => [
                'id' => $case->value,
                'label' => $case->label(),
            ],
            self::cases()
        );
    }
}
