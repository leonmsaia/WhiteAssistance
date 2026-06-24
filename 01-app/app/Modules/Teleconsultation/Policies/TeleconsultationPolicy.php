<?php

namespace App\Modules\Teleconsultation\Policies;

use App\Modules\Appointments\Infrastructure\Models\Appointment;
use App\Modules\AccessControl\Domain\RoleName;
use App\Modules\Identity\Infrastructure\Models\User;
use App\Modules\Teleconsultation\Infrastructure\Models\Teleconsultation;
use App\Shared\Enums\AppointmentStatus;
use Illuminate\Support\Facades\Gate;

class TeleconsultationPolicy
{
    public function view(User $user, Teleconsultation $teleconsultation): bool
    {
        $appointment = $teleconsultation->appointment;

        if ($appointment->status !== AppointmentStatus::Confirmed) {
            return false;
        }

        return Gate::forUser($user)->allows('view', $appointment);
    }

    public function start(User $user, Teleconsultation $teleconsultation): bool
    {
        $appointment = $teleconsultation->appointment;

        if ($appointment->status !== AppointmentStatus::Confirmed) {
            return false;
        }

        return $this->canManage($user, $appointment);
    }

    public function finish(User $user, Teleconsultation $teleconsultation): bool
    {
        if ($teleconsultation->status !== 'started') {
            return false;
        }

        return $this->canManage($user, $teleconsultation->appointment);
    }

    private function canManage(User $user, Appointment $appointment): bool
    {
        if (! $user->hasRole(RoleName::SPECIALIST)) {
            return false;
        }

        return $appointment->specialist_id === $user->specialist?->id;
    }
}
