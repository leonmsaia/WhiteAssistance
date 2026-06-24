@extends('layouts.app')

@section('title', 'Panel administración')

@section('content')
    <h1>Administración</h1>
    <p class="muted">Vista general de la plataforma.</p>

    <div class="grid" style="margin: 1.5rem 0;">
        <div class="card"><strong>Usuarios</strong><div style="font-size:2rem;">{{ $stats['users'] }}</div></div>
        <div class="card"><strong>Pacientes</strong><div style="font-size:2rem;">{{ $stats['patients'] }}</div></div>
        <div class="card"><strong>Especialistas</strong><div style="font-size:2rem;">{{ $stats['specialists'] }}</div></div>
        <div class="card"><strong>Citas hoy</strong><div style="font-size:2rem;">{{ $stats['appointments_today'] }}</div></div>
        <div class="card"><strong>Pendientes</strong><div style="font-size:2rem;">{{ $stats['appointments_pending'] }}</div></div>
    </div>

    <div class="card">
        <h2>Citas recientes</h2>
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Paciente</th>
                    <th>Especialista</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($recentAppointments as $appointment)
                    <tr>
                        <td>{{ $appointment->scheduled_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $appointment->patient->fullName() }}</td>
                        <td>{{ $appointment->specialist->fullName() }}</td>
                        <td><span class="badge">{{ $appointment->status->label() }}</span></td>
                        <td><a href="{{ route('appointments.show', $appointment) }}">Ver</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
