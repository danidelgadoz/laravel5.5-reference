<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SuscripcionPagada extends Model
{
    protected $table = 'suscripcion_pagada';
    public $timestamps = true;
    protected $guarded = ['id',"deleted_at","created_at","updated_at"];
    protected $with = ['plan', 'delivery'];

    public function plan()
    {
        return $this->hasOne('App\Plan', 'id', 'plan_id');
    }

    public function delivery()
    {
        return $this->hasOne('App\Delivery', 'id', 'delivery_id');
    }
}
