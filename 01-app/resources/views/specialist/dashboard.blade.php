@extends('layouts.app')

@section('title', 'Panel especialista')

@section('content')
    <h1>Hola, {{ $specialist?->fullName() ?? auth()->user()->name }}</h1>
    <p class="muted">Panel operativo del especialista.</p>

    <div class="grid" style="margin: 1.5rem 0;">
        <div class="card">
            <strong>Citas hoy</strong>
            <div style="font-size: 2rem;">{{ $todayAppointments->count() }}</div>
        </div>
        <div class="card">
            <strong>Pendientes de confirmar</strong>
            <div style="font-size: 2rem;">{{ $pendingCount }}</div>
        </div>
    </div>

    <div class="card">
        <h2>Agenda de hoy</h2>
        @forelse ($todayAppointments as $appointment)
            <div style="padding: .75rem 0; border-bottom: 1px solid var(--border);">
                <strong>{{ $appointment->scheduled_at->format('H:i') }}</strong>
                <span class="badge">{{ $appointment->status->label() }}</span>
                <div>{{ $appointment->patient->fullName() }} · {{ $appointment->specialty->name }}</div>
                <a href="{{ route('appointments.show', $appointment) }}">Ver detalle</a>
            </div>
        @empty
            <p class="muted">No hay citas para hoy.</p>
        @endforelse
    </div>
@endsection
