@extends('layouts.app')

@section('title', 'Mis citas')

@section('content')
    <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap;">
        <h1>Citas</h1>
        @can('create', App\Modules\Appointments\Infrastructure\Models\Appointment::class)
            <a href="{{ route('appointments.create') }}" class="btn">Nueva cita</a>
        @endcan
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Paciente</th>
                    <th>Especialista</th>
                    <th>Especialidad</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($appointments as $appointment)
                    <tr>
                        <td>{{ $appointment->scheduled_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $appointment->patient->fullName() }}</td>
                        <td>{{ $appointment->specialist->fullName() }}</td>
                        <td>{{ $appointment->specialty->name }}</td>
                        <td><span class="badge">{{ $appointment->status->label() }}</span></td>
                        <td><a href="{{ route('appointments.show', $appointment) }}">Ver</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="muted">No hay citas registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{ $appointments->links() }}
    </div>
@endsection
