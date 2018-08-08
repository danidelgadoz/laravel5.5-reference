<?php

namespace App\Http\Controllers;

use App\Evento;
use App\EventoImagen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Storage;

class EventoController extends Controller
{
    public $storagePath = 'public/img/eventos';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = $request->limit ? $request->limit : 1000;

        $eventos = Evento::with(['imagenes'])
            ->orderByDesc("id")
            ->limit($limit)
            ->get();

        return response($eventos, 200);
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

        $evento = DB::transaction(function () use ($request, $images_list_array, $feature_image_path) {
            $evento = new Evento();
            $evento->publicado = $request->publicado;
            $evento->titulo = $request->titulo;
            $evento->inner_html = $request->inner_html;
            $evento->featured = $request->featured;
            $evento->feature_image = $feature_image_path;
            $evento->categoria_id = $request->categoria_id;
            $evento->save();

            foreach ($images_list_array as $imagen) {
                $evento_imagen = new EventoImagen();
                $evento_imagen->url = $imagen;
                $evento_imagen->evento_id = $evento->id;
                $evento_imagen->save();
            }

            return $evento;
        });

        $evento_response = Evento::with([
            'imagenes'
        ])->findOrFail($evento->id);

        return response($evento_response, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Evento  $evento
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $evento = Evento::with([
            'imagenes'
        ])->findOrFail($id);

        return response($evento, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Evento  $evento
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Evento $evento)
    {
        $feature_image_path = $this->uploadOneFile($request, 'feature_image');

        $evento->publicado = $request->publicado;
        $evento->titulo = $request->titulo;
        $evento->inner_html = $request->inner_html;
        $evento->featured = $request->featured;
        $evento->feature_image = $feature_image_path ? $feature_image_path : null;
        $evento->categoria_id = $request->categoria_id;
        $evento->save();

        $evento_response = Evento::with([
            'imagenes'
        ])->findOrFail($evento->id);

        return response($evento_response, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Evento  $evento
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $evento = Evento::findOrFail($id);
        $evento->delete();

        return response([
            'id'=> $evento->id,
            'deleted'=> true,
            'message' => "Se eliminó el evento con ID ${evento['id']} exitosamente."
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

    public function getRelated(Request $request, Evento $evento)
    {
        $limit = $request->limit ? $request->limit : 1000;

        $eventos = Evento::where('categoria_id', $evento->categoria_id)
            ->where('id', '!=' , $evento->id)
            ->orderByDesc("id")
            ->limit($limit)
            ->get();

        return response($eventos, 200);
    }
}
