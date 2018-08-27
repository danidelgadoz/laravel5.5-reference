<?php

namespace App\Mail;

use App\ContactoLog;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ContactUs extends Mailable
{
    use Queueable, SerializesModels;

    public $contacto;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(ContactoLog $contacto)
    {
        $this->contacto = $contacto;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->contacto->email, $this->contacto->nombres)
                    ->to(env('CRAFTIMES_EMAIL_CONTACTO'))
                    ->subject('Craftimes Contacto')
                    ->view('email.contact');
    }
}
