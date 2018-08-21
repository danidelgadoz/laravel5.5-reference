<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Giftcard extends Model
{
    protected $table = 'giftcard';
    public $timestamps = true;
    protected $dates = ['deleted_at'];
    protected $guarded = ['id', "deleted_at", "created_at", "updated_at"];
//    protected $with = ['pedido_detalle'];

    public function pedido_detalle()
    {
        return $this->hasOne('App\PedidoDetalle', 'id', 'pedido_detalle_id');
    }
}
