<?php

namespace App\Modules\Appointments\Policies;

use App\Modules\Appointments\Infrastructure\Models\Appointment;
use App\Modules\Identity\Infrastructure\Models\User;
use App\Modules\AccessControl\Domain\RoleName;

class AppointmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(RoleName::all());
    }

    public function view(User $user, Appointment $appointment): bool
    {
        if ($user->hasRole(RoleName::ADMIN)) {
            return true;
        }

        if ($user->hasRole(RoleName::PATIENT)) {
            return $user->patient?->id === $appointment->patient_id;
        }

        if ($user->hasRole(RoleName::SPECIALIST)) {
            return $user->specialist?->id === $appointment->specialist_id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(RoleName::PATIENT) && $user->patient !== null;
    }

    public function confirm(User $user, Appointment $appointment): bool
    {
        if (! $appointment->canBeConfirmed()) {
            return false;
        }

        if ($user->hasRole(RoleName::ADMIN)) {
            return true;
        }

        return $user->hasRole(RoleName::SPECIALIST)
            && $user->specialist?->id === $appointment->specialist_id;
    }

    public function cancel(User $user, Appointment $appointment): bool
    {
        if (! $appointment->canBeCancelled()) {
            return false;
        }

        if ($user->hasRole(RoleName::ADMIN)) {
            return true;
        }

        if ($user->hasRole(RoleName::PATIENT)) {
            return $user->patient?->id === $appointment->patient_id;
        }

        if ($user->hasRole(RoleName::SPECIALIST)) {
            return $user->specialist?->id === $appointment->specialist_id;
        }

        return false;
    }

    public function complete(User $user, Appointment $appointment): bool
    {
        if (! $appointment->canBeCompleted()) {
            return false;
        }

        if ($user->hasRole(RoleName::ADMIN)) {
            return true;
        }

        return $user->hasRole(RoleName::SPECIALIST)
            && $user->specialist?->id === $appointment->specialist_id;
    }

    public function update(User $user, Appointment $appointment): bool
    {
        if ($user->hasRole(RoleName::ADMIN)) {
            return true;
        }

        if ($user->hasRole(RoleName::PATIENT)) {
            return $user->patient?->id === $appointment->patient_id;
        }

        if ($user->hasRole(RoleName::SPECIALIST)) {
            return $user->specialist?->id === $appointment->specialist_id;
        }

        return false;
    }

    public function delete(User $user, Appointment $appointment): bool
    {
        if ($user->hasRole(RoleName::ADMIN)) {
            return true;
        }

        if ($user->hasRole(RoleName::PATIENT)) {
            return $user->patient?->id === $appointment->patient_id;
        }

        if ($user->hasRole(RoleName::SPECIALIST)) {
            return $user->specialist?->id === $appointment->specialist_id;
        }

        return false;
    }
}
