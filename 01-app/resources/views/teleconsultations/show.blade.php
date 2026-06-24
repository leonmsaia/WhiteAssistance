@extends('layouts.app')

@section('title', 'Teleconsulta')

@section('content')
    <h1>Teleconsulta — Cita #{{ $appointment->id }}</h1>
    <p class="muted">Sesión de videollamada asociada a la cita confirmada.</p>

    <div class="card">
        <p><strong>Paciente:</strong> {{ $appointment->patient->fullName() }}</p>
        <p><strong>Especialista:</strong> {{ $appointment->specialist->fullName() }}</p>
        <p><strong>Especialidad:</strong> {{ $appointment->specialty->name }}</p>
        <p><strong>Fecha de la cita:</strong> {{ $appointment->scheduled_at->format('d/m/Y H:i') }} - {{ $appointment->ends_at->format('H:i') }}</p>
        <p><strong>Estado actual:</strong> <span class="badge">{{ $teleconsultation->status }}</span></p>
    </div>

    <div class="card">
        @if ($teleconsultation->status === 'started')
            <iframe
                src="{{ $teleconsultation->room_url }}"
                width="100%"
                height="700">
            </iframe>
        @else
            <a href="{{ $teleconsultation->room_url }}" target="_blank" class="btn">Abrir Jitsi</a>
        @endif
    </div>

    @if (auth()->user()->hasRole('specialist') && auth()->user()->specialist?->id === $appointment->specialist_id)
        <div class="card actions">
            @if ($teleconsultation->status === 'waiting')
                <form method="POST" action="{{ route('teleconsultations.start', $appointment) }}">
                    @csrf
                    <button type="submit" class="btn">Iniciar consulta</button>
                </form>
            @endif

            @if ($teleconsultation->status === 'started')
                <form method="POST" action="{{ route('teleconsultations.finish', $appointment) }}">
                    @csrf
                    <textarea
                        name="consultation_notes"
                        rows="10">{{ old('consultation_notes', $teleconsultation->consultation_notes) }}</textarea>
                    <button type="submit" class="btn btn-secondary">Finalizar consulta</button>
                </form>
            @endif
        </div>
    @endif

    @if ($teleconsultation->status === 'ended')
        <div class="card">
            <pre>{{ $teleconsultation->consultation_notes }}</pre>
        </div>
    @endif
@endsection
