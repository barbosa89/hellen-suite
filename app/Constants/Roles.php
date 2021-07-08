<?php

namespace App\Constants;

use Illuminate\Contracts\Support\Arrayable;

class Roles implements Arrayable
{
    public const ROOT = 'root';
    public const MANAGER = 'manager';
    public const ADMIN = 'admin';
    public const RECEPTIONIST = 'receptionist';
    public const ACCOUNTANT = 'accountant';
    public const CASHIER = 'cashier';

    public function toArray(): array
    {
        return [
            self::ROOT,
            self::MANAGER,
            self::ADMIN,
            self::RECEPTIONIST,
            self::ACCOUNTANT,
            self::CASHIER,
        ];
    }
}
