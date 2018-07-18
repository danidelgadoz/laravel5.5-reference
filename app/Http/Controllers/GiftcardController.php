<?php

namespace App\Http\Controllers;

use App\Giftcard;
use App\PedidoDetalle;
use App\Pedido;
use App\Suscripcion;
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
//        $giftcards = Giftcard::whereHas('pedido_detalle', function ($query) {
//            $query->whereHas('pedido', function ($query) {
//                $query->where('estado', 'CONFIRMADA');
//            });
//        })->orderBy('id', 'desc')
//            ->get();

        $giftcards = Giftcard::orderByDesc("id")->get();

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
        $giftcards = Giftcard::whereHas('pedido_detalle_id', function ($query) use ($id) {
            $query->where('pedido_id', $id);
        })->get();

        return response($giftcards, 200);
    }

    public function validar(Request $request)
    {
        $giftcard = Giftcard::where('codigo', $request->codigo)->firstOrFail();
        $pedido_detalle = PedidoDetalle::find($giftcard->pedido_detalle_id);
        $pedido = Pedido::find($pedido_detalle->pedido_id, ['estado']);

        if ($giftcard->estado === 'CANJEADO')
            return response(['error' => "Giftcard anteriormente canjeado."], 409);

        if ($pedido->estado === 'PENDIENTE')
            return response(['error' => "Pedido del giftcard tiene el pago pendiente"], 409);

        if ($pedido->estado === 'CANCELADA')
            return response(['error' => "Pedido del giftcard ya ha sido cancelado"], 409);

        return response(['message' => "Giftcard disponible."], 200);

    }

    public function canjear(Request $request)
    {
        $giftcard = Giftcard::where('codigo', $request->codigo)->firstOrFail();
        $pedido_detalle = PedidoDetalle::find($giftcard->pedido_detalle_id);
        $pedido = Pedido::find($pedido_detalle->pedido_id, ['estado']);

        if ($giftcard->estado === 'CANJEADO')
            return response(['error' => "Giftcard anteriormente canjeado."], 409);

        if ($pedido->estado === 'PENDIENTE')
            return response(['error' => "Pedido del giftcard tiene el pago pendiente"], 409);

        if ($pedido->estado === 'CANCELADA')
            return response(['error' => "Pedido del giftcard ya ha sido cancelado"], 409);

        $suscripcion = DB::transaction(function () use ($request, $pedido_detalle, $giftcard) {
            $suscripcion = new Suscripcion();
            $suscripcion->meses = $giftcard["meses"];
            $suscripcion->fecha_de_inicio = date("Y-m-d H:i:s");
            $suscripcion->direccion = $request->envio['direccion'];
            $suscripcion->distrito = $request->envio['distrito'];
            $suscripcion->referencia = $request->envio['referencia'];
            $suscripcion->nombres = $request->envio['remitente_nombres'];
            $suscripcion->email = $request->envio['remitente_email'];
            $suscripcion->celular = $request->envio['remitente_telefono'];
            $suscripcion->pedido_detalle_id = $pedido_detalle->id;
            $suscripcion->save();

            $giftcard->estado = 'CANJEADO';
            $giftcard->save();

            return $suscripcion;
        });

        return response([
            'message' => "Giftcard canjeado con éxito, su suscripcion ha iniciado.",
            'data' => Suscripcion::find($suscripcion->id)
        ], 200);

    }
}
