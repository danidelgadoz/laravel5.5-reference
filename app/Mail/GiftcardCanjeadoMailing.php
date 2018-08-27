<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class GiftcardCanjeadoMailing extends Mailable
{
    use Queueable, SerializesModels;

    public $pedido;
    public $giftcard;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($pedido, $giftcard)
    {
        $this->pedido = $pedido;
        $this->giftcard = $giftcard;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = "Craftimes: Gitcard canjeado";

        return $this->from('contacto@craftimes.com', 'Craftimes')
                    ->to($this->pedido->cliente['email'])
                    ->cc([env('CRAFTIMES_EMAIL_CC_PEDIDO')])
                    ->subject($subject)
                    ->view('email.giftcardcanjeado');
    }
}
