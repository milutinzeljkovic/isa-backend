<?php

namespace App\Policies;

use App\User;
use App\Appointment;
use Illuminate\Auth\Access\HandlesAuthorization;

class AppointmentPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

        /**
     * Determine whether the user can update the patient.
     *
     * @param  \App\User  $user
     * @param  \App\Patient  $patient
     * @return mixed
     */
    public function reserve(User $user, $appointment)
    {
        $app = Appointment::find($appointment);
        if($app->patient_id != null)
        {
            return false;
        }
        return true;
    }
}
