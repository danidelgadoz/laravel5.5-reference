<?php

namespace App\Http\Controllers;

use App\Giftcard;
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
        $giftcards = Giftcard::all();
        return response($giftcards, 200);
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
     * @param  \App\Giftcard  $giftcard
     * @return \Illuminate\Http\Response
     */
    public function show(Giftcard $giftcard)
    {
        return response($giftcard, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Giftcard  $giftcard
     * @return \Illuminate\Http\Response
     */
    public function edit(Giftcard $giftcard)
    {
        //
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Giftcard  $giftcard
     * @return \Illuminate\Http\Response
     */
    public function destroy(Giftcard $giftcard)
    {
        //
    }

    public function validar(Request $request)
    {
        $giftcard = Giftcard::where('codigo', $request->codigo)
//                    ->where('canjeado', null)
                    ->firstOrFail();

        if ($giftcard->canjeado)
            return response(['error' => "Giftcard anteriormente canjeado."], 409);
        else
            return response(['message' => "Giftcard disponible."], 200);

    }

    public function canjear(Request $request)
    {
        $giftcard = Giftcard::where('codigo', $request->codigo)->firstOrFail();

        if ($giftcard->canjeado) {
            return response(['error' => "Giftcard anteriormente canjeado."], 409);

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
                $giftcard->canjeado = date("Y-m-d H:i:s");
                $giftcard->save();
            });

            return response([
                'message' => "Giftcard canjeado con éxito.",
                'data' => Giftcard::find($giftcard->id, [
                    'id',
                    'codigo',
                    'canjeado',
                    'meses',
                    'precio',
                    'delivery_id',
                    'plan_id'
                ])
            ], 200);
        }
    }
}
