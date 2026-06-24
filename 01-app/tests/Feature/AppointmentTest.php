<?php

namespace Tests\Feature;

use App\Mail\AppointmentConfirmedMail;
use App\Modules\AccessControl\Domain\RoleName;
use App\Modules\Appointments\Infrastructure\Models\Appointment;
use App\Modules\Identity\Infrastructure\Models\User;
use App\Modules\Patients\Infrastructure\Models\Patient;
use App\Modules\Specialists\Infrastructure\Models\Specialist;
use App\Modules\Specialties\Infrastructure\Models\Specialty;
use App\Shared\Enums\AppointmentStatus;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function test_patient_can_book_appointment(): void
    {
        [$patientUser, $specialty, $specialist] = $this->createPatientAndSpecialist();

        $response = $this->actingAs($patientUser)->post('/appointments', [
            'specialty_id' => $specialty->id,
            'specialist_id' => $specialist->id,
            'scheduled_at' => now()->addDay()->format('Y-m-d\TH:i'),
            'reason' => 'Consulta general por control de rutina',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('appointments', [
            'patient_id' => $patientUser->patient->id,
            'specialist_id' => $specialist->id,
            'specialty_id' => $specialty->id,
            'status' => AppointmentStatus::Scheduled->value,
        ]);
    }

    public function test_specialist_can_confirm_appointment(): void
    {
        Mail::fake();

        [$patientUser, $specialty, $specialist] = $this->createPatientAndSpecialist();

        $appointment = Appointment::factory()->create([
            'patient_id' => $patientUser->patient->id,
            'specialist_id' => $specialist->id,
            'specialty_id' => $specialty->id,
            'status' => AppointmentStatus::Scheduled,
        ]);

        $specialistUser = $specialist->user;

        $response = $this->actingAs($specialistUser)->post(
            route('appointments.confirm', $appointment)
        );

        $response->assertRedirect();

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => AppointmentStatus::Confirmed->value,
        ]);

        Mail::assertSent(AppointmentConfirmedMail::class);
    }

    /**
     * @return array{0: User, 1: Specialty, 2: Specialist}
     */
    private function createPatientAndSpecialist(): array
    {
        $specialty = Specialty::factory()->create();

        $specialistUser = User::factory()->create();
        $specialistUser->assignRole(RoleName::SPECIALIST);

        $specialist = Specialist::factory()->create([
            'user_id' => $specialistUser->id,
        ]);
        $specialist->specialties()->attach($specialty);

        $patientUser = User::factory()->create();
        $patientUser->assignRole(RoleName::PATIENT);

        Patient::factory()->create([
            'user_id' => $patientUser->id,
        ]);

        $patientUser->load('patient');

        return [$patientUser, $specialty, $specialist];
    }
}
