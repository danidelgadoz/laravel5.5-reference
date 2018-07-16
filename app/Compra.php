<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Compra extends Model
{
    use SoftDeletes;

    protected $table = 'compra';
    public $timestamps = true;
    protected $dates = ['deleted_at'];
    protected $guarded = ['id',"deleted_at","created_at","updated_at"];
    protected $with = ['cliente', 'cupon', 'factura'];//, 'suscripciones'];

    public function cliente()
    {
        return $this->hasOne('App\Cliente', 'id', 'cliente_id');
    }

    public function factura()
    {
        return $this->hasOne('App\Factura', 'id', 'factura_id');
    }

    public function cupon()
    {
        return $this->hasOne('App\Cupon', 'id', 'cupon_id');
    }

    public function suscripciones()
    {
        return $this->hasMany('App\SuscripcionPagada', 'compra_id', 'id');
    }
}
