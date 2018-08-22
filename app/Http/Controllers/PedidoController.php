<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Pedido;
use App\Envio;
use App\Cliente;
use App\Producto;
use App\PedidoDetalle;
use App\Giftcard;
use App\Factura;
use App\Suscripcion;
use App\PayuConfirmacion;
use App\Mail\PedidoNuevoMailing;
use App\Mail\GiftcardMailing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Log;

class PedidoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pedidos = Pedido::with(['cliente', 'cupon', 'factura', 'detalles'])
            ->orderByDesc("id")
            ->get();
        return response($pedidos, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $pedido = DB::transaction(function () use ($request) {
            $cliente = Cliente::where('email', $request->cliente['email'])->first();

            if (!$cliente) {
                $cliente = new Cliente();
                $cliente->card_number = null;
                $cliente->first_name = $request->cliente['first_name'];
                $cliente->last_name = $request->cliente['last_name'];
                $cliente->email = $request->cliente['email'];
                $cliente->address = $request->cliente['address'];
                $cliente->address_city = $request->cliente['address_city'];
                $cliente->country_code = $request->cliente['country_code'];
                $cliente->phone_number = $request->cliente['phone_number'];
                $cliente->save();
            } else {
                $cliente->card_number = null;
                $cliente->first_name = $request->cliente['first_name'];
                $cliente->last_name = $request->cliente['last_name'];
                $cliente->address = $request->cliente['address'];
                $cliente->address_city = $request->cliente['address_city'];
                $cliente->country_code = $request->cliente['country_code'];
                $cliente->phone_number = $request->cliente['phone_number'];
                $cliente->save();
            }

            if ($request->factura) {
                $factura = new Factura();
                $factura->ruc = $request->factura['ruc'];
                $factura->razon_social = $request->factura['razon_social'];
                $factura->direccion = $request->factura['direccion'];
                $factura->distrito = $request->factura['distrito'];
                $factura->referencia = $request->factura['referencia'];
                $factura->save();
            }

            $pedido = new Pedido();
            $pedido->tipo_de_pago = $request->tipo_de_pago;
            $pedido->estado = ($request->tipo_de_pago === 'TARJETA') ? 'PROCESANDO' : 'PENDIENTE';
            $pedido->precio = 0;
            $pedido->cliente_id = $cliente->id;
            $pedido->cupon_id = $request->cupon_id;
            $pedido->factura_id = $request->factura ? $factura->id : null;
            foreach ($request->pedido_detalle as $pd) {
                $producto = Producto::find($pd['producto_id']);
                $pedido->precio = $pedido->precio + ($producto['precio'] * $pd['cantidad']);
            }
            $pedido->save();

            $envio = new Envio();
            $envio->remitente_nombres = $request->envio['remitente_nombres'];
            $envio->remitente_email = $request->envio['remitente_email'];
            $envio->remitente_telefono = $request->envio['remitente_telefono'];
            $envio->entrega_direccion = $request->envio['direccion'];
            $envio->entrega_distrito = $request->envio['distrito'];
            $envio->entrega_referencia = $request->envio['referencia'];
            $envio->pedido_id = $pedido->id;
            $envio->save();

            foreach ($request->pedido_detalle as $pd) {
                $producto = Producto::find($pd['producto_id']);

                $pedido_detalle = new PedidoDetalle();
                $pedido_detalle->precio_unitario = $producto->precio;
                $pedido_detalle->cantidad = $pd['cantidad'];
                $pedido_detalle->total = $pd['cantidad'] * $producto->precio;
                $pedido_detalle->pedido_id = $pedido->id;
                $pedido_detalle->producto_id = $producto->id;

                if ($pd['tipo'] === "GIFTCARD") {
                    $pedido_detalle->is_giftcard = true;
                    $pedido_detalle->mailing_owner_address = isset($pd['mailing_owner_address']) ? $pd['mailing_owner_address'] : null;
                    $pedido_detalle->mailign_owner_name = isset($pd['mailign_owner_name']) ? $pd['mailign_owner_name'] : null;
                }
                $pedido_detalle->save();
            }

            return $pedido;
        });

        $pedidoForMailing = Pedido::with([
            'cliente',
            'factura',
            'detalles'
        ])->find($pedido->id);

//        return view('email.pedidonuevo')->with(['pedido' => $pedidoForMailing, 'envio' => $request->envio]);

        if ($request->tipo_de_pago !== 'TARJETA') {
            Mail::send(new PedidoNuevoMailing($pedidoForMailing, $request->envio));
        }

        return response($pedidoForMailing, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Pedido  $pedido
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pedido = Pedido::with([
            'cliente',
            'cupon',
            'factura',
            'detalles' => function ($query) {
                $query->with(['giftcard','suscripcion']);
            }
        ])->find($id);

        return response($pedido, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Pedido  $pedido
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pedido $pedido)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Pedido  $pedido
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pedido $pedido)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Pedido  $pedido
     * @return \Illuminate\Http\Response
     */
    public function confirm(Request $request, Pedido $pedido)
    {
        if ($pedido->estado === 'CONFIRMADA' || $pedido->estado === 'CANCELADA')
            return response(['error' => "El pedido ya ha sido '{$pedido['estado']}'"], 409);

        $pedido_detalles = PedidoDetalle::where('pedido_id', $pedido->id)->get();
        $envio = Envio::where('pedido_id', $pedido->id)->firstOrFail();

        DB::transaction(function () use ($pedido, $pedido_detalles, $envio) {
            $pedido->estado = 'CONFIRMADA';
            $pedido->save();

            foreach ($pedido_detalles as $pd) {
                if ($pd['is_giftcard']) {
                    $giftcard = new Giftcard();
                    $giftcard->estado = 'DISPONIBLE';
                    $giftcard->codigo = $this->random(10);
                    $giftcard->mailing_owner_address = $pd['mailing_owner_address'];
                    $giftcard->mailign_owner_name = $pd['mailign_owner_name'];
                    $giftcard->remitente_nombres = $envio->remitente_nombres;
                    $giftcard->remitente_email = $envio->remitente_email;
                    $giftcard->remitente_telefono = $envio->remitente_telefono;
                    $giftcard->entrega_direccion = $envio->entrega_direccion;
                    $giftcard->entrega_distrito = $envio->entrega_distrito;
                    $giftcard->entrega_referencia = $envio->entrega_referencia;
                    $giftcard->pedido_detalle_id = $pd->id;
                    $giftcard->save();

                    if ($giftcard->mailing_owner_address)
                        Mail::send(new GiftcardMailing($giftcard));

                }
                else {
                    $suscripcion = new Suscripcion();
                    $suscripcion->estado = "ACTIVO";
                    $suscripcion->fecha_de_inicio = date("Y-m-d H:i:s");
                    $suscripcion->nombres = $envio->remitente_nombres;
                    $suscripcion->email = $envio->remitente_email;
                    $suscripcion->celular = $envio->remitente_telefono;
                    $suscripcion->direccion = $envio->entrega_direccion;
                    $suscripcion->distrito = $envio->entrega_distrito;
                    $suscripcion->referencia = $envio->entrega_referencia;
                    $suscripcion->pedido_detalle_id = $pd->id;
                    $suscripcion->save();
                }
            };
        });

        if ($pedido->tipo_de_pago === 'TARJETA') {
            $pedidoForMailing = Pedido::with([
                'cliente',
                'factura',
                'detalles'
            ])->find($pedido->id);

            Mail::send(new PedidoNuevoMailing($pedidoForMailing, $envio));
        }

        return response([
            'message' => "Se ha confirmado el pedido.",
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Pedido  $pedido
     * @return \Illuminate\Http\Response
     */
    public function cancel(Request $request, Pedido $pedido)
    {
        if ($pedido->estado === 'CONFIRMADA' || $pedido->estado === 'CANCELADA')
            return response(['error' => "El pedido ya ha sido '{$pedido['estado']}'"], 409);

        $pedido->estado = 'CANCELADA';
        $pedido->save();

        return response([
            'message' => "Se ha cancelado el pedido.",
        ], 200);
    }

    /**
     * Generate a more truly "random" alpha-numeric string.
     *
     * @param  int  $length
     * @return string
     *
     * @throws \RuntimeException
     */
    public static function random($length = 16)
    {
        if ( ! function_exists('openssl_random_pseudo_bytes'))
        {
            throw new RuntimeException('OpenSSL extension is required.');
        }

        $bytes = openssl_random_pseudo_bytes($length * 2);

        if ($bytes === false)
        {
            throw new RuntimeException('Unable to generate random string.');
        }

        return substr(str_replace(array('/', '+', '='), '', base64_encode($bytes)), 0, $length);
    }

    public function payuConfirmation()
    {
        $this->storePayuConfirmation();

        if ($_POST['state_pol'] === "6")
            return response("Transaction (Declinada) almacenada en base de datos", 200);

        if ($_POST['state_pol'] === "5")
            return response("Transaction (Expirada) almacenada en base de datos", 200);

        $pedido = Pedido::findOrFail($_POST["reference_sale"]);
        $pedido_detalles = PedidoDetalle::where('pedido_id', $pedido->id)->get();
        $envio = Envio::where('pedido_id', $pedido->id)->firstOrFail();

        DB::transaction(function () use ($pedido, $pedido_detalles, $envio) {
            $pedido->estado = 'CONFIRMADA';
            $pedido->save();

            foreach ($pedido_detalles as $pd) {
                if ($pd['is_giftcard']) {
                    $giftcard = new Giftcard();
                    $giftcard->estado = 'DISPONIBLE';
                    $giftcard->codigo = $this->random(10);
                    $giftcard->mailing_owner_address = $pd['mailing_owner_address'];
                    $giftcard->mailign_owner_name = $pd['mailign_owner_name'];
                    $giftcard->remitente_nombres = $envio->remitente_nombres;
                    $giftcard->remitente_email = $envio->remitente_email;
                    $giftcard->remitente_telefono = $envio->remitente_telefono;
                    $giftcard->entrega_direccion = $envio->entrega_direccion;
                    $giftcard->entrega_distrito = $envio->entrega_distrito;
                    $giftcard->entrega_referencia = $envio->entrega_referencia;
                    $giftcard->pedido_detalle_id = $pd->id;
                    $giftcard->save();

                    if ($giftcard->mailing_owner_address)
                        Mail::send(new GiftcardMailing($giftcard));

                }
                else {
                    $suscripcion = new Suscripcion();
                    $suscripcion->estado = "ACTIVO";
                    $suscripcion->fecha_de_inicio = date("Y-m-d H:i:s");
                    $suscripcion->nombres = $envio->remitente_nombres;
                    $suscripcion->email = $envio->remitente_email;
                    $suscripcion->celular = $envio->remitente_telefono;
                    $suscripcion->direccion = $envio->entrega_direccion;
                    $suscripcion->distrito = $envio->entrega_distrito;
                    $suscripcion->referencia = $envio->entrega_referencia;
                    $suscripcion->pedido_detalle_id = $pd->id;
                    $suscripcion->save();
                }
            };
        });

        $pedidoForMailing = Pedido::with([
            'cliente',
            'factura',
            'detalles'
        ])->find($pedido->id);

        Mail::send(new PedidoNuevoMailing($pedidoForMailing, $envio));

        return response("Transaction (Aprobada) almacenada en base de datos", 200);
    }

    private function storePayuConfirmation(){
        Log::info('New post request from PayU');
        Log::info($_POST);

        $payu_confirmation = new PayuConfirmacion();
        $payu_confirmation->response_code_pol = !empty($_POST['response_code_pol']) ? $_POST['response_code_pol'] : null;
        $payu_confirmation->phone = !empty($_POST['phone']) ? $_POST['phone'] : null;
        $payu_confirmation->additional_value = !empty($_POST['additional_value']) ? $_POST['additional_value'] : null;
        $payu_confirmation->test = !empty($_POST['test']) ? $_POST['test'] : null;
        $payu_confirmation->transaction_date = !empty($_POST['transaction_date']) ? $_POST['transaction_date'] : null;
        $payu_confirmation->cc_number = !empty($_POST['cc_number']) ? $_POST['cc_number'] : null;
        $payu_confirmation->cc_holder = !empty($_POST['cc_holder']) ? $_POST['cc_holder'] : null;
        $payu_confirmation->error_code_bank = !empty($_POST['error_code_bank']) ? $_POST['error_code_bank'] : null;
        $payu_confirmation->billing_country = !empty($_POST['billing_country']) ? $_POST['billing_country'] : null;
        $payu_confirmation->bank_referenced_name = !empty($_POST['bank_referenced_name']) ? $_POST['bank_referenced_name'] : null;
        $payu_confirmation->description = !empty($_POST['description']) ? $_POST['description'] : null;
        $payu_confirmation->administrative_fee_tax = !empty($_POST['administrative_fee_tax']) ? $_POST['administrative_fee_tax'] : null;
        $payu_confirmation->value = !empty($_POST['value']) ? $_POST['value'] : null;
        $payu_confirmation->administrative_fee = !empty($_POST['administrative_fee']) ? $_POST['administrative_fee'] : null;
        $payu_confirmation->payment_method_type = !empty($_POST['payment_method_type']) ? $_POST['payment_method_type'] : null;
        $payu_confirmation->office_phone = !empty($_POST['office_phone']) ? $_POST['office_phone'] : null;
        $payu_confirmation->email_buyer = !empty($_POST['email_buyer']) ? $_POST['email_buyer'] : null;
        $payu_confirmation->response_message_pol = !empty($_POST['response_message_pol']) ? $_POST['response_message_pol'] : null;
        $payu_confirmation->error_message_bank = !empty($_POST['error_message_bank']) ? $_POST['error_message_bank'] : null;
        $payu_confirmation->shipping_city = !empty($_POST['shipping_city']) ? $_POST['shipping_city'] : null;
        $payu_confirmation->transaction_id = !empty($_POST['transaction_id']) ? $_POST['transaction_id'] : null;
        $payu_confirmation->sign = !empty($_POST['sign']) ? $_POST['sign'] : null;
        $payu_confirmation->tax = !empty($_POST['tax']) ? $_POST['tax'] : null;
        $payu_confirmation->payment_method = !empty($_POST['payment_method']) ? $_POST['payment_method'] : null;
        $payu_confirmation->billing_address = !empty($_POST['billing_address']) ? $_POST['billing_address'] : null;
        $payu_confirmation->payment_method_name = !empty($_POST['payment_method_name']) ? $_POST['payment_method_name'] : null;
        $payu_confirmation->pse_bank = !empty($_POST['pse_bank']) ? $_POST['pse_bank'] : null;
        $payu_confirmation->state_pol = !empty($_POST['state_pol']) ? $_POST['state_pol'] : null;
        $payu_confirmation->date = !empty($_POST['date']) ? $_POST['date'] : null;
        $payu_confirmation->nickname_buyer = !empty($_POST['nickname_buyer']) ? $_POST['nickname_buyer'] : null;
        $payu_confirmation->reference_pol = !empty($_POST['reference_pol']) ? $_POST['reference_pol'] : null;
        $payu_confirmation->currency = !empty($_POST['currency']) ? $_POST['currency'] : null;
        $payu_confirmation->risk = !empty($_POST['risk']) ? $_POST['risk'] : null;
        $payu_confirmation->shipping_address = !empty($_POST['shipping_address']) ? $_POST['shipping_address'] : null;
        $payu_confirmation->bank_id = !empty($_POST['bank_id']) ? $_POST['bank_id'] : null;
        $payu_confirmation->payment_request_state = !empty($_POST['payment_request_state']) ? $_POST['payment_request_state'] : null;
        $payu_confirmation->customer_number = !empty($_POST['customer_number']) ? $_POST['customer_number'] : null;
        $payu_confirmation->administrative_fee_base = !empty($_POST['administrative_fee_base']) ? $_POST['administrative_fee_base'] : null;
        $payu_confirmation->attempts = !empty($_POST['attempts']) ? $_POST['attempts'] : null;
        $payu_confirmation->merchant_id = !empty($_POST['merchant_id']) ? $_POST['merchant_id'] : null;
        $payu_confirmation->exchange_rate = !empty($_POST['exchange_rate']) ? $_POST['exchange_rate'] : null;
        $payu_confirmation->shipping_country = !empty($_POST['shipping_country']) ? $_POST['shipping_country'] : null;
        $payu_confirmation->installments_number = !empty($_POST['installments_number']) ? $_POST['installments_number'] : null;
        $payu_confirmation->franchise = !empty($_POST['franchise']) ? $_POST['franchise'] : null;
        $payu_confirmation->payment_method_id = !empty($_POST['payment_method_id']) ? $_POST['payment_method_id'] : null;
        $payu_confirmation->extra1 = !empty($_POST['extra1']) ? $_POST['extra1'] : null;
        $payu_confirmation->extra2 = !empty($_POST['extra2']) ? $_POST['extra2'] : null;
        $payu_confirmation->antifraudMerchantId = !empty($_POST['antifraudMerchantId']) ? $_POST['antifraudMerchantId'] : null;
        $payu_confirmation->extra3 = !empty($_POST['extra3']) ? $_POST['extra3'] : null;
        $payu_confirmation->nickname_seller = !empty($_POST['nickname_seller']) ? $_POST['nickname_seller'] : null;
        $payu_confirmation->ip = !empty($_POST['ip']) ? $_POST['ip'] : null;
        $payu_confirmation->airline_code = !empty($_POST['airline_code']) ? $_POST['airline_code'] : null;
        $payu_confirmation->billing_city = !empty($_POST['billing_city']) ? $_POST['billing_city'] : null;
        $payu_confirmation->pse_reference1 = !empty($_POST['pse_reference1']) ? $_POST['pse_reference1'] : null;
        $payu_confirmation->reference_sale = !empty($_POST['reference_sale']) ? $_POST['reference_sale'] : null;
        $payu_confirmation->pse_reference3 = !empty($_POST['pse_reference3']) ? $_POST['pse_reference3'] : null;
        $payu_confirmation->pse_reference2 = !empty($_POST['pse_reference2']) ? $_POST['pse_reference2'] : null;

        // this ones just apear on success (APPROVED)
        $payu_confirmation->transaction_bank_id = !empty($_POST['transaction_bank_id']) ? $_POST['transaction_bank_id'] : null;
        $payu_confirmation->commision_pol_currency = !empty($_POST['commision_pol_currency']) ? $_POST['commision_pol_currency'] : null;
        $payu_confirmation->commision_pol = !empty($_POST['commision_pol']) ? $_POST['commision_pol'] : null;
        $payu_confirmation->cus = !empty($_POST['cus']) ? $_POST['cus'] : null;
        $payu_confirmation->authorization_code = !empty($_POST['authorization_code']) ? $_POST['authorization_code'] : null;

        $payu_confirmation->pedido_id = !empty($_POST['reference_sale']) ? $_POST['reference_sale'] : null;
        $payu_confirmation->save();
    }
}
