<?php

use App\Modules\Specialists\Http\Controllers\SpecialistDashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'active', 'role:specialist'])->prefix('specialist')->name('specialist.')->group(function (): void {
    Route::get('/dashboard', SpecialistDashboardController::class)->name('dashboard');
});
