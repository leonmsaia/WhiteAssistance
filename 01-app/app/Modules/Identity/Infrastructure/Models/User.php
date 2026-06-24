<?php

namespace App\Modules\Identity\Infrastructure\Models;

use App\Modules\Patients\Infrastructure\Models\Patient;
use App\Modules\Specialists\Infrastructure\Models\Specialist;
use App\Shared\Enums\UserStatus;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    protected string $guard_name = 'web';

    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => UserStatus::class,
        ];
    }

    public function isActive(): bool
    {
        return $this->status === UserStatus::Active;
    }

    public function patient(): HasOne
    {
        return $this->hasOne(Patient::class);
    }

    public function specialist(): HasOne
    {
        return $this->hasOne(Specialist::class);
    }

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }
}
