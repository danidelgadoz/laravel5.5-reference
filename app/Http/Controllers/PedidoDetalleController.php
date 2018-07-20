<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\PedidoDetalle;
use Illuminate\Http\Request;

class PedidoDetalleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pedidos_detalles = PedidoDetalle::with(['pedido'])
            ->orderBy('id', 'desc')
            ->get();

        return response($pedidos_detalles, 200);
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
     * @param  \App\PedidoDetalle  $pedido_detalle
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pedido_detalle = PedidoDetalle::with([
            'pedido',
        ])->find($id);

        return response($pedido_detalle, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PedidoDetalle  $suscipcion_pagada
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PedidoDetalle $pedido_detalle)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PedidoDetalle  $pedido_detalle
     * @return \Illuminate\Http\Response
     */
    public function destroy(PedidoDetalle $pedido_detalle)
    {
        //
    }
}
