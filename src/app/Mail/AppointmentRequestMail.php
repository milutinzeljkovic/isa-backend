<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;
use App\Appointment;
use App\OperationsRoom;


class AppointmentRequestMail extends Mailable
{

    public $user;
    public $appointment;
    public $patient;


    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user,Appointment $appointment,User $patient)
    {
        $this->user = $user;
        $this->appointment = $appointment;
        $this->patient = $patient;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.newAppointmentRequest');
    }
}
