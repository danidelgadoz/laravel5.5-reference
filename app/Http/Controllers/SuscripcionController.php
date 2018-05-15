<?php

namespace App\Http\Controllers;

use App\Suscripcion;
use App\Cliente;
use App\Plan;
use App\Factura;
use App\Delivery;
use Illuminate\Http\Request;
use Culqi\Culqi;

class SuscripcionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $suscripciones = Suscripcion::all();
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
        $culqi = new Culqi(array('api_key' => env('CULQUI_PRIVATE_KEY')));
        $cliente = Cliente::where('email', $request->cliente['email'])->first();
        $plan = Plan::find($request->plan_id);

        if(!$plan) {
            return response(['message' => "No existe el siguiente plan_id: {$request->plan_id}"], 200);
        }

        if (!$cliente) {
            $culqui_cliente = $culqi->Customers->create(
                array(
                    "address" => $request->cliente['address'],
                    "address_city" => $request->cliente['address_city'],
                    "country_code" => $request->cliente['country_code'],
                    "email" => $request->cliente['email'],
                    "first_name" => $request->cliente['first_name'],
                    "last_name" => $request->cliente['last_name'],
                    "phone_number" => $request->cliente['phone_number'],
                )
            );
            $cliente = new Cliente();
        }
        else {
            $culqui_cliente = $culqi->Customers->update(
                $cliente->culqui_id,
                array(
                    "address" => $request->cliente['address'],
                    "address_city" => $request->cliente['address_city'],
                    "country_code" => $request->cliente['country_code'],
                    "first_name" => $request->cliente['first_name'],
                    "last_name" => $request->cliente['last_name'],
                    "phone_number" => $request->cliente['phone_number'],
                )
            );
        }

        $culqi_tarjeta = $culqi->Cards->create(
            array(
                "customer_id" => $culqui_cliente->id,
                "token_id" => $request->header('culqui-token-id')
            )
        );

        $culqui_suscripcion = $culqi->Subscriptions->create(
            array(
                "card_id" => $culqi_tarjeta->id,
                "plan_id" => $plan->culqui_id
            )
        );

        $cliente->culqui_id = $culqui_cliente->id;
        $cliente->culqui_card_id = $culqi_tarjeta->id;
        $cliente->first_name = $culqui_cliente->antifraud_details->first_name;
        $cliente->last_name = $culqui_cliente->antifraud_details->last_name;
        $cliente->email = $culqui_cliente->email;
        $cliente->address = $culqui_cliente->antifraud_details->address;
        $cliente->address_city = $culqui_cliente->antifraud_details->address_city;
        $cliente->country_code = $culqui_cliente->antifraud_details->country_code;
        $cliente->phone_number = $culqui_cliente->antifraud_details->phone;
        $cliente->save();

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
        $suscripcion->culqui_suscription_id = $culqui_suscripcion->id;
        $suscripcion->plan_id = $plan->id;
        $suscripcion->cliente_id = $cliente->id;
        $suscripcion->cupon_id = $request->cupon_id;
        $suscripcion->factura_id = $request->factura ? $factura->id : null;
        $suscripcion->delivery_id = $delivery->id;
        $suscripcion->save();

        return response([
            'carftime_id'=> $suscripcion->id,
            'culqui_id'=> $suscripcion->culqui_suscription_id,
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
        //
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
