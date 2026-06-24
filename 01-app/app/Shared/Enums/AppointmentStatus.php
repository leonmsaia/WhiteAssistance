<?php

namespace App\Shared\Enums;

enum AppointmentStatus: string
{
    case Scheduled = 'scheduled';
    case Confirmed = 'confirmed';
    case Cancelled = 'cancelled';
    case Completed = 'completed';
    case NoShow = 'no_show';

    public function label(): string
    {
        return match ($this) {
            self::Scheduled => 'Programada',
            self::Confirmed => 'Confirmada',
            self::Cancelled => 'Cancelada',
            self::Completed => 'Completada',
            self::NoShow => 'No asistió',
        };
    }
}
