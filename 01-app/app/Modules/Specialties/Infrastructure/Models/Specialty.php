<?php

namespace App\Modules\Specialties\Infrastructure\Models;

use App\Modules\Appointments\Infrastructure\Models\Appointment;
use App\Modules\Specialists\Infrastructure\Models\Specialist;
use Database\Factories\SpecialtyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Specialty extends Model
{
    /** @use HasFactory<SpecialtyFactory> */
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function specialists(): BelongsToMany
    {
        return $this->belongsToMany(Specialist::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    protected static function newFactory(): SpecialtyFactory
    {
        return SpecialtyFactory::new();
    }
}
