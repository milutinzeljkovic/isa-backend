<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use App\User;
use App\Appointment;
use App\OperationsRoom;
use App\Clinic;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AppointmentReservedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $appointment;
    public $doctor;
    public $operationsRoom;
    public $clinic;
    public $id;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user,Appointment $appointment, User $doctor, OperationsRoom $operationsRoom, Clinic $clinic, $id )
    {
        $this->user = $user;
        $this->appointment = $appointment;
        $this->doctor = $doctor;
        $this->operationsRoom = $operationsRoom;
        $this->clinic = $clinic;
        $this->id = $id;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.reserved');
    }
}
