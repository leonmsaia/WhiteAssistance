<?php

namespace Tests\Feature;

use App\Modules\AccessControl\Domain\RoleName;
use App\Modules\Identity\Infrastructure\Models\User;
use App\Modules\Patients\Infrastructure\Models\Patient;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function test_patient_can_login(): void
    {
        $user = User::factory()->create([
            'email' => 'patient@example.com',
            'password' => 'password',
        ]);
        $user->assignRole(RoleName::PATIENT);

        Patient::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('patient.dashboard'));

        $response->assertOk();
        $this->assertAuthenticatedAs($user);
    }
}
