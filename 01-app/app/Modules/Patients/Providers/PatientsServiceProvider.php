<?php

namespace App\Modules\Patients\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class PatientsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::middleware('web')
            ->group(base_path('app/Modules/Patients/Routes/web.php'));
    }
}
