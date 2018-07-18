<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Pedido;
use App\Cliente;
use App\Plan;
use App\SuscripcionPagada;
use App\Giftcard;
use App\Factura;
use App\Delivery;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pedidos = Pedido::all();
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
        if ($request->tipo_de_pago === 'TARJETA')
            return response(['error' => "Pago por tarjeta crédito/débito no disponible"], 409);

        $pedido = DB::transaction(function () use ($request) {
            $cliente = Cliente::where('email', $request->cliente['email'])->first();
            $plan = Plan::where('default', true)->first();

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
            $pedido->estado = 'PENDIENTE';
            $pedido->precio = 0;
            $pedido->cliente_id = $cliente->id;
            $pedido->cupon_id = $request->cupon_id;
            $pedido->factura_id = $request->factura ? $factura->id : null;
            foreach ($request->productos as $suscripcion) {
                $pedido->precio = $pedido->precio + ($suscripcion['meses'] * $plan->precio);
            }
            $pedido->save();

            foreach ($request->productos as $suscripcion) {
                $suscripcion_pagada = new SuscripcionPagada();
                $suscripcion_pagada->meses = $suscripcion["meses"];
                $suscripcion_pagada->precio = $plan->precio;
                $suscripcion_pagada->pedido_id = $pedido->id;
                $suscripcion_pagada->plan_id = $plan->id;

                if ($suscripcion['tipo'] === "GIFTCARD") {
                    // GIFTCARD
                    $suscripcion_pagada->fecha_de_inicio = null;
                    $suscripcion_pagada->save();

                    $giftcard = new Giftcard();
                    $giftcard->estado = 'DISPONIBLE';
                    $giftcard->codigo = $this->random(10);
                    $giftcard->remitente_nombres = $request->envio['remitente_nombres'];
                    $giftcard->remitente_email = $request->envio['remitente_email'];
                    $giftcard->remitente_telefono = $request->envio['remitente_telefono'];
                    $giftcard->entrega_direccion = $request->envio['direccion'];
                    $giftcard->entrega_distrito = $request->envio['distrito'];
                    $giftcard->entrega_referencia = $request->envio['referencia'];
                    $giftcard->suscripcion_pagada_id = $suscripcion_pagada->id;
                    $giftcard->save();

                } else {
                    // SUSCRIPCION
                    $delivery = new Delivery();
                    $delivery->nombres = $request->envio['remitente_nombres'];
                    $delivery->email = $request->envio['remitente_email'];
                    $delivery->celular = $request->envio['remitente_telefono'];
                    $delivery->direccion = $request->envio['remitente_nombres'];
                    $delivery->distrito = $request->envio['remitente_nombres'];
                    $delivery->referencia = $request->envio['remitente_nombres'];
                    $delivery->save();

                    $suscripcion_pagada->fecha_de_inicio = date("Y-m-d H:i:s");
                    $suscripcion_pagada->delivery_id = $delivery->id;
                    $suscripcion_pagada->save();
                }
            }

            return $pedido;
        });

        return response([
            'id' => $pedido->id,
            'message' => "Registro de pedido exitoso.",
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Pedido  $pedido
     * @return \Illuminate\Http\Response
     */
    public function show(Pedido $pedido)
    {
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
        if ($pedido->estado !== 'PENDIENTE')
            return response(['error' => "El pedido ya ha sido '{$pedido['estado']}'"], 409);

        $pedido_actualizado = DB::transaction(function () use ($pedido) {
//            SuscripcionPagada::where('pedido_id', $pedido->id)
//                ->update([
//                    'fecha_de_inicio' => date("Y-m-d H:i:s")
//                ]);

            $pedido->estado = 'CONFIRMADA';
            $pedido->save();

            return $pedido;
        });

        return response([
            'message' => "Se ha actualizado el estado del pedido a '{$pedido_actualizado['estado']}'",
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
        if ($pedido->estado !== 'PENDIENTE')
            return response(['error' => "El pedido ya ha sido '{$pedido['estado']}'"], 409);

        $pedido->estado = 'CANCELADA';
        $pedido->save();
        return response($pedido, 200);
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
}
