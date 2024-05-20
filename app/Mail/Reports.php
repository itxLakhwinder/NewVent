<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Reports extends Mailable
{
    use Queueable, SerializesModels;

    public $report;
    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($report,$user)
    {
        $this->report = $report;
        $this->user = $user;
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
                ->subject('Report')
                ->view('emails.reports');
  
    }
}
