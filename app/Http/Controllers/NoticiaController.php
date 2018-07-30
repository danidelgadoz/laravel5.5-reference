<?php

namespace App\Http\Controllers;

use App\Noticia;
use Illuminate\Http\Request;
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
        $noticias = Noticia
            ::orderByDesc("id")
            ->get()
            ->map(function ($item) {
                $item->images_list = json_decode($item->images_list);
                return collect($item->toArray());
            });
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
        $images_list_json = $this->uploadManyFiles($request, 'images_list');
        $feature_image_path = $this->uploadOneFile($request, 'feature_image');

        $noticia = new Noticia();
        $noticia->publicado = $request->publicado;
        $noticia->titulo = $request->titulo;
        $noticia->inner_html = $request->inner_html;
        $noticia->featured = $request->featured;
        $noticia->feature_image = $feature_image_path;
        $noticia->images_list = $images_list_json;
        $noticia->save();

        $noticia->images_list = $noticia->images_list ? json_decode($noticia->images_list) : null;

        return response($noticia, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Noticia  $noticia
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $noticia = Noticia::findOrFail($id);
        $noticia->images_list = json_decode($noticia->images_list);
        return response($noticia, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Noticia  $noticia
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $noticia = Noticia::findOrFail($id);
        return response($noticia, 200);
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
            return json_encode($array_images);
        }
        return null;
    }
}
