<?php

namespace App\Modules\Administration\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Appointments\Infrastructure\Models\Appointment;
use App\Modules\Identity\Infrastructure\Models\User;
use App\Modules\Patients\Infrastructure\Models\Patient;
use App\Modules\Specialists\Infrastructure\Models\Specialist;
use App\Shared\Enums\AppointmentStatus;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'users' => User::query()->count(),
                'patients' => Patient::query()->count(),
                'specialists' => Specialist::query()->count(),
                'appointments_today' => Appointment::query()->whereDate('scheduled_at', today())->count(),
                'appointments_pending' => Appointment::query()->where('status', AppointmentStatus::Scheduled)->count(),
            ],
            'recentAppointments' => Appointment::query()
                ->with(['patient', 'specialist', 'specialty'])
                ->latest()
                ->limit(8)
                ->get(),
        ]);
    }
}
