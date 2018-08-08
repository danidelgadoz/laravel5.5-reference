<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventoImagen extends Model
{
    use SoftDeletes;

    protected $table = 'evento_imagen';
    public $timestamps = true;
    protected $guarded = ['id',"deleted_at","created_at","updated_at"];

}

