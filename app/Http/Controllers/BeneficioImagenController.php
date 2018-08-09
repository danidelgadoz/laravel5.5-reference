<?php

namespace App\Http\Controllers;

use App\BeneficioImagen;
use Illuminate\Http\Request;

class BeneficioImagenController extends Controller
{
    public $storagePath = 'public/img/noticias';

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

        $beneficioImagen = new BeneficioImagen;
        $beneficioImagen->beneficio_id = $request->beneficio_id;
        $beneficioImagen->url = $feature_image_path;
        $beneficioImagen->save();

        return response($beneficioImagen, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\BeneficioImagen  $beneficioImagen
     * @return \Illuminate\Http\Response
     */
    public function show(BeneficioImagen $beneficioImagen)
    {
        return response($beneficioImagen, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\BeneficioImagen  $beneficioImagen
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BeneficioImagen $beneficioImagen)
    {
        $feature_image_path = $this->uploadOneFile($request, 'imagen');

        $beneficioImagen->beneficio_id = $request->beneficio_id;
        $beneficioImagen->url = $feature_image_path;
        $beneficioImagen->save();

        return response($beneficioImagen, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\BeneficioImagen  $beneficioImagen
     * @return \Illuminate\Http\Response
     */
    public function destroy(BeneficioImagen $beneficioImagen)
    {
        $beneficioImagen->delete();

        return response([
            'id'=> $beneficioImagen->id,
            'deleted'=> true,
            'message' => "Se eliminó la imagen con ID ${beneficioImagen['id']} exitosamente."
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
