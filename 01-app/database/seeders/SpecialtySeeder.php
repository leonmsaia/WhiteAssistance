<?php

namespace Database\Seeders;

use App\Modules\Specialties\Infrastructure\Models\Specialty;
use Illuminate\Database\Seeder;

class SpecialtySeeder extends Seeder
{
    public function run(): void
    {
        $specialties = [
            ['code' => 'GENERAL', 'name' => 'Medicina General'],
            ['code' => 'CARDIO', 'name' => 'Cardiología'],
            ['code' => 'DERMA', 'name' => 'Dermatología'],
            ['code' => 'PEDIAT', 'name' => 'Pediatría'],
            ['code' => 'PSYCH', 'name' => 'Psicología Clínica'],
        ];

        foreach ($specialties as $specialty) {
            Specialty::query()->updateOrCreate(
                ['code' => $specialty['code']],
                ['name' => $specialty['name'], 'is_active' => true],
            );
        }
    }
}
