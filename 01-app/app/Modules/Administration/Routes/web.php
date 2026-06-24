<?php

use App\Modules\Administration\Http\Controllers\AdminDashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'active', 'role:admin'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
});
