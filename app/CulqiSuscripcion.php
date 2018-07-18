<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CulqiSuscripcion extends Model
{
    //
    use SoftDeletes;

    protected $table = 'culqi_suscripcion';
    public $timestamps = true;
    protected $dates = ['deleted_at'];
    protected $guarded = ['id',"deleted_at","created_at","updated_at"];
    protected $with = ['cliente', 'plan', 'cupon', 'factura'];

    public function cliente()
    {
        return $this->hasOne('App\Cliente', 'id', 'cliente_id');
    }

    public function plan()
    {
        return $this->hasOne('App\Plan', 'id', 'plan_id');
    }

    public function cupon()
    {
        return $this->hasOne('App\Cupon', 'id', 'cupon_id');
    }

    public function factura()
    {
        return $this->hasOne('App\Factura', 'id', 'factura_id');
    }
}
