<?php

use App\Modules\AccessControl\Domain\RoleName;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();

        if ($user->hasRole(RoleName::ADMIN)) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole(RoleName::SPECIALIST)) {
            return redirect()->route('specialist.dashboard');
        }

        return redirect()->route('patient.dashboard');
    }

    return redirect()->route('login');
});
