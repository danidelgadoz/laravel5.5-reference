<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    //
    protected $table = 'factura';
    public $timestamps = true;
    protected $guarded = ['id'];
}
