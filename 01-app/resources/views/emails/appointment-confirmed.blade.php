<h2>Consulta confirmada</h2>

<p>Su cita fue confirmada.</p>

<p>
    <strong>Fecha:</strong>
    {{ $appointment->scheduled_at->format('d/m/Y H:i') }}
</p>

<p>
    <strong>Especialista:</strong>
    {{ $appointment->specialist->fullName() }}
</p>

<p>
    <strong>Especialidad:</strong>
    {{ $appointment->specialty->name }}
</p>

<p>Puede ingresar a su consulta desde el detalle de la cita en WhiteAssistance.</p>
