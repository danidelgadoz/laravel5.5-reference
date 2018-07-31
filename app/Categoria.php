<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categoria extends Model
{
    use SoftDeletes;

    protected $table = 'categoria';
    public $timestamps = true;
    protected $dates = ['deleted_at'];
    protected $guarded = ['id',"deleted_at","created_at","updated_at"];
//    protected $with = ['noticias'];

    public function noticias()
    {
        return $this->hasMany('App\Noticia', 'categoria_id', 'id')
            ->select([
                'id',
                'publicado',
                'titulo',
                'inner_html',
                'featured',
                'feature_image',
                'categoria_id'
            ]);
    }
}