<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\SuscripcionPagada;
use App\Delivery;
use Illuminate\Http\Request;

class SuscripcionPagadaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $suscripciones_pagadas = SuscripcionPagada::all();
        return response($suscripciones_pagadas, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SuscripcionPagada  $suscripcion_pagada
     * @return \Illuminate\Http\Response
     */
    public function show(SuscripcionPagada $suscripcion_pagada)
    {
        return response($suscripcion_pagada, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SuscripcionPagada  $suscripcion_pagada
     * @return \Illuminate\Http\Response
     */
    public function edit(SuscripcionPagada $suscripcion_pagada)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SuscripcionPagada  $suscipcion_pagada
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SuscripcionPagada $suscripcion_pagada)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SuscripcionPagada  $suscripcion_pagada
     * @return \Illuminate\Http\Response
     */
    public function destroy(SuscripcionPagada $suscripcion_pagada)
    {
        //
    }

    public function validarGiftcard(Request $request)
    {
        $giftcard = SuscripcionPagada::where('giftcard_codigo', $request->codigo)
                    ->firstOrFail();

        if ($giftcard->fecha_de_inicio)
            return response(['error' => "Giftcard anteriormente canjeado."], 409);
        else
            return response(['message' => "Giftcard disponible."], 200);

    }

    public function canjearGiftcard(Request $request)
    {
        $giftcard = SuscripcionPagada::where('giftcard_codigo', $request->codigo)->firstOrFail();

        if ($giftcard->canjeado) {
            return response(['error' => "SuscripcionPagada anteriormente canjeado."], 409);

        } else {
            DB::transaction(function () use ($request, $giftcard) {
                $delivery = new Delivery();
                $delivery->direccion = $request->entrega_direccion;
                $delivery->distrito = $request->entrega_distrito;
                $delivery->referencia = $request->entrega_referencia;
                $delivery->nombres = $request->entrega_remitente;
                $delivery->email = $request->entrega_email;
                $delivery->celular = $request->entrega_celular;
                $delivery->save();

                $giftcard->delivery_id = $delivery->id;
                $giftcard->fecha_de_inicio = date("Y-m-d H:i:s");
                $giftcard->save();
            });

            return response([
                'message' => "SuscripcionPagada canjeado con éxito.",
                'data' => SuscripcionPagada::find($giftcard->id, [
                    'id',
                    'giftcard_codigo',
                    'fecha_de_inicio',
                    'meses',
                    'precio',
                    'delivery_id',
                    'plan_id'
                ])
            ], 200);
        }
    }
}
