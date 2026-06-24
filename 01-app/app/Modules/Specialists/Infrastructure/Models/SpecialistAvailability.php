<?php

namespace App\Modules\Specialists\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpecialistAvailability extends Model
{
    protected $fillable = [
        'specialist_id',
        'weekday',
        'start_time',
        'end_time',
    ];

    public function specialist(): BelongsTo
    {
        return $this->belongsTo(Specialist::class);
    }

    public function weekdayName(): string
    {
        return self::weekdayNames()[$this->weekday] ?? (string) $this->weekday;
    }

    /**
     * @return array<int, string>
     */
    public static function weekdayNames(): array
    {
        return [
            0 => 'Domingo',
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
        ];
    }
}
