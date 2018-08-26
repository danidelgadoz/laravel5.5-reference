<?php

namespace App\Http\Controllers;

use App\Beneficio;
use App\BeneficioImagen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Storage;

class BeneficioController extends Controller
{
    public $storagePath = 'public/img/beneficios';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = $request->limit ? $request->limit : 1000;

        $beneficios = Beneficio::with(['imagenes'])
            ->orderByDesc("id")
            ->limit($limit)
            ->get();

        return response($beneficios, 200);
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

        $beneficio = DB::transaction(function () use ($request, $images_list_array, $feature_image_path) {
            $beneficio = new Beneficio();
            $beneficio->publicado = $request->publicado;
            $beneficio->titulo = $request->titulo;
            $beneficio->inner_html = $request->inner_html;
            $beneficio->featured = $request->featured;
            $beneficio->feature_image = $feature_image_path;
            $beneficio->categoria_id = $request->categoria_id;
            $beneficio->save();

            foreach ($images_list_array as $imagen) {
                $beneficio_imagen = new BeneficioImagen();
                $beneficio_imagen->url = $imagen;
                $beneficio_imagen->beneficio_id = $beneficio->id;
                $beneficio_imagen->save();
            }

            return $beneficio;
        });

        $beneficio_response = Beneficio::with([
            'imagenes'
        ])->findOrFail($beneficio->id);

        return response($beneficio_response, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Beneficio  $beneficio
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $beneficio = Beneficio::with([
            'imagenes'
        ])->findOrFail($id);

        return response($beneficio, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Beneficio  $beneficio
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Beneficio $beneficio)
    {
        $feature_image_path = $this->uploadOneFile($request, 'feature_image');

        $beneficio->publicado = $request->publicado;
        $beneficio->titulo = $request->titulo;
        $beneficio->inner_html = $request->inner_html;
        $beneficio->featured = $request->featured;
        $beneficio->feature_image = $feature_image_path ? $feature_image_path : null;
        $beneficio->categoria_id = $request->categoria_id;
        $beneficio->save();

        $beneficio_response = Beneficio::with([
            'imagenes'
        ])->findOrFail($beneficio->id);

        return response($beneficio_response, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Beneficio  $beneficio
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $beneficio = Beneficio::findOrFail($id);
        $beneficio->delete();

        return response([
            'id'=> $beneficio->id,
            'deleted'=> true,
            'message' => "Se eliminó el beneficio con ID ${beneficio['id']} exitosamente."
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

    public function getRelated(Request $request, Beneficio $beneficio)
    {
        $limit = $request->limit ? $request->limit : 1000;

        $beneficios = Beneficio::where('categoria_id', $beneficio->categoria_id)
            ->where('id', '!=' , $beneficio->id)
            ->orderByDesc("id")
            ->limit($limit)
            ->get();

        return response($beneficios, 200);
    }

}
