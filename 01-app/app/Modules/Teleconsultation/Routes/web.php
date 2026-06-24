<?php

use App\Modules\Teleconsultation\Http\Controllers\TeleconsultationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'active'])->group(function (): void {
    Route::get('/teleconsultations/{appointment}', [TeleconsultationController::class, 'show'])
        ->name('teleconsultations.show');
    Route::post('/teleconsultations/{appointment}/start', [TeleconsultationController::class, 'start'])
        ->name('teleconsultations.start');
    Route::post('/teleconsultations/{appointment}/finish', [TeleconsultationController::class, 'finish'])
        ->name('teleconsultations.finish');
});
