<?php

namespace Database\Factories;

use App\Modules\Appointments\Infrastructure\Models\Appointment;
use App\Modules\Patients\Infrastructure\Models\Patient;
use App\Modules\Specialists\Infrastructure\Models\Specialist;
use App\Modules\Specialties\Infrastructure\Models\Specialty;
use App\Shared\Enums\AppointmentStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        $scheduledAt = fake()->dateTimeBetween('+1 day', '+14 days');

        return [
            'patient_id' => Patient::factory(),
            'specialist_id' => Specialist::factory(),
            'specialty_id' => Specialty::factory(),
            'scheduled_at' => $scheduledAt,
            'ends_at' => (clone $scheduledAt)->modify('+30 minutes'),
            'status' => AppointmentStatus::Scheduled,
            'reason' => fake()->sentence(),
        ];
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AppointmentStatus::Confirmed,
        ]);
    }
}
