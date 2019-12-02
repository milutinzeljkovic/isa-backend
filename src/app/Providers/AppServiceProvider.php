<?php

namespace App\Providers;

use App\Services\PatientService;


use Illuminate\Support\ServiceProvider;
use App\Services\ClinicService;
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
         //   'App\Services\IPatientService',
          //  PatientService::class,
            'App\Services\IClinicService',
            ClinicService::class
        );
        $this->app->bind(
            'App\Services\IPatientService',
            PatientService::class,
        );
    }
}
