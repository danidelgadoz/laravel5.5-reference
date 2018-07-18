<?php

namespace App\Http\Controllers;

use App\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $producto = Producto::orderByDesc("id")->get();
        return response($producto, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $producto = new Producto;
        $producto->codigo = $request->codigo;
        $producto->suscripcion_interval = $request->suscripcion_interval;
        $producto->suscripcion_interval_count = $request->suscripcion_interval_count;
        $producto->nombre = $request->nombre;
        $producto->currency_code = $request->currency_code;
        $producto->precio = $request->precio;
        $producto->save();

        return response($producto, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Producto  $producto
     * @return \Illuminate\Http\Response
     */
    public function show(Producto $producto)
    {
        return response($producto, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Producto  $producto
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Producto $producto)
    {
        $producto->codigo = $request->codigo;
        $producto->suscripcion_interval = $request->suscripcion_interval;
        $producto->suscripcion_interval_count = $request->suscripcion_interval_count;
        $producto->nombre = $request->nombre;
        $producto->currency_code = $request->currency_code;
        $producto->precio = $request->precio;
        $producto->save();

        return response($producto, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Producto  $producto
     * @return \Illuminate\Http\Response
     */
    public function destroy(Producto $producto)
    {
        $producto->delete();

        return response([
            'id'=> $producto->id,
            'deleted'=> true,
            'message' => "Se eliminó el producto con ID ${producto['id']} exitosamente."
        ], 200);
    }
}
