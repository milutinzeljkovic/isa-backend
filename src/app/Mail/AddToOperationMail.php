<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;
use App\Operations;
use App\OperationsRoom;


class AddToOperationMail extends Mailable
{

    public $user;
    public $operation;
    public $room;


    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user,Operations $operation,OperationsRoom $room)
    {
        $this->user = $user;
        $this->operation = $operation;
        $this->room = $room;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.addToOperationMail');
    }
}
