<?php

namespace App\Http\Controllers;

use App\Giftcard;
use App\SuscripcionPagada;
use App\Pedido;
use App\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GiftcardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $giftcards = Giftcard::whereHas('suscripcion_pagada', function ($query) {
            $query->whereHas('pedido', function ($query) {
                $query->where('estado', 'CONFIRMADA');
            });
        })->get();

        return response($giftcards, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Giftcard  $giftcard
     * @return \Illuminate\Http\Response
     */
    public function show(Giftcard $giftcard)
    {
        return response($giftcard, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Giftcard  $giftcard
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Giftcard $giftcard)
    {
        //
    }

    public function getByPedido($id)
    {
        $giftcards = Giftcard::whereHas('suscripcion_pagada', function ($query) use ($id) {
            $query->where('pedido_id', $id);
        })->get();

        return response($giftcards, 200);
    }

    public function validar(Request $request)
    {
        $giftcard = Giftcard::where('codigo', $request->codigo)->firstOrFail();
        $suscripcion = SuscripcionPagada::find($giftcard->suscripcion_pagada_id);
        $pedido = Pedido::find($suscripcion->pedido_id, ['estado']);

        if ($suscripcion->fecha_de_inicio)
            return response(['error' => "Giftcard anteriormente canjeado."], 409);

        if ($pedido->estado === 'PENDIENTE')
            return response(['error' => "Compra de giftcard tiene el pago pendiente"], 409);

        if ($pedido->estado === 'CANCELADA')
            return response(['error' => "Compra de giftcard ha sido cancelada"], 409);

        return response(['message' => "Giftcard disponible."], 200);

    }

    public function canjear(Request $request)
    {
        $giftcard = Giftcard::where('codigo', $request->codigo)->firstOrFail();
        $suscripcion = SuscripcionPagada::find($giftcard->suscripcion_pagada_id);
        $pedido = Pedido::find($suscripcion->pedido_id, ['estado']);

        if ($suscripcion->fecha_de_inicio)
            return response(['error' => "Giftcard anteriormente canjeado."], 409);

        if ($pedido->estado === 'PENDIENTE')
            return response(['error' => "Compra de giftcard tiene el pago pendiente"], 409);

        if ($pedido->estado === 'CANCELADA')
            return response(['error' => "Compra de giftcard ha sido cancelada"], 409);

        DB::transaction(function () use ($request, $suscripcion, $giftcard) {
            $delivery = new Delivery();
            $delivery->direccion = $request->envio['direccion'];
            $delivery->distrito = $request->envio['distrito'];
            $delivery->referencia = $request->envio['referencia'];
            $delivery->nombres = $request->envio['remitente_nombres'];
            $delivery->email = $request->envio['remitente_email'];
            $delivery->celular = $request->envio['remitente_telefono'];
            $delivery->save();

            $suscripcion->delivery_id = $delivery->id;
            $suscripcion->fecha_de_inicio = date("Y-m-d H:i:s");
            $suscripcion->save();

            $giftcard->estado = 'CANJEADO';
            $giftcard->save();
        });

        return response([
            'message' => "SuscripcionPagada canjeado con éxito.",
            'data' => SuscripcionPagada::find($giftcard->id, [
                'id',
                'fecha_de_inicio',
                'meses',
                'precio',
                'delivery_id',
                'plan_id'
            ])
        ], 200);

    }
}
