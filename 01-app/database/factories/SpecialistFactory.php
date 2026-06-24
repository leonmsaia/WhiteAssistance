<?php

namespace Database\Factories;

use App\Modules\Identity\Infrastructure\Models\User;
use App\Modules\Specialists\Infrastructure\Models\Specialist;
use App\Shared\Enums\ProfileStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Specialist>
 */
class SpecialistFactory extends Factory
{
    protected $model = Specialist::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'license_number' => 'MP-'.fake()->unique()->numerify('#####'),
            'bio' => fake()->sentence(),
            'status' => ProfileStatus::Active,
        ];
    }
}
