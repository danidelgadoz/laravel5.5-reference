<?php

namespace App\Http\Controllers;

use App\Giftcard;
use App\SuscripcionPagada;
use App\Compra;
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
        $giftcard = Giftcard::all();
        return response($giftcard, 200);
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


    public function validar(Request $request)
    {
        $giftcard = Giftcard::where('codigo', $request->codigo)->firstOrFail();
        $suscripcion = SuscripcionPagada::find($giftcard->suscripcion_pagada_id);
        $compra = Compra::find($suscripcion->compra_id, ['estado']);

        if ($suscripcion->fecha_de_inicio)
            return response(['error' => "Giftcard anteriormente canjeado."], 409);

        if ($compra->estado === 'SOLICITADA')
            return response(['error' => "Compra de giftcard tiene el pago pendiente"], 409);

        if ($compra->estado === 'CANCELADA')
            return response(['error' => "Compra de giftcard ha sido cancelada"], 409);

        return response(['message' => "Giftcard disponible."], 200);

    }

    public function canjear(Request $request)
    {
        $giftcard = Giftcard::where('codigo', $request->codigo)->firstOrFail();
        $suscripcion = SuscripcionPagada::find($giftcard->suscripcion_pagada_id);
        $compra = Compra::find($suscripcion->compra_id, ['estado']);

        if ($suscripcion->fecha_de_inicio)
            return response(['error' => "Giftcard anteriormente canjeado."], 409);

        if ($compra->estado === 'SOLICITADA')
            return response(['error' => "Compra de giftcard tiene el pago pendiente"], 409);

        if ($compra->estado === 'CANCELADA')
            return response(['error' => "Compra de giftcard ha sido cancelada"], 409);

        DB::transaction(function () use ($request, $suscripcion, $giftcard) {
            $delivery = new Delivery();
            $delivery->direccion = $request->entrega_direccion;
            $delivery->distrito = $request->entrega_distrito;
            $delivery->referencia = $request->entrega_referencia;
            $delivery->nombres = $request->entrega_remitente;
            $delivery->email = $request->entrega_email;
            $delivery->celular = $request->entrega_celular;
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
