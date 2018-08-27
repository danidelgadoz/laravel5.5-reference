<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SuscripcionMailing extends Mailable
{
    use Queueable, SerializesModels;

    public $suscripcion;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($suscripcion)
    {
        $this->suscripcion = $suscripcion;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = "Craftimes: Tu suscripción se ha realizado con éxito";

        return $this->from('ventas@craftimes.com', 'Craftimes Ventas')
                    ->to($this->suscripcion['email'])
                    ->cc([env('CRAFTIMES_EMAIL_CC_PEDIDO')])
                    ->subject($subject)
                    ->view('email.suscripcion');
    }
}
