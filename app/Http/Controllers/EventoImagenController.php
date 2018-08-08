<?php

namespace App\Http\Controllers;

use App\EventoImagen;
use Illuminate\Http\Request;
use Storage;

class EventoImagenController extends Controller
{
    public $storagePath = 'public/img/eventos';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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
        $feature_image_path = $this->uploadOneFile($request, 'imagen');

        $eventoImagen = new EventoImagen;
        $eventoImagen->evento_id = $request->evento_id;
        $eventoImagen->url = $feature_image_path;
        $eventoImagen->save();

        return response($eventoImagen, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\EventoImagen  $eventoImagen
     * @return \Illuminate\Http\Response
     */
    public function show(EventoImagen $eventoImagen)
    {
        return response($eventoImagen, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EventoImagen  $eventoImagen
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EventoImagen $eventoImagen)
    {
        $feature_image_path = $this->uploadOneFile($request, 'imagen');

        $eventoImagen->evento_id = $request->evento_id;
        $eventoImagen->url = $feature_image_path;
        $eventoImagen->save();

        return response($eventoImagen, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EventoImagen  $eventoImagen
     * @return \Illuminate\Http\Response
     */
    public function destroy(EventoImagen $eventoImagen)
    {
        $eventoImagen->delete();

        return response([
            'id'=> $eventoImagen->id,
            'deleted'=> true,
            'message' => "Se eliminó el evento con ID ${eventoImagen['id']} exitosamente."
        ], 200);
    }

    private function uploadOneFile(Request $request, $field)
    {
        if($request->hasFile($field))
        {
            $path = $request->file($field)->store($this->storagePath);
            return url(Storage::url($path));
        }
        return null;
    }
}
