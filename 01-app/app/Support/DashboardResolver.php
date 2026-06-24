<?php

namespace App\Support;

use App\Modules\AccessControl\Domain\RoleName;
use App\Modules\Identity\Infrastructure\Models\User;

final class DashboardResolver
{
    public static function routeName(User $user): string
    {
        if ($user->hasRole(RoleName::ADMIN)) {
            return 'admin.dashboard';
        }

        if ($user->hasRole(RoleName::SPECIALIST)) {
            return 'specialist.dashboard';
        }

        return 'patient.dashboard';
    }
}
