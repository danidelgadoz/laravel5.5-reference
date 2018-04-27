<?php

namespace App\Http\Controllers;

use App\Cupon;
use Illuminate\Http\Request;

class CuponController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cupon = Cupon::all();
        return response($cupon, 200);
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
        $cupon = new Cupon;
        $cupon->codigo = $request->codigo;
        $cupon->habilitado = $request->habilitado;
        $cupon->cantidad_disponible = $request->cantidad_disponible;
        $cupon->cantidad_canjeados = $request->cantidad_canjeados;
        $cupon->fecha_inicio = $request->fecha_inicio;
        $cupon->fecha_fin = $request->fecha_fin;
        $cupon->plan_id = $request->plan_id;
        $cupon->save();

        return response($cupon, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Cupon  $cupon
     * @return \Illuminate\Http\Response
     */
    public function show(Cupon $cupon)
    {
        return response($cupon, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Cupon  $cupon
     * @return \Illuminate\Http\Response
     */
    public function edit(Cupon $cupon)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Cupon  $cupon
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cupon $cupon)
    {
        $cupon->habilitado = $request->habilitado;
        $cupon->cantidad_disponible = $request->cantidad_disponible;
        $cupon->fecha_inicio = $request->fecha_inicio;
        $cupon->fecha_fin = $request->fecha_fin;
        $cupon->save();
        return response($cupon, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Cupon  $cupon
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cupon $cupon)
    {
        $cupon->delete();
        return response([
            'id'=> $cupon->id,
            'deleted'=> true,
            'message' => "Se eliminó el cupon con ID {$cupon->id} con exitosamente."
        ], 200);
    }
}
