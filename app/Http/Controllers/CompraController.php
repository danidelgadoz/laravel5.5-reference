<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Compra;
use App\Cliente;
use App\Plan;
use App\Giftcard;
use App\Factura;
use Illuminate\Http\Request;

class CompraController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $compra = Compra::all();
        return response($compra, 200);
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
        $compra = DB::transaction(function () use ($request) {
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

            $compra = new Compra();
            $compra->cliente_id = $cliente->id;
            $compra->cupon_id = $request->cupon_id;
            $compra->factura_id = $request->factura ? $factura->id : null;
            $compra->amount = 0;
            $compra->precio = 0;
            foreach ($request->giftcards as $suscripcion) {
                $compra->precio = $compra->precio + ($suscripcion['meses'] * $plan->precio);
            }
            $compra->save();

            foreach ($request->giftcards as $suscripcion) {
                $giftcard = new Giftcard();
                $giftcard->codigo = $this->random(10);
//                $giftcard->canjeado = date("Y-m-d H:i:s");
                $giftcard->meses = $suscripcion["meses"];
                $giftcard->precio = $plan->precio;
                $giftcard->entrega_remitente_nombres = $request->giftcards_entrega_remitente_nombres;
                $giftcard->entrega_remitente_email = $request->giftcards_entrega_remitente_email;
                $giftcard->entrega_remitente_telefono = $request->giftcards_entrega_remitente_telefono;
                $giftcard->entrega_direccion = $request->giftcards_entrega_direccion;
                $giftcard->entrega_distrito = $request->giftcards_entrega_distrito;
                $giftcard->entrega_referencia = $request->giftcards_entrega_referencia;
                $giftcard->compra_id = $compra->id;
                $giftcard->plan_id = $plan->id;
                $giftcard->save();
            }

            return $compra;
        });

        return response([
            'id' => $compra->id,
            'message' => "Registro de compra exitoso.",
        ], 201);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Compra  $compra
     * @return \Illuminate\Http\Response
     */
    public function show(Compra $compra)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Compra  $compra
     * @return \Illuminate\Http\Response
     */
    public function edit(Compra $compra)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Compra  $compra
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Compra $compra)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Compra  $compra
     * @return \Illuminate\Http\Response
     */
    public function destroy(Compra $compra)
    {
        //
    }

    /**
     * Generate a more truly "random" alpha-numeric string.
     *
     * @param  int  $length
     * @return string
     *
     * @throws \RuntimeException
     */
    public static function random($length = 16)
    {
        if ( ! function_exists('openssl_random_pseudo_bytes'))
        {
            throw new RuntimeException('OpenSSL extension is required.');
        }

        $bytes = openssl_random_pseudo_bytes($length * 2);

        if ($bytes === false)
        {
            throw new RuntimeException('Unable to generate random string.');
        }

        return substr(str_replace(array('/', '+', '='), '', base64_encode($bytes)), 0, $length);
    }
}
