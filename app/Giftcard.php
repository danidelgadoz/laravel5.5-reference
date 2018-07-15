<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Giftcard extends Model
{
    protected $table = 'giftcard';
    public $timestamps = false;
    protected $dates = ['deleted_at'];
    protected $guarded = ['id', "deleted_at", "created_at", "updated_at"];
    protected $with = ['suscripcion_pagada'];

    public function suscripcion_pagada()
    {
        return $this->hasOne('App\SuscripcionPagada', 'id', 'suscripcion_pagada_id');
    }
}
