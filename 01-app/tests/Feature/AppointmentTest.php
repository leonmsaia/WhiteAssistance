<?php

namespace Tests\Feature;

use App\Mail\AppointmentConfirmedMail;
use App\Modules\AccessControl\Domain\RoleName;
use App\Modules\Appointments\Infrastructure\Models\Appointment;
use App\Modules\Identity\Infrastructure\Models\User;
use App\Modules\Patients\Infrastructure\Models\Patient;
use App\Modules\Specialists\Infrastructure\Models\Specialist;
use App\Modules\Specialists\Infrastructure\Models\SpecialistAvailability;
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
            'scheduled_at' => $this->appointmentTimeInsideAvailability(),
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

    public function test_specialist_with_availability_can_receive_appointment_inside_window(): void
    {
        [$patientUser, $specialty, $specialist] = $this->createPatientAndSpecialist();

        $scheduledAt = $this->appointmentTimeInsideAvailability();

        $response = $this->actingAs($patientUser)->post('/appointments', [
            'specialty_id' => $specialty->id,
            'specialist_id' => $specialist->id,
            'scheduled_at' => $scheduledAt,
            'reason' => 'Consulta dentro del horario disponible',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('appointments', [
            'specialist_id' => $specialist->id,
            'scheduled_at' => \Carbon\Carbon::parse($scheduledAt)->format('Y-m-d H:i:s'),
        ]);
    }

    public function test_specialist_cannot_receive_overlapping_appointment(): void
    {
        [$patientUser, $specialty, $specialist] = $this->createPatientAndSpecialist();

        $scheduledAt = \Carbon\Carbon::parse($this->appointmentTimeInsideAvailability());

        Appointment::factory()->create([
            'patient_id' => $patientUser->patient->id,
            'specialist_id' => $specialist->id,
            'specialty_id' => $specialty->id,
            'scheduled_at' => $scheduledAt,
            'ends_at' => $scheduledAt->copy()->addMinutes(30),
            'status' => AppointmentStatus::Scheduled,
        ]);

        $response = $this->actingAs($patientUser)->post('/appointments', [
            'specialty_id' => $specialty->id,
            'specialist_id' => $specialist->id,
            'scheduled_at' => $scheduledAt->copy()->addMinutes(15)->format('Y-m-d\TH:i'),
            'reason' => 'Intento de cita solapada con otra existente',
        ]);

        $response->assertSessionHasErrors('scheduled_at');
        $this->assertDatabaseCount('appointments', 1);
    }

    public function test_specialist_cannot_receive_appointment_outside_availability(): void
    {
        [$patientUser, $specialty, $specialist] = $this->createPatientAndSpecialist();

        $scheduledAt = \Carbon\Carbon::parse($this->appointmentTimeInsideAvailability())
            ->setTime(14, 0);

        $response = $this->actingAs($patientUser)->post('/appointments', [
            'specialty_id' => $specialty->id,
            'specialist_id' => $specialist->id,
            'scheduled_at' => $scheduledAt->format('Y-m-d\TH:i'),
            'reason' => 'Intento de cita fuera del horario disponible',
        ]);

        $response->assertSessionHasErrors('scheduled_at');
        $this->assertDatabaseCount('appointments', 0);
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
            'consultation_duration_minutes' => 30,
        ]);
        $specialist->specialties()->attach($specialty);

        SpecialistAvailability::query()->create([
            'specialist_id' => $specialist->id,
            'weekday' => now()->addDay()->dayOfWeek,
            'start_time' => '09:00',
            'end_time' => '13:00',
        ]);

        $patientUser = User::factory()->create();
        $patientUser->assignRole(RoleName::PATIENT);

        Patient::factory()->create([
            'user_id' => $patientUser->id,
        ]);

        $patientUser->load('patient');

        return [$patientUser, $specialty, $specialist];
    }

    private function appointmentTimeInsideAvailability(): string
    {
        return now()->addDay()->setTime(10, 0)->format('Y-m-d\TH:i');
    }
}
