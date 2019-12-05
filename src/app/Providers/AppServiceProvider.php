<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ClinicService;
use App\Services\PatientService;
use App\Services\DoctorService;

use Illuminate\Support\Facades\Log;
use DB;



class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        DB::listen(function ($query) {
            \Log::info(
                $query->sql, $query->bindings, $query->time
            );
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        
        $this->app->bind(
            'App\Services\IClinicService',
            ClinicService::class
        );
        $this->app->bind(
            'App\Services\IPatientService',
            PatientService::class,
        );

        $this->app->bind(
            'App\Services\IDoctorService',
            DoctorService::class
        );
    }
}
