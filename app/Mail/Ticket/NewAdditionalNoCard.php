<?php

namespace App\Mail\Ticket;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewAdditionalNoCard extends Mailable
{
    use Queueable, SerializesModels;

    protected $data;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        array $data
    ) {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $config = config('constants.config.mail');
        $build = $this->from($config['from'], $config['fromName'])
            ->subject('Additional charge from Clearit')
            ->view('emails.tickets.new_additional_no_card')
            ->with($this->data);
        if ($this->data['invoicePath']) {
            $build->attach($this->data['invoicePath']);
        }
        return $build;
    }
}
