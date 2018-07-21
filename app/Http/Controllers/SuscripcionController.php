<?php

namespace App\Http\Controllers;

use App\Suscripcion;
use Illuminate\Http\Request;

class SuscripcionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $suscripciones = Suscripcion
            ::with(['pedido_detalle' => function ($query) {
                $query->with(['pedido' => function ($query) {
                    $query->with(['cliente', 'factura']);
                }]);
            }])
//            ->whereHas('pedido_detalle', function ($query) {
//                $query->whereHas('pedido', function ($query) {
//                    $query->where('estado', 'CONFIRMADA');
//                });
//            })
            ->orderByDesc("id")
            ->get();

        return response($suscripciones, 200);
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
     * @param  \App\Suscripcion  $suscripcion
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $suscripcion = Suscripcion
            ::with(['pedido_detalle' => function ($query) {
                $query->with(['pedido' => function ($query) {
                    $query->with(['cliente', 'factura']);
                }]);
            }])
            ->find($id);

        return response($suscripcion, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Suscripcion  $suscripcion
     * @return \Illuminate\Http\Response
     */
    public function edit(Suscripcion $suscripcion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Suscripcion  $suscripcion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Suscripcion $suscripcion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Suscripcion  $suscripcion
     * @return \Illuminate\Http\Response
     */
    public function destroy(Suscripcion $suscripcion)
    {
        //
    }
}
