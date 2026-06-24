<?php

namespace App\Modules\AccessControl\Domain;

final class RoleName
{
    public const PATIENT = 'patient';

    public const SPECIALIST = 'specialist';

    public const ADMIN = 'admin';

    public static function all(): array
    {
        return [
            self::PATIENT,
            self::SPECIALIST,
            self::ADMIN,
        ];
    }
}
