<?php

namespace App\Modules\Patients\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Modules\AccessControl\Domain\RoleName;
use App\Modules\Identity\Infrastructure\Models\User;
use App\Modules\Patients\Infrastructure\Models\Patient;
use App\Shared\Enums\ProfileStatus;
use App\Shared\Enums\UserStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PatientRegisterController extends Controller
{
    public function create(): View
    {
        return view('auth.register-patient');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:254', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'document_number' => ['nullable', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        DB::transaction(function () use ($validated): void {
            $user = User::query()->create([
                'name' => trim("{$validated['first_name']} {$validated['last_name']}"),
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'status' => UserStatus::Active,
            ]);

            $user->assignRole(RoleName::PATIENT);

            Patient::query()->create([
                'user_id' => $user->id,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'document_number' => $validated['document_number'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'status' => ProfileStatus::Active,
            ]);
        });

        Auth::attempt([
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        $request->session()->regenerate();

        return redirect()->route('patient.dashboard')
            ->with('status', 'Registro completado correctamente.');
    }
}
