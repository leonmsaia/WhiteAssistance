<?php

use App\Providers\AppServiceProvider;
use App\Modules\Administration\Providers\AdministrationServiceProvider;
use App\Modules\Appointments\Providers\AppointmentsServiceProvider;
use App\Modules\Identity\Providers\IdentityServiceProvider;
use App\Modules\Patients\Providers\PatientsServiceProvider;
use App\Modules\Specialists\Providers\SpecialistsServiceProvider;
use App\Modules\Teleconsultation\Providers\TeleconsultationServiceProvider;

return [
    AppServiceProvider::class,
    IdentityServiceProvider::class,
    PatientsServiceProvider::class,
    SpecialistsServiceProvider::class,
    AdministrationServiceProvider::class,
    AppointmentsServiceProvider::class,
    TeleconsultationServiceProvider::class,
];
