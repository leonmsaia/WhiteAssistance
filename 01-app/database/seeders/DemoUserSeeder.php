<?php

namespace Database\Seeders;

use App\Modules\AccessControl\Domain\RoleName;
use App\Modules\Identity\Infrastructure\Models\User;
use App\Modules\Patients\Infrastructure\Models\Patient;
use App\Modules\Specialists\Infrastructure\Models\Specialist;
use App\Modules\Specialties\Infrastructure\Models\Specialty;
use App\Shared\Enums\ProfileStatus;
use App\Shared\Enums\UserStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@whiteassistance.local'],
            [
                'name' => 'Admin WhiteAssistance',
                'password' => Hash::make('password'),
                'status' => UserStatus::Active,
            ],
        );
        $admin->syncRoles([RoleName::ADMIN]);

        $specialty = Specialty::query()->where('code', 'GENERAL')->first()
            ?? Specialty::query()->first();

        $specialistUser = User::query()->updateOrCreate(
            ['email' => 'specialist@whiteassistance.local'],
            [
                'name' => 'Dr. Ana Specialist',
                'password' => Hash::make('password'),
                'status' => UserStatus::Active,
            ],
        );
        $specialistUser->syncRoles([RoleName::SPECIALIST]);

        $specialist = Specialist::query()->updateOrCreate(
            ['user_id' => $specialistUser->id],
            [
                'first_name' => 'Ana',
                'last_name' => 'Specialist',
                'license_number' => 'MP-10001',
                'bio' => 'Médica general con enfoque en teleconsulta.',
                'status' => ProfileStatus::Active,
            ],
        );

        if ($specialty) {
            $specialist->specialties()->syncWithoutDetaching([$specialty->id]);
        }

        $patientUser = User::query()->updateOrCreate(
            ['email' => 'patient@whiteassistance.local'],
            [
                'name' => 'María Paciente',
                'password' => Hash::make('password'),
                'status' => UserStatus::Active,
            ],
        );
        $patientUser->syncRoles([RoleName::PATIENT]);

        Patient::query()->updateOrCreate(
            ['user_id' => $patientUser->id],
            [
                'first_name' => 'María',
                'last_name' => 'Paciente',
                'document_number' => '30123456',
                'phone' => '+5491112345678',
                'status' => ProfileStatus::Active,
            ],
        );
    }
}
