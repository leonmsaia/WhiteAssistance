<?php

namespace App\Modules\Appointments\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\AppointmentConfirmedMail;
use App\Modules\Appointments\Infrastructure\Models\Appointment;
use App\Modules\AccessControl\Domain\RoleName;
use App\Modules\Specialists\Infrastructure\Models\Specialist;
use App\Modules\Specialties\Infrastructure\Models\Specialty;
use App\Shared\Enums\AppointmentStatus;
use App\Shared\Enums\ProfileStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Appointment::class);

        $user = $request->user();

        $appointments = Appointment::query()
            ->with(['patient', 'specialist', 'specialty'])
            ->when($user->hasRole(RoleName::PATIENT), function ($query) use ($user) {
                $query->where('patient_id', $user->patient?->id);
            })
            ->when($user->hasRole(RoleName::SPECIALIST), function ($query) use ($user) {
                $query->where('specialist_id', $user->specialist?->id);
            })
            ->latest('scheduled_at')
            ->paginate(10);

        return view('appointments.index', compact('appointments'));
    }

    public function create(Request $request): View
    {
        Gate::authorize('create', Appointment::class);

        $specialties = Specialty::query()->where('is_active', true)->orderBy('name')->get();

        $specialists = Specialist::query()
            ->with(['specialties', 'availabilities'])
            ->where('status', ProfileStatus::Active)
            ->orderBy('last_name')
            ->get();

        return view('appointments.create', compact('specialties', 'specialists'));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Appointment::class);

        $validated = $request->validate([
            'specialty_id' => ['required', 'exists:specialties,id'],
            'specialist_id' => ['required', 'exists:specialists,id'],
            'scheduled_at' => ['required', 'date', 'after:now'],
            'reason' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        $specialist = Specialist::query()
            ->with('availabilities')
            ->where('id', $validated['specialist_id'])
            ->where('status', ProfileStatus::Active)
            ->whereHas('specialties', fn ($query) => $query->where('specialties.id', $validated['specialty_id']))
            ->firstOrFail();

        $appointmentStart = \Carbon\Carbon::parse($validated['scheduled_at']);
        $appointmentEnd = $appointmentStart->copy()->addMinutes($specialist->consultation_duration_minutes);
        $weekday = $appointmentStart->dayOfWeek;
        $date = $appointmentStart->format('Y-m-d');

        $hasMatchingAvailability = $specialist->availabilities
            ->where('weekday', $weekday)
            ->contains(function ($availability) use ($appointmentStart, $appointmentEnd, $date) {
                $slotStart = \Carbon\Carbon::parse("{$date} {$availability->start_time}");
                $slotEnd = \Carbon\Carbon::parse("{$date} {$availability->end_time}");

                return $appointmentStart->gte($slotStart) && $appointmentEnd->lte($slotEnd);
            });

        if (! $hasMatchingAvailability) {
            return back()
                ->withInput()
                ->withErrors(['scheduled_at' => 'El horario seleccionado está fuera de la disponibilidad del especialista.']);
        }

        $hasOverlap = Appointment::query()
            ->where('specialist_id', $specialist->id)
            ->whereIn('status', [AppointmentStatus::Scheduled, AppointmentStatus::Confirmed])
            ->where('scheduled_at', '<', $appointmentEnd)
            ->where('ends_at', '>', $appointmentStart)
            ->exists();

        if ($hasOverlap) {
            return back()
                ->withInput()
                ->withErrors(['scheduled_at' => 'El especialista ya tiene una cita en ese horario.']);
        }

        $appointment = Appointment::query()->create([
            'patient_id' => $request->user()->patient->id,
            'specialist_id' => $specialist->id,
            'specialty_id' => $validated['specialty_id'],
            'scheduled_at' => $appointmentStart,
            'ends_at' => $appointmentEnd,
            'status' => AppointmentStatus::Scheduled,
            'reason' => $validated['reason'],
        ]);

        return redirect()
            ->route('appointments.show', $appointment)
            ->with('status', 'Cita solicitada correctamente.');
    }

    public function show(Appointment $appointment): View
    {
        Gate::authorize('view', $appointment);

        $appointment->load(['patient', 'specialist', 'specialty']);

        return view('appointments.show', compact('appointment'));
    }

    public function confirm(Appointment $appointment): RedirectResponse
    {
        Gate::authorize('confirm', $appointment);

        $appointment->update(['status' => AppointmentStatus::Confirmed]);

        $appointment->load(['patient.user', 'specialist', 'specialty']);

        Mail::to($appointment->patient->user->email)
            ->send(new AppointmentConfirmedMail($appointment));

        return back()->with('status', 'Cita confirmada.');
    }

    public function cancel(Request $request, Appointment $appointment): RedirectResponse
    {
        Gate::authorize('cancel', $appointment);

        $validated = $request->validate([
            'cancellation_reason' => ['nullable', 'string', 'max:300'],
        ]);

        $appointment->update([
            'status' => AppointmentStatus::Cancelled,
            'cancellation_reason' => $validated['cancellation_reason'] ?? 'Cancelada por el usuario.',
        ]);

        return back()->with('status', 'Cita cancelada.');
    }

    public function complete(Appointment $appointment): RedirectResponse
    {
        Gate::authorize('complete', $appointment);

        $appointment->update(['status' => AppointmentStatus::Completed]);

        return back()->with('status', 'Cita marcada como completada.');
    }
}
