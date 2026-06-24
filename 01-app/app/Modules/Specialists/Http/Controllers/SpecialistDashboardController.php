<?php

namespace App\Modules\Specialists\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Appointments\Infrastructure\Models\Appointment;
use App\Shared\Enums\AppointmentStatus;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SpecialistDashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $specialist = $request->user()->specialist;

        $todayAppointments = Appointment::query()
            ->with(['patient', 'specialty'])
            ->when($specialist, fn ($query) => $query->where('specialist_id', $specialist->id))
            ->whereDate('scheduled_at', today())
            ->whereIn('status', [
                AppointmentStatus::Scheduled,
                AppointmentStatus::Confirmed,
            ])
            ->orderBy('scheduled_at')
            ->get();

        $pendingCount = Appointment::query()
            ->when($specialist, fn ($query) => $query->where('specialist_id', $specialist->id))
            ->where('status', AppointmentStatus::Scheduled)
            ->count();

        return view('specialist.dashboard', [
            'specialist' => $specialist,
            'todayAppointments' => $todayAppointments,
            'pendingCount' => $pendingCount,
        ]);
    }
}
