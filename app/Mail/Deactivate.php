<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Deactivate extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
	public $reason;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $reason)
    {
        $this->user = $user;
		$this->reason = $reason;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
		return $this->from('info@ventspaceapp.com','Vent Space')
                ->replyTo($this->user->email)
                ->subject('Account Deactivated')
                ->view('emails.deactivated');
  
    }
}
