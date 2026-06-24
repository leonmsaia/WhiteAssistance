<?php

namespace Database\Factories;

use App\Modules\Specialties\Infrastructure\Models\Specialty;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Specialty>
 */
class SpecialtyFactory extends Factory
{
    protected $model = Specialty::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'code' => strtoupper(fake()->unique()->lexify('???')),
            'name' => ucfirst($name),
            'is_active' => true,
        ];
    }
}
