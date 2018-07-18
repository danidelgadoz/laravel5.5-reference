<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    //
    protected $table = 'delivery';
    public $timestamps = false;
    protected $guarded = ['id'];
    protected $with = ['suscripcion_pagada'];

    public function suscripcion_pagada()
    {
        return $this->hasOne('App\SuscripcionPagada', 'id', 'suscripcion_pagada_id');
    }
}
