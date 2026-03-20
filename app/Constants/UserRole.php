<?php
declare(strict_types=1);
namespace App\Constants;

class UserRole
{
    public const ADMIN = 'admin';
    public const TI = 'ti';
    public const USUARIO = 'usuario';

    public static function getAll(): array
    {
        return [self::ADMIN, self::TI, self::USUARIO];
    }

    public static function isValid(string $role): bool
    {
        return in_array($role, self::getAll(), true);
    }
}
