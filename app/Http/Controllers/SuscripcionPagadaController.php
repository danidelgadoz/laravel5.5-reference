<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\SuscripcionPagada;
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
        $suscripciones_pagadas = SuscripcionPagada::whereHas('pedido', function ($query) {
            $query->where('estado', 'CONFIRMADA');
        })->whereNotNull('fecha_de_inicio')->get();

        return response($suscripciones_pagadas, 200);
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
