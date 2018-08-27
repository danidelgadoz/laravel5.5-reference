<?php

namespace App\Http\Controllers;

use App\PayuConfirmacion;
use Illuminate\Http\Request;

class PayuConfirmacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $payuConfirmacion = PayuConfirmacion::orderByDesc("id")->get();

        return response($payuConfirmacion, 200);
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
     * @param  \App\PayuConfirmacion  $payuConfirmacion
     * @return \Illuminate\Http\Response
     */
    public function show(PayuConfirmacion $payuConfirmacion)
    {
        return response($payuConfirmacion, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PayuConfirmacion  $payuConfirmacion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PayuConfirmacion $payuConfirmacion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PayuConfirmacion  $payuConfirmacion
     * @return \Illuminate\Http\Response
     */
    public function destroy(PayuConfirmacion $payuConfirmacion)
    {
        //
    }


    public function getByPedido($id)
    {
        $payuConfirmacion = PayuConfirmacion::where('pedido_id', $id)->orderByDesc("id")->get();

        return response($payuConfirmacion, 200);
    }
}
