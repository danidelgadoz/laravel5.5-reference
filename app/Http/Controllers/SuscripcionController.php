<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Suscripcion;
use App\Cliente;
use App\Plan;
use App\Factura;
use App\Delivery;
use Illuminate\Http\Request;
use Excel;

class SuscripcionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->estado) {
            if($request->estado == 'ALL')
                $suscripciones = Suscripcion::all();
            else
                $suscripciones = Suscripcion::where('estado', $request->estado)->get();
        } else {
            $suscripciones = Suscripcion::all();
        }

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

        $suscripcion = DB::transaction(function () use ($request) {
            $cliente = Cliente::where('email', $request->cliente['email'])->first();
            $plan = Plan::where('default', true)->first();

            if (!$cliente) {
                $cliente = new Cliente();
                $cliente->card_number = null;
                $cliente->first_name = $request->cliente['first_name'];
                $cliente->last_name = $request->cliente['last_name'];
                $cliente->email = $request->cliente['email'];
                $cliente->address = $request->cliente['address'];
                $cliente->address_city = $request->cliente['address_city'];
                $cliente->country_code = $request->cliente['country_code'];
                $cliente->phone_number = $request->cliente['phone_number'];
                $cliente->save();
            }

            if ($request->factura) {
                $factura = new Factura();
                $factura->ruc = $request->factura['ruc'];
                $factura->razon_social = $request->factura['razon_social'];
                $factura->direccion = $request->factura['direccion'];
                $factura->distrito = $request->factura['distrito'];
                $factura->referencia = $request->factura['referencia'];
                $factura->save();
            }

            $delivery = new Delivery();
            $delivery->direccion = $request->entrega_direccion;
            $delivery->distrito = $request->entrega_distrito;
            $delivery->referencia = $request->entrega_referencia;
            $delivery->nombres = $request->entrega_remitente;
            $delivery->email = $request->entrega_email;
            $delivery->celular = $request->entrega_celular;
            $delivery->save();

            $suscripcion = new Suscripcion();
            $suscripcion->plan_id = $plan->id;
            $suscripcion->cliente_id = $cliente->id;
            $suscripcion->cupon_id = $request->cupon_id;
            $suscripcion->factura_id = $request->factura ? $factura->id : null;
            $suscripcion->delivery_id = $delivery->id;
            $suscripcion->save();

            return $suscripcion;
        });

        return response([
            'message' => "Registro de cargo de suscripción exitoso",
            'plan' => [
                'name' => $suscripcion->plan->name,
                'amount' => $suscripcion->plan->amount,
                'currency_code' => $suscripcion->plan->currency_code,
                'interval' => $suscripcion->plan->interval,
                'interval_count' => $suscripcion->plan->interval_count,
                'interval_count' => $suscripcion->plan->interval_count,
                'default' => $suscripcion->plan->default,
                'bbva' => $suscripcion->plan->bbva,

            ]
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Suscripcion  $suscripcion
     * @return \Illuminate\Http\Response
     */
    public function show(Suscripcion $suscripcion)
    {
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

    public function confirm(Request $request, Suscripcion $suscripcion)
    {
        $suscripcion->estado = 'CONFIRMADA';
        $suscripcion->save();
        return response($suscripcion, 200);
    }

    public function cancel(Request $request, Suscripcion $suscripcion)
    {
        $suscripcion->estado = 'CANCELADA';
        $suscripcion->save();
        return response($suscripcion, 200);
    }

    public function export(Request $request)
    {
        $suscripciones = Suscripcion::all()->map(function ($item) {
            $item['cliente_first_name'] = $item->cliente['first_name'];
            $item['cliente_last_name'] = $item->cliente['last_name'];
            $item['cliente_email'] = $item->cliente['email'];
            $item['cliente_address'] = $item->cliente['address'];
            $item['cliente_address_city'] = $item->cliente['address_city'];
            $item['cliente_country_code'] = $item->cliente['country_code'];
            $item['cliente_phone_number'] = $item->cliente['phone_number'];
            $item['plan_name'] = $item->plan['name'];
            $item['plan_amount'] = $item->plan['amount'];
            $item['plan_currency_code'] = $item->plan['currency_code'];
            $item['plan_description'] = $item->plan['description'];
            $item['plan_default'] = $item->plan['default'];
            $item['plan_bbva'] = $item->plan['bbva'];
            $item['factura_ruc'] = $item->factura['ruc'];
            $item['factura_razon_social'] = $item->factura['razon_social'];
            $item['factura_direccion'] = $item->factura['direccion'];
            $item['factura_distrito'] = $item->factura['distrito'];
            $item['factura_referencia'] = $item->factura['referencia'];
            $item['delivery_nombres'] = $item->delivery['nombres'];
            $item['delivery_email'] = $item->delivery['email'];
            $item['delivery_celular'] = $item->delivery['celular'];
            $item['delivery_direccion'] = $item->delivery['direccion'];
            $item['delivery_distrito'] = $item->delivery['distrito'];
            $item['delivery_referencia'] = $item->delivery['referencia'];
            return collect($item->toArray())
                ->except([
                    'factura_id',
                    'cupon_id',
                    'cliente_id',
                    'plan_id',
                    'delivery_id',
                    'updated_at',
                    'deleted_at',
                    'cliente',
                    'plan',
                    'cupon',
                    'factura',
                    'delivery'
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
