<?php

namespace Tests\Feature;

use App\Mail\TeleconsultationEndedMail;
use App\Mail\TeleconsultationStartedMail;
use App\Modules\AccessControl\Domain\RoleName;
use App\Modules\Appointments\Infrastructure\Models\Appointment;
use App\Modules\Identity\Infrastructure\Models\User;
use App\Modules\Patients\Infrastructure\Models\Patient;
use App\Modules\Specialists\Infrastructure\Models\Specialist;
use App\Modules\Specialties\Infrastructure\Models\Specialty;
use App\Modules\Teleconsultation\Infrastructure\Models\Teleconsultation;
use App\Shared\Enums\AppointmentStatus;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class TeleconsultationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function test_specialist_can_start_teleconsultation(): void
    {
        Mail::fake();

        [$patientUser, $appointment, $specialistUser] = $this->createConfirmedAppointment();

        $response = $this->actingAs($specialistUser)->post(
            route('teleconsultations.start', $appointment)
        );

        $response->assertRedirect(route('teleconsultations.show', $appointment));

        $this->assertDatabaseHas('teleconsultations', [
            'appointment_id' => $appointment->id,
            'status' => 'started',
        ]);

        Mail::assertSent(TeleconsultationStartedMail::class);
    }

    public function test_specialist_can_finish_teleconsultation(): void
    {
        Mail::fake();

        [$patientUser, $appointment, $specialistUser] = $this->createConfirmedAppointment();

        Teleconsultation::query()->create([
            'appointment_id' => $appointment->id,
            'room_name' => "wa-appointment-{$appointment->id}",
            'room_url' => 'https://meet.jit.si/wa-appointment-'.$appointment->id,
            'status' => 'started',
            'started_at' => now(),
        ]);

        $response = $this->actingAs($specialistUser)->post(
            route('teleconsultations.finish', $appointment),
            ['consultation_notes' => 'Paciente estable. Control en 30 días.']
        );

        $response->assertRedirect(route('teleconsultations.show', $appointment));

        $this->assertDatabaseHas('teleconsultations', [
            'appointment_id' => $appointment->id,
            'status' => 'ended',
            'consultation_notes' => 'Paciente estable. Control en 30 días.',
        ]);

        Mail::assertSent(TeleconsultationEndedMail::class);
    }

    public function test_patient_cannot_finish_teleconsultation(): void
    {
        [$patientUser, $appointment] = $this->createConfirmedAppointment();

        Teleconsultation::query()->create([
            'appointment_id' => $appointment->id,
            'room_name' => "wa-appointment-{$appointment->id}",
            'room_url' => 'https://meet.jit.si/wa-appointment-'.$appointment->id,
            'status' => 'started',
            'started_at' => now(),
        ]);

        $response = $this->actingAs($patientUser)->post(
            route('teleconsultations.finish', $appointment),
            ['consultation_notes' => 'Intento no autorizado']
        );

        $response->assertForbidden();

        $this->assertDatabaseHas('teleconsultations', [
            'appointment_id' => $appointment->id,
            'status' => 'started',
        ]);
    }

    /**
     * @return array{0: User, 1: Appointment, 2: User}
     */
    private function createConfirmedAppointment(): array
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

        $appointment = Appointment::factory()->create([
            'patient_id' => $patientUser->patient->id,
            'specialist_id' => $specialist->id,
            'specialty_id' => $specialty->id,
            'status' => AppointmentStatus::Confirmed,
        ]);

        return [$patientUser, $appointment, $specialistUser];
    }
}
