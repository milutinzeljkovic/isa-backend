<?php

namespace App\Providers;
use App\Patient;
use App\Policies\PatientPolicy;
use App\Policies\AppointmentPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Patient' => 'App\Policies\PatientPolicy',
        'App\Appointment' => 'App\Policies\AppointmentPolicy',
        'App\ClinicAdmin' => 'App\Policies\MedicalStaffPolicy',
        'App\ClinicalCenterAdmin' => 'App\Policies\MedicalStaffPolicy',
        'App\Clinic' => 'App\Policies\ClinicPolicy'
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
