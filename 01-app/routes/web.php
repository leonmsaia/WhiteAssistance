<?php

use App\Support\DashboardResolver;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route(
            DashboardResolver::routeName(auth()->user())
        );
    }

    return redirect()->route('login');
});
