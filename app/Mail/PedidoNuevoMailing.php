<?php

namespace App\Mail;

use App\Pedido;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PedidoNuevoMailing extends Mailable
{
    use Queueable, SerializesModels;

    public $pedido;
    public $envio;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Pedido $pedido, $envio)
    {
        $this->pedido = $pedido;
        $this->envio = $envio;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('ventas@craftimes.com', 'Craftimes Ventas')
                    ->to($this->pedido->cliente['email'])
                    ->cc([env('CRAFTIMES_EMAIL_CC_PEDIDO')])
                    ->subject('Craftimes: Nuevo pedido')
                    ->view('email.pedidonuevo');
    }
}
