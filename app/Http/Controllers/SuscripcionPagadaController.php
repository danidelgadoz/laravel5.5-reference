<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\SuscripcionPagada;
use App\Compra;
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
//        $suscripciones_pagadas = SuscripcionPagada::whereNotNull('fecha_de_inicio')->get();
        $suscripciones_pagadas = SuscripcionPagada::whereHas('compra', function ($query) {
            $query->where('estado', 'CONFIRMADA');
        })->get();
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
}
