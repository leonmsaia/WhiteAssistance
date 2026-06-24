<h2>Teleconsulta iniciada</h2>

<p>Su especialista ha iniciado la teleconsulta.</p>

<p>
    <strong>Fecha:</strong>
    {{ $appointment->scheduled_at->format('d/m/Y H:i') }}
</p>

<p>
    <strong>Especialista:</strong>
    {{ $appointment->specialist->fullName() }}
</p>

<p>
    <a href="{{ $teleconsultation->room_url }}">Ingresar a la videollamada</a>
</p>
