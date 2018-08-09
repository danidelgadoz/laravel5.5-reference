<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BeneficioImagen extends Model
{
    use SoftDeletes;

    protected $table = 'beneficio_imagen';
    public $timestamps = true;
    protected $guarded = ['id',"deleted_at","created_at","updated_at"];

}
