<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Suscripcion extends Model
{
    //
    protected $table = 'suscripcion';
    public $timestamps = false;
    protected $guarded = ['id'];
    protected $with = ['pedido_detalle'];

    public function pedido_detalle()
    {
        return $this->hasOne('App\PedidoDetalle', 'id', 'pedido_detalle_id');
    }
}
