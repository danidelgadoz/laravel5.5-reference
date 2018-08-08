<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Evento extends Model
{
    use SoftDeletes;

    protected $table = 'evento';
    public $timestamps = true;
    protected $dates = ['deleted_at'];
    protected $guarded = ['id',"deleted_at","created_at","updated_at"];

    public function categoria()
    {
        return $this->hasOne('App\Categoria', 'id', 'categoria_id')
            ->select([
                'id',
                'nombre',
                'descripcion'
            ]);
    }

    public function imagenes()
    {
        return $this->hasMany('App\EventoImagen', 'evento_id', 'id')
            ->select([
                'id',
                'url',
                'evento_id',
            ]);
    }
}