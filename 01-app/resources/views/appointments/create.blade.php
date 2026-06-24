@extends('layouts.app')

@section('title', 'Nueva cita')

@section('content')
    <h1>Solicitar cita</h1>
    <p class="muted">Complete los datos para programar una consulta.</p>

    <div class="card" style="max-width: 640px;">
        <form method="POST" action="{{ route('appointments.store') }}">
            @csrf

            <label for="specialty_id">Especialidad</label>
            <select id="specialty_id" name="specialty_id" required>
                <option value="">Seleccione...</option>
                @foreach ($specialties as $specialty)
                    <option value="{{ $specialty->id }}" @selected(old('specialty_id') == $specialty->id)>
                        {{ $specialty->name }}
                    </option>
                @endforeach
            </select>

            <label for="specialist_id">Especialista</label>
            <select id="specialist_id" name="specialist_id" required>
                <option value="">Seleccione...</option>
                @foreach ($specialists as $specialist)
                    <option value="{{ $specialist->id }}" @selected(old('specialist_id') == $specialist->id)>
                        {{ $specialist->fullName() }}
                        ({{ $specialist->specialties->pluck('name')->join(', ') }})
                    </option>
                @endforeach
            </select>

            <label for="scheduled_at">Fecha y hora</label>
            <input id="scheduled_at" type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}" required>

            <label for="reason">Motivo de consulta</label>
            <textarea id="reason" name="reason" rows="4" required>{{ old('reason') }}</textarea>

            <button type="submit" class="btn">Solicitar cita</button>
        </form>
    </div>
@endsection
