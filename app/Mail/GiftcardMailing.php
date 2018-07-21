<?php

namespace App\Mail;

use App\Giftcard;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class GiftcardMailing extends Mailable
{
    use Queueable, SerializesModels;

    public $giftcard;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Giftcard $giftcard)
    {
        $this->giftcard = $giftcard;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('ventas@craftimes.com', 'Craftimes Ventas')
                    ->to($this->giftcard->mailing_owner_address)
                    ->subject('Craftimes: canjea tu giftcard')
                    ->view('email.giftcard');
    }
}
