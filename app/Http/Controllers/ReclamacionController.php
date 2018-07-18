<?php

namespace App\Http\Controllers;

use App\Reclamacion;
use Illuminate\Http\Request;

class ReclamacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $reclamaciones = Reclamacion::orderByDesc("id")->get();
        return response($reclamaciones, 200);
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
        $reclamacion = new Reclamacion;
        $reclamacion->cliente_nombres = $request->cliente_nombres;
        $reclamacion->cliente_numero_documento = $request->cliente_numero_documento;
        $reclamacion->cliente_telefono1 = $request->cliente_telefono1;
        $reclamacion->cliente_telefono2 = $request->cliente_telefono2;
        $reclamacion->cliente_email = $request->cliente_email;
        $reclamacion->reclamacion_tipo = $request->reclamacion_tipo;
        $reclamacion->reclamacion_relacion = $request->reclamacion_relacion;
        $reclamacion->reclamacion_descripcion1 = $request->reclamacion_descripcion1;
        $reclamacion->reclamacion_descripcion2 = $request->reclamacion_descripcion2;
        $reclamacion->reclamacion_pedido = $request->reclamacion_pedido;
        $reclamacion->save();

        return response($reclamacion, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Reclamacion  $reclamacion
     * @return \Illuminate\Http\Response
     */
    public function show(Reclamacion $reclamacion)
    {
        return response($reclamacion, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Reclamacion  $reclamacion
     * @return \Illuminate\Http\Response
     */
    public function edit(Reclamacion $reclamacion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Reclamacion  $reclamacion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Reclamacion $reclamacion)
    {
        $reclamacion->cliente_nombres = $request->cliente_nombres;
        $reclamacion->cliente_numero_documento = $request->cliente_numero_documento;
        $reclamacion->cliente_telefono1 = $request->cliente_telefono1;
        $reclamacion->cliente_telefono2 = $request->cliente_telefono2;
        $reclamacion->cliente_email = $request->cliente_email;
        $reclamacion->reclamacion_tipo = $request->reclamacion_tipo;
        $reclamacion->reclamacion_relacion = $request->reclamacion_relacion;
        $reclamacion->reclamacion_descripcion1 = $request->reclamacion_descripcion1;
        $reclamacion->reclamacion_descripcion2 = $request->reclamacion_descripcion2;
        $reclamacion->reclamacion_pedido = $request->reclamacion_pedido;
        $reclamacion->save();

        return response($reclamacion, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Reclamacion  $reclamacion
     * @return \Illuminate\Http\Response
     */
    public function destroy(Reclamacion $reclamacion)
    {
        $reclamacion->delete();
        return response([
            'id'=> $reclamacion->id,
            'deleted'=> true,
            'message' => "Se eliminó la reclamación con ID {$reclamacion->id} con exitosamente."
        ], 200);
    }
}
