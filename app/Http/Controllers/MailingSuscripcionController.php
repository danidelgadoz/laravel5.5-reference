<?php

namespace App\Http\Controllers;

use App\MailingSuscripcion;
use Illuminate\Http\Request;

class MailingSuscripcionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mailingSuscripcion = MailingSuscripcion::all();
        return response($mailingSuscripcion, 200);
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
        $mailingSuscripcion = new MailingSuscripcion;
        $mailingSuscripcion->email = $request->email;
        $mailingSuscripcion->save();

        return response($mailingSuscripcion, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\MailingSuscripcion  $mailingSuscripcion
     * @return \Illuminate\Http\Response
     */
    public function show(MailingSuscripcion $mailingSuscripcion)
    {
        return response($mailingSuscripcion, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\MailingSuscripcion  $mailingSuscripcion
     * @return \Illuminate\Http\Response
     */
    public function edit(MailingSuscripcion $mailingSuscripcion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\MailingSuscripcion  $mailingSuscripcion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MailingSuscripcion $mailingSuscripcion)
    {
        $mailingSuscripcion->habilitado = $request->habilitado;
        $mailingSuscripcion->save();
        return response($mailingSuscripcion, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\MailingSuscripcion  $mailingSuscripcion
     * @return \Illuminate\Http\Response
     */
    public function destroy(MailingSuscripcion $mailingSuscripcion)
    {
        $mailingSuscripcion->delete();
        return response([
            'id'=> $mailingSuscripcion->id,
            'deleted'=> true,
            'message' => "Se eliminó la suscripción de correos con ID {$mailingSuscripcion->id} con exitosamente."
        ], 200);
    }
}
