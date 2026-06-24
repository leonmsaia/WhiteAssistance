<?php

namespace App\Modules\Appointments\Providers;

use App\Modules\Appointments\Infrastructure\Models\Appointment;
use App\Modules\Appointments\Policies\AppointmentPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppointmentsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(Appointment::class, AppointmentPolicy::class);

        Route::middleware('web')
            ->group(base_path('app/Modules/Appointments/Routes/web.php'));
    }
}
