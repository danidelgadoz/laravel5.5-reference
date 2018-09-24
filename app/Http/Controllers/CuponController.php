<?php

namespace App\Http\Controllers;

use App\Cupon;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CuponController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cupon = Cupon::orderByDesc("id")->get();
        return response($cupon, 200);
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
        $cupon->descripcion = $request->descripcion;
        $cupon->habilitado = $request->habilitado;
        $cupon->cantidad_disponible = $request->cantidad_disponible;
        $cupon->cantidad_canjeados = 0;
        $cupon->fecha_inicio = $request->fecha_inicio;
        $cupon->fecha_fin = $request->fecha_fin;
        $cupon->descuento = $request->descuento;
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Cupon  $cupon
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cupon $cupon)
    {
        $cupon->descripcion = $request->descripcion;
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

    public function validar(Request $request)
    {
        $cupon_valido = Cupon::where('codigo', $request->codigo)
            ->whereRaw('cantidad_canjeados < cantidad_disponible')
            ->where('habilitado', true)
            ->where('fecha_inicio', '<', Carbon::now())
            ->where('fecha_fin', '>', Carbon::now())
            ->first();

        if (!$cupon_valido) {
            return response(['message' => "Cupon inválido"], 409);
        }

        return response(['message' => "Cupon válido."], 200);

    }
}
