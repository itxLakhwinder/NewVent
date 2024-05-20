<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Approve extends Mailable
{
    use Queueable, SerializesModels;

    // public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
		return $this->from('info@ventspaceapp.com','Vent Space')
                // ->replyTo($this->user->email)
                ->subject('Account Approved')
                ->view('emails.approve');
  
    }
}
