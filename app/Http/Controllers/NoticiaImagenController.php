<?php

namespace App\Http\Controllers;

use App\NoticiaImagen;
use Illuminate\Http\Request;
use Storage;

class NoticiaImagenController extends Controller
{
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

        $noticiaImagen = new NoticiaImagen;
        $noticiaImagen->noticia_id = $request->noticia_id;
        $noticiaImagen->url = $feature_image_path;
        $noticiaImagen->save();

        return response($noticiaImagen, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\NoticiaImagen  $noticiaImagen
     * @return \Illuminate\Http\Response
     */
    public function show(NoticiaImagen $noticiaImagen)
    {
        return response($noticiaImagen, 200);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\NoticiaImagen  $noticiaImagen
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, NoticiaImagen $noticiaImagen)
    {
        $feature_image_path = $this->uploadOneFile($request, 'imagen');

        $noticiaImagen->noticia_id = $request->noticia_id;
        $noticiaImagen->url = $feature_image_path;
        $noticiaImagen->save();

        return response($noticiaImagen, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\NoticiaImagen  $noticiaImagen
     * @return \Illuminate\Http\Response
     */
    public function destroy(NoticiaImagen $noticiaImagen)
    {
        $noticiaImagen->delete();

        return response([
            'id'=> $noticiaImagen->id,
            'deleted'=> true,
            'message' => "Se eliminó la imagen con ID ${noticiaImagen['id']} exitosamente."
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
