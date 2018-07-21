<?php

namespace App\Http\Controllers;

use App\Suscripcion;
use Illuminate\Http\Request;
use Excel;

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

    public function export(Request $request) {
        $suscripciones = Suscripcion
            ::with(['pedido_detalle' => function ($query) {
                $query->with(['pedido' => function ($query) {
                    $query->with(['cliente', 'factura']);
                }]);
            }])
            ->orderByDesc("id")
            ->get()
            ->map(function ($item) {
                $item['producto_codigo'] = $item->pedido_detalle->producto['codigo'];
                $item['producto_suscripcion_interval'] = $item->pedido_detalle->producto['suscripcion_interval'];
                $item['producto_suscripcion_interval_count'] = $item->pedido_detalle->producto['suscripcion_interval_count'];
                $item['producto_nombre'] = $item->pedido_detalle->producto['nombre'];
                $item['pedido tipo_de_pago'] = $item->pedido_detalle->pedido['tipo_de_pago'];
                $item['pedido estado'] = $item->pedido_detalle->pedido['estado'];
                $item['pedido precio'] = $item->pedido_detalle->pedido['precio'];
                $item['pedido currency_code'] = $item->pedido_detalle->pedido['currency_code'];
                $item['cliente_first_name'] = $item->pedido_detalle->pedido->cliente['first_name'];
                $item['cliente_last_name'] = $item->pedido_detalle->pedido->cliente['last_name'];
                $item['cliente_last_name'] = $item->pedido_detalle->pedido->cliente['last_name'];
                $item['cliente_email'] = $item->pedido_detalle->pedido->cliente['email'];
                $item['cliente_address'] = $item->pedido_detalle->pedido->cliente['address'];
                $item['cliente_address_city'] = $item->pedido_detalle->pedido->cliente['address_city'];
                $item['cliente_country_code'] = $item->pedido_detalle->pedido->cliente['country_code'];
                $item['cliente_phone_number'] = $item->pedido_detalle->pedido->cliente['phone_number'];
                $item['factura_ruc'] = $item->pedido_detalle->pedido->factura['ruc'];
                $item['factura_razon_social'] = $item->pedido_detalle->pedido->factura['razon_social'];
                $item['factura_direccion'] = $item->pedido_detalle->pedido->factura['direccion'];
                $item['factura_distrito'] = $item->pedido_detalle->pedido->factura['distrito'];
                $item['factura_distrito'] = $item->pedido_detalle->pedido->factura['distrito'];
                $item['factura_referencia'] = $item->pedido_detalle->pedido->factura['referencia'];

                return collect($item->toArray())
                    ->except([
                        'updated_at',
                        'deleted_at',
                        'pedido_detalle',
                    ])->all();
            });

        Excel::create('Filename', function($excel) use ($suscripciones) {
            $excel->setTitle('Payments');
            $excel->setCreator('Laravel')->setCompany('WJ Gilmore, LLC');
            $excel->setDescription('payments file');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($suscripciones) {
                $sheet->fromArray($suscripciones, null, 'A1', false, true);
            });
        })->export('xls');
    }
}
