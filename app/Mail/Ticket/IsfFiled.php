<?php

namespace App\Mail\Ticket;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class IsfFiled extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $config = config('constants.config');
        $mail = $config['config'];
        return $this->from($mail['from'], $mail['fromName'])
            ->subject('ISF filing confirmation')
            ->view('emails.tickets.isf_filed')
            ->with([
                'infoEmail' => $config['INFO_EMAIL'],
                'serviceName' => $config['SERVICE_NAME'],
            ]);
    }
}
