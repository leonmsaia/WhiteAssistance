@extends('layouts.app')

@section('title', 'Detalle de cita')

@section('content')
    <h1>Cita #{{ $appointment->id }}</h1>
    <p class="muted">Detalle de la cita médica.</p>

    <div class="card">
        <p><strong>Estado:</strong> <span class="badge">{{ $appointment->status->label() }}</span></p>
        <p><strong>Fecha:</strong> {{ $appointment->scheduled_at->format('d/m/Y H:i') }} - {{ $appointment->ends_at->format('H:i') }}</p>
        <p><strong>Paciente:</strong> {{ $appointment->patient->fullName() }}</p>
        <p><strong>Especialista:</strong> {{ $appointment->specialist->fullName() }}</p>
        <p><strong>Especialidad:</strong> {{ $appointment->specialty->name }}</p>
        <p><strong>Motivo:</strong> {{ $appointment->reason }}</p>
        @if ($appointment->cancellation_reason)
            <p><strong>Motivo de cancelación:</strong> {{ $appointment->cancellation_reason }}</p>
        @endif
    </div>

    @if ($appointment->status === \App\Shared\Enums\AppointmentStatus::Confirmed)
        <div class="card actions">
            @if (auth()->user()->hasRole('patient'))
                <a href="{{ route('teleconsultations.show', $appointment) }}" class="btn">Entrar a consulta</a>
            @elseif (auth()->user()->hasRole('specialist'))
                <a href="{{ route('teleconsultations.show', $appointment) }}" class="btn">Administrar consulta</a>
            @elseif (auth()->user()->hasRole('admin'))
                <a href="{{ route('teleconsultations.show', $appointment) }}" class="btn btn-secondary">Ver consulta</a>
            @endif
        </div>
    @endif

    <div class="card actions">
        @can('confirm', $appointment)
            <form method="POST" action="{{ route('appointments.confirm', $appointment) }}">
                @csrf
                <button type="submit" class="btn">Confirmar cita</button>
            </form>
        @endcan

        @can('complete', $appointment)
            <form method="POST" action="{{ route('appointments.complete', $appointment) }}">
                @csrf
                <button type="submit" class="btn btn-secondary">Marcar completada</button>
            </form>
        @endcan

        @can('cancel', $appointment)
            <form method="POST" action="{{ route('appointments.cancel', $appointment) }}">
                @csrf
                <input type="hidden" name="cancellation_reason" value="Cancelada desde el detalle de cita.">
                <button type="submit" class="btn btn-danger">Cancelar cita</button>
            </form>
        @endcan

        <a href="{{ route('appointments.index') }}" class="btn btn-secondary">Volver al listado</a>
    </div>
@endsection
