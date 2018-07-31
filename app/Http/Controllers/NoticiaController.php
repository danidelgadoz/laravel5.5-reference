<?php

namespace App\Http\Controllers;

use App\Noticia;
use App\NoticiaImagen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Storage;

class NoticiaController extends Controller
{
    public $storagePath = 'public/img/noticias';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $noticias = Noticia::with(['imagenes'])
            ->orderByDesc("id")
            ->get();

        return response($noticias, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $images_list_array = $this->uploadManyFiles($request, 'images_list');
        $feature_image_path = $this->uploadOneFile($request, 'feature_image');

        $noticia = DB::transaction(function () use ($request, $images_list_array, $feature_image_path) {
            $noticia = new Noticia();
            $noticia->publicado = $request->publicado;
            $noticia->titulo = $request->titulo;
            $noticia->inner_html = $request->inner_html;
            $noticia->featured = $request->featured;
            $noticia->feature_image = $feature_image_path;
            $noticia->categoria_id = $request->categoria_id;
            $noticia->save();

            foreach ($images_list_array as $imagen) {
                $noticia_imagen = new NoticiaImagen();
                $noticia_imagen->url = $imagen;
                $noticia_imagen->noticia_id = $noticia->id;
                $noticia_imagen->save();
            }

            return $noticia;
        });

        $noticia_response = Noticia::with([
            'imagenes'
        ])->findOrFail($noticia->id);

        return response($noticia_response, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Noticia  $noticia
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $noticia = Noticia::with([
            'imagenes'
        ])->findOrFail($id);

        return response($noticia, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Noticia  $noticia
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Noticia $noticia)
    {
        $feature_image_path = $this->uploadOneFile($request, 'feature_image');

        $noticia->publicado = $request->publicado;
        $noticia->titulo = $request->titulo;
        $noticia->inner_html = $request->inner_html;
        $noticia->featured = $request->featured;
        $noticia->feature_image = $feature_image_path ? $feature_image_path : null;
        $noticia->categoria_id = $request->categoria_id;
        $noticia->save();

        $noticia_response = Noticia::with([
            'imagenes'
        ])->findOrFail($noticia->id);

        return response($noticia_response, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Noticia  $noticia
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $noticia = Noticia::findOrFail($id);
        $noticia->delete();

        return response([
            'id'=> $noticia->id,
            'deleted'=> true,
            'message' => "Se eliminó la noticia con ID ${noticia['id']} exitosamente."
        ], 200);
    }

    /**
     * Storage Images functions
     */

    private function uploadOneFile(Request $request, $field)
    {
        if($request->hasFile($field))
        {
            $path = $request->file($field)->store($this->storagePath);
            return url(Storage::url($path));
        }
        return null;
    }

    private function uploadManyFiles(Request $request, $field)
    {
        if($request->hasFile($field))
        {
            $array_images = array();
            foreach ($request->file($field) as $file) {
                $path = $file->store($this->storagePath);
                array_push($array_images, url(Storage::url($path)));
            }
            return $array_images;
        }
        return null;
    }
}
