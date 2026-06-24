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

            <label for="scheduled_at">Fecha y hora</label>
            <input id="scheduled_at" type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}" required>

            <label for="specialist_id">Especialista</label>
            <select id="specialist_id" name="specialist_id" required>
                <option value="">Seleccione fecha y hora primero...</option>
                @foreach ($specialists as $specialist)
                    <option
                        value="{{ $specialist->id }}"
                        data-weekdays="{{ $specialist->availabilities->pluck('weekday')->unique()->implode(',') }}"
                        data-duration="{{ $specialist->consultation_duration_minutes ?? 30 }}"
                        @selected(old('specialist_id') == $specialist->id)
                        hidden
                    >
                        {{ $specialist->fullName() }}
                        ({{ $specialist->specialties->pluck('name')->join(', ') }})
                    </option>
                @endforeach
            </select>

            <p id="consultation-duration" class="muted" style="margin-top: -.5rem;" hidden>
                Duración: <strong id="consultation-duration-value"></strong> minutos
            </p>

            <label for="reason">Motivo de consulta</label>
            <textarea id="reason" name="reason" rows="4" required>{{ old('reason') }}</textarea>

            <button type="submit" class="btn">Solicitar cita</button>
        </form>
    </div>

    <script>
        (function () {
            const scheduledAtInput = document.getElementById('scheduled_at');
            const specialistSelect = document.getElementById('specialist_id');
            const durationBlock = document.getElementById('consultation-duration');
            const durationValue = document.getElementById('consultation-duration-value');
            const options = Array.from(specialistSelect.querySelectorAll('option[data-weekdays]'));
            const placeholder = specialistSelect.querySelector('option:not([data-weekdays])');
            const oldSpecialistId = @json(old('specialist_id'));

            function updateDuration() {
                const selected = specialistSelect.selectedOptions[0];

                if (!selected || !selected.dataset.duration) {
                    durationBlock.hidden = true;
                    return;
                }

                durationValue.textContent = selected.dataset.duration;
                durationBlock.hidden = false;
            }

            function filterSpecialists() {
                const value = scheduledAtInput.value;
                let weekday = null;

                if (value) {
                    weekday = new Date(value).getDay();
                }

                let visibleCount = 0;

                options.forEach(function (option) {
                    const weekdays = option.dataset.weekdays
                        ? option.dataset.weekdays.split(',').map(Number)
                        : [];

                    const visible = weekday !== null && weekdays.includes(weekday);
                    option.hidden = !visible;
                    option.disabled = !visible;

                    if (visible) {
                        visibleCount++;
                    }
                });

                if (weekday === null) {
                    placeholder.textContent = 'Seleccione fecha y hora primero...';
                    specialistSelect.value = '';
                    updateDuration();
                    return;
                }

                if (visibleCount === 0) {
                    placeholder.textContent = 'No hay especialistas disponibles ese día';
                    specialistSelect.value = '';
                    updateDuration();
                    return;
                }

                placeholder.textContent = 'Seleccione...';

                const current = specialistSelect.value;
                const currentVisible = options.some(function (option) {
                    return !option.hidden && option.value === current;
                });

                if (!currentVisible) {
                    specialistSelect.value = '';
                }

                updateDuration();
            }

            scheduledAtInput.addEventListener('change', filterSpecialists);
            scheduledAtInput.addEventListener('input', filterSpecialists);
            specialistSelect.addEventListener('change', updateDuration);

            filterSpecialists();
            updateDuration();

            if (oldSpecialistId) {
                specialistSelect.value = oldSpecialistId;
                updateDuration();
            }
        })();
    </script>
@endsection
