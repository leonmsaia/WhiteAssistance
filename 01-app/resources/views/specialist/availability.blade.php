@extends('layouts.app')

@section('title', 'Disponibilidad semanal')

@section('content')
    <h1>Disponibilidad semanal</h1>
    <p class="muted">Defina los horarios en los que puede atender consultas.</p>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Día</th>
                    <th>Inicio</th>
                    <th>Fin</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($availabilities as $availability)
                    <tr>
                        <td>{{ $availability->weekdayName() }}</td>
                        <td>{{ \Illuminate\Support\Str::substr($availability->start_time, 0, 5) }}</td>
                        <td>{{ \Illuminate\Support\Str::substr($availability->end_time, 0, 5) }}</td>
                        <td>
                            <form method="POST" action="{{ route('specialist.availability.destroy', $availability) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="muted">No hay disponibilidad configurada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card" style="max-width: 480px;">
        <h2>Agregar disponibilidad</h2>
        <form method="POST" action="{{ route('specialist.availability.store') }}">
            @csrf

            <label for="weekday">Día</label>
            <select id="weekday" name="weekday" required>
                <option value="">Seleccione...</option>
                @foreach ($weekdayNames as $value => $label)
                    <option value="{{ $value }}" @selected(old('weekday') == $value)>{{ $label }}</option>
                @endforeach
            </select>

            <label for="start_time">Hora inicio</label>
            <input id="start_time" type="time" name="start_time" value="{{ old('start_time') }}" required>

            <label for="end_time">Hora fin</label>
            <input id="end_time" type="time" name="end_time" value="{{ old('end_time') }}" required>

            <label for="consultation_duration_minutes">Duración de consulta</label>
            <select id="consultation_duration_minutes" name="consultation_duration_minutes" required>
                @foreach ([15, 30, 45, 60] as $minutes)
                    <option
                        value="{{ $minutes }}"
                        @selected(old('consultation_duration_minutes', $specialist?->consultation_duration_minutes ?? 30) == $minutes)
                    >
                        {{ $minutes }} minutos
                    </option>
                @endforeach
            </select>

            <button type="submit" class="btn">Agregar disponibilidad</button>
        </form>
    </div>
@endsection
