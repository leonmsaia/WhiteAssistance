<?php

use App\Modules\Patients\Http\Controllers\Auth\PatientRegisterController;
use App\Modules\Patients\Http\Controllers\PatientDashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/register', [PatientRegisterController::class, 'create'])->name('register');
    Route::post('/register', [PatientRegisterController::class, 'store']);
});

Route::middleware(['auth', 'active', 'role:patient'])->prefix('patient')->name('patient.')->group(function (): void {
    Route::get('/dashboard', PatientDashboardController::class)->name('dashboard');
});
