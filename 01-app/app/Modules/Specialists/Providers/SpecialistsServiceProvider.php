<?php

namespace App\Modules\Specialists\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class SpecialistsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::middleware('web')
            ->group(base_path('app/Modules/Specialists/Routes/web.php'));
    }
}
