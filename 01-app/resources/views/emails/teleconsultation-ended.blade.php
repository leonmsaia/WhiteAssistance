<h2>Teleconsulta finalizada</h2>

<p>Su teleconsulta ha finalizado.</p>

<p>
    <strong>Fecha:</strong>
    {{ $appointment->scheduled_at->format('d/m/Y H:i') }}
</p>

<p>
    <strong>Especialista:</strong>
    {{ $appointment->specialist->fullName() }}
</p>

<p>Puede revisar el resumen de la consulta en WhiteAssistance.</p>
