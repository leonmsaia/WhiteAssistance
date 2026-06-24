<?php

namespace App\Modules\Teleconsultation\Providers;

use App\Modules\Teleconsultation\Infrastructure\Models\Teleconsultation;
use App\Modules\Teleconsultation\Policies\TeleconsultationPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class TeleconsultationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(Teleconsultation::class, TeleconsultationPolicy::class);

        Route::middleware('web')
            ->group(base_path('app/Modules/Teleconsultation/Routes/web.php'));
    }
}
