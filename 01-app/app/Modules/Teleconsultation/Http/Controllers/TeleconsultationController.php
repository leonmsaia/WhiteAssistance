<?php

namespace App\Modules\Teleconsultation\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\TeleconsultationEndedMail;
use App\Mail\TeleconsultationStartedMail;
use App\Modules\Appointments\Infrastructure\Models\Appointment;
use App\Modules\Teleconsultation\Infrastructure\Models\Teleconsultation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class TeleconsultationController extends Controller
{
    public function show(Appointment $appointment): View
    {
        $appointment->load(['patient.user', 'specialist.user', 'specialty']);

        $teleconsultation = $this->resolveTeleconsultation($appointment);
        $teleconsultation->setRelation('appointment', $appointment);

        Gate::authorize('view', $teleconsultation);

        return view('teleconsultations.show', compact('appointment', 'teleconsultation'));
    }

    public function start(Appointment $appointment): RedirectResponse
    {
        $teleconsultation = $this->resolveTeleconsultation($appointment);
        $teleconsultation->setRelation('appointment', $appointment);

        Gate::authorize('start', $teleconsultation);

        $teleconsultation->update([
            'status' => 'started',
            'started_at' => now(),
        ]);

        $appointment->load(['patient.user', 'specialist', 'specialty']);

        Mail::to($appointment->patient->user->email)
            ->send(new TeleconsultationStartedMail($appointment, $teleconsultation));

        return redirect()->route('teleconsultations.show', $appointment);
    }

    public function finish(Appointment $appointment): RedirectResponse
    {
        $teleconsultation = Teleconsultation::query()
            ->where('appointment_id', $appointment->id)
            ->firstOrFail();

        $teleconsultation->setRelation('appointment', $appointment);

        Gate::authorize('finish', $teleconsultation);

        request()->validate([
            'consultation_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $teleconsultation->update([
            'consultation_notes' => request('consultation_notes'),
            'status' => 'ended',
            'ended_at' => now(),
        ]);

        $appointment->load(['patient.user', 'specialist', 'specialty']);

        Mail::to($appointment->patient->user->email)
            ->send(new TeleconsultationEndedMail($appointment, $teleconsultation));

        return redirect()->route('teleconsultations.show', $appointment);
    }

    private function resolveTeleconsultation(Appointment $appointment): Teleconsultation
    {
        $room = "wa-appointment-{$appointment->id}";
        $url = 'https://meet.jit.si/'.$room;

        return Teleconsultation::query()->firstOrCreate(
            ['appointment_id' => $appointment->id],
            [
                'room_name' => $room,
                'room_url' => $url,
                'status' => 'waiting',
            ]
        );
    }
}
