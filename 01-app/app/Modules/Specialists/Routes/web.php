<?php

use App\Modules\Specialists\Http\Controllers\SpecialistAvailabilityController;
use App\Modules\Specialists\Http\Controllers\SpecialistDashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'active', 'role:specialist'])->prefix('specialist')->name('specialist.')->group(function (): void {
    Route::get('/dashboard', SpecialistDashboardController::class)->name('dashboard');

    Route::get('/availability', [SpecialistAvailabilityController::class, 'index'])->name('availability.index');
    Route::post('/availability', [SpecialistAvailabilityController::class, 'store'])->name('availability.store');
    Route::delete('/availability/{availability}', [SpecialistAvailabilityController::class, 'destroy'])->name('availability.destroy');
});
