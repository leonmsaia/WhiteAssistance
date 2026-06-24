@extends('layouts.app')

@section('title', 'Panel paciente')

@section('content')
    <h1>Hola, {{ $patient?->fullName() ?? auth()->user()->name }}</h1>
    <p class="muted">Bienvenido a su panel de paciente.</p>

    <div class="grid" style="margin: 1.5rem 0;">
        <div class="card">
            <strong>Próximas citas</strong>
            <div style="font-size: 2rem;">{{ $upcomingAppointments->count() }}</div>
        </div>
        <div class="card">
            <a href="{{ route('appointments.create') }}" class="btn">Solicitar cita</a>
        </div>
    </div>

    <div class="card">
        <h2>Próximas citas</h2>
        @forelse ($upcomingAppointments as $appointment)
            <div style="padding: .75rem 0; border-bottom: 1px solid var(--border);">
                <strong>{{ $appointment->scheduled_at->format('d/m/Y H:i') }}</strong>
                <span class="badge">{{ $appointment->status->label() }}</span>
                <div class="muted">{{ $appointment->specialist->fullName() }} · {{ $appointment->specialty->name }}</div>
                <a href="{{ route('appointments.show', $appointment) }}">Ver detalle</a>
            </div>
        @empty
            <p class="muted">No tiene citas próximas.</p>
        @endforelse
    </div>
@endsection
