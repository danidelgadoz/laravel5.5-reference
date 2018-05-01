<?php

namespace App\Http\Controllers;

use App\Suscripcion;
use App\Cliente;
use App\Plan;
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
        $plan = Plan::find($request->plan_id);
        if(!$plan) {
            return response(['message' => "No existe el siguiente plan_id: {$request->plan_id}"], 200);
        }

        $culqi = new Culqi(array('api_key' => env('CULQUI_PRIVATE_KEY')));
        $cliente = Cliente::where('email', $request->cliente['email'])->first();

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
            $culqi_tarjeta = $culqi->Cards->create(
                array(
                    "customer_id" => $culqui_cliente->id,
                    "token_id" => $request->header('culqui-token-id')
                )
            );

            $cliente = new Cliente();
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
        }

        $culqui_suscripcion = $culqi->Subscriptions->create(
            array(
                "card_id" => $cliente->culqui_card_id,
                "plan_id" => $plan->culqui_id
            )
        );

        $suscripcion = new Suscripcion();
        $suscripcion->culqui_suscription_id = $culqui_suscripcion->id;
        $suscripcion->entrega_direccion = $request->entrega_direccion;
        $suscripcion->entrega_distrito = $request->entrega_distrito;
        $suscripcion->entrega_referencia = $request->entrega_referencia;
        $suscripcion->plan_id = $plan->id;
        $suscripcion->cliente_id = $cliente->id;
        $suscripcion->save();

        return response($suscripcion, 201);
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
