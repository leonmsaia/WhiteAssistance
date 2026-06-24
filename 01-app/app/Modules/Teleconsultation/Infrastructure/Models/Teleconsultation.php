<?php

namespace App\Modules\Teleconsultation\Infrastructure\Models;

use App\Modules\Appointments\Infrastructure\Models\Appointment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Teleconsultation extends Model
{
    protected $fillable = [
        'appointment_id',
        'room_name',
        'room_url',
        'status',
        'started_at',
        'ended_at',
        'consultation_notes',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
        ];
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }
}
