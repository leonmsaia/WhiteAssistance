<?php

namespace App\Modules\Patients\Infrastructure\Models;

use App\Modules\Appointments\Infrastructure\Models\Appointment;
use App\Modules\Identity\Infrastructure\Models\User;
use App\Shared\Enums\ProfileStatus;
use Database\Factories\PatientFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    /** @use HasFactory<PatientFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'document_number',
        'phone',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => ProfileStatus::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function fullName(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    protected static function newFactory(): PatientFactory
    {
        return PatientFactory::new();
    }
}
