<?php

namespace App\Http\Controllers;

use App\Cliente;
use Illuminate\Http\Request;
use Culqi\Culqi;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cliente = Cliente::all();
        return response($cliente, 200);
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
        $culqui_cliente = $culqi->Customers->create(
            array(
                "address" => $request->address,
                "address_city" => $request->address_city,
                "country_code" => $request->country_code,
                "email" => $request->email,
                "first_name" => $request->first_name,
                "last_name" => $request->last_name,
                "phone_number" => $request->phone_number,
            )
        );

        $cliente = new Cliente();
        $cliente->culqui_id = $culqui_cliente->id;
        $cliente->first_name = $culqui_cliente->antifraud_details->first_name;
        $cliente->last_name = $culqui_cliente->antifraud_details->last_name;
        $cliente->email = $culqui_cliente->email;
        $cliente->address = $culqui_cliente->antifraud_details->address;
        $cliente->address_city = $culqui_cliente->antifraud_details->address_city;
        $cliente->country_code = $culqui_cliente->antifraud_details->country_code;
        $cliente->phone_number = $culqui_cliente->antifraud_details->phone;
        $cliente->save();

        return response($cliente, 201);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function show(Cliente $cliente)
    {
        return response($cliente, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function edit(Cliente $cliente)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cliente $cliente)
    {
        $cliente->culqui_id = $request->culqui_id;
        $cliente->save();
        return response($cliente, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cliente $cliente)
    {
        $cliente->delete();
        return response([
            'id'=> $cliente->id,
            'deleted'=> true,
            'message' => "Se eliminó el plan con ID {$cliente->id} con exitosamente."
        ], 200);
    }
}
