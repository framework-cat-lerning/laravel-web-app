<?php

namespace App\Enums;

enum ProductStatus: int
{
    case PENDING = 1;
    case APPROVED = 2;

    /**
     * ラベル
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING => '申請中（承認待ち）',
            self::APPROVED => '承認済み',
        };
    }

    /**
     * 申請中か
     */
    public function isPending(): bool
    {
        return $this === self::PENDING;
    }
}
