<?php

namespace Database\Factories;

use App\Modules\Identity\Infrastructure\Models\User;
use App\Modules\Patients\Infrastructure\Models\Patient;
use App\Shared\Enums\ProfileStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Patient>
 */
class PatientFactory extends Factory
{
    protected $model = Patient::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'document_number' => fake()->numerify('########'),
            'phone' => fake()->phoneNumber(),
            'status' => ProfileStatus::Active,
        ];
    }
}
