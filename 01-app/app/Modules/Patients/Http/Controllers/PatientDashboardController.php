<?php

namespace App\Modules\Patients\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Appointments\Infrastructure\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PatientDashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $patient = $request->user()->patient;

        $upcomingAppointments = Appointment::query()
            ->with(['specialist', 'specialty'])
            ->when($patient, fn ($query) => $query->where('patient_id', $patient->id))
            ->upcoming()
            ->orderBy('scheduled_at')
            ->limit(5)
            ->get();

        return view('patient.dashboard', [
            'patient' => $patient,
            'upcomingAppointments' => $upcomingAppointments,
        ]);
    }
}
