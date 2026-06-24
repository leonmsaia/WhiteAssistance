<?php

namespace App\Modules\Appointments\Infrastructure\Models;

use App\Modules\Patients\Infrastructure\Models\Patient;
use App\Modules\Specialists\Infrastructure\Models\Specialist;
use App\Modules\Specialties\Infrastructure\Models\Specialty;
use App\Shared\Enums\AppointmentStatus;
use Database\Factories\AppointmentFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    /** @use HasFactory<AppointmentFactory> */
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'specialist_id',
        'specialty_id',
        'scheduled_at',
        'ends_at',
        'status',
        'reason',
        'cancellation_reason',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'ends_at' => 'datetime',
            'status' => AppointmentStatus::class,
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function specialist(): BelongsTo
    {
        return $this->belongsTo(Specialist::class);
    }

    public function specialty(): BelongsTo
    {
        return $this->belongsTo(Specialty::class);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('scheduled_at', '>=', now())
            ->whereIn('status', [
                AppointmentStatus::Scheduled,
                AppointmentStatus::Confirmed,
            ]);
    }

    public function canBeCancelled(): bool
    {
        return ! in_array($this->status, [
            AppointmentStatus::Completed,
            AppointmentStatus::Cancelled,
            AppointmentStatus::NoShow,
        ], true);
    }

    public function canBeConfirmed(): bool
    {
        return $this->status === AppointmentStatus::Scheduled;
    }

    public function canBeCompleted(): bool
    {
        return in_array($this->status, [
            AppointmentStatus::Scheduled,
            AppointmentStatus::Confirmed,
        ], true);
    }

    protected static function newFactory(): AppointmentFactory
    {
        return AppointmentFactory::new();
    }
}
