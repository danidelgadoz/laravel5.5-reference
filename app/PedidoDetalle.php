<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PedidoDetalle extends Model
{
    protected $table = 'pedido_detalle';
    public $timestamps = true;
    protected $guarded = ['id',"deleted_at","created_at","updated_at"];
    protected $with = ['pedido', 'producto'];

    public function plan()
    {
        return $this->hasOne('App\Plan', 'id', 'plan_id');
    }

    public function suscripcion()
    {
        return $this->hasOne('App\Suscripcion', 'id', 'suscripcion_id');
    }

    public function pedido()
    {
        return $this->hasOne('App\Pedido', 'id', 'pedido_id');
    }

    public function producto()
    {
        return $this->hasOne('App\Producto', 'id', 'producto_id');
    }
}
