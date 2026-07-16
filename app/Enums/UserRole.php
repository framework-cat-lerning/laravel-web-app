<?php

namespace App\Enums;

enum UserRole: int
{
    case SYSTEM_ADMIN   = 1;
    case IMPORTER       = 2;
    case SHOP_USER      = 3;
}
