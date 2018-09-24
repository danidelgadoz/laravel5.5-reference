<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PedidoDetalle extends Model
{
    protected $table = 'pedido_detalle';
    public $timestamps = true;
    protected $guarded = ['id',"deleted_at","created_at","updated_at"];
    protected $with = ['producto'];

    public function pedido()
    {
        return $this->hasOne('App\Pedido', 'id', 'pedido_id')
            ->select([
                'id',
                'tipo_de_pago',
                'estado',
                'subtotal',
                'descuento',
                'total',
                'currency_code',
                'cliente_id',
                'factura_id',
                'cupon_id'
            ]);
    }

    public function producto()
    {
        return $this->hasOne('App\Producto', 'id', 'producto_id')
            ->select([
                'id',
                'codigo',
                'suscripcion_interval',
                'suscripcion_interval_count',
                'nombre',
                'currency_code',
                'precio',
                'descripcion'
            ]);
    }

    public function suscripcion()
    {
        return $this->belongsTo('App\Suscripcion', 'id', 'pedido_detalle_id')
            ->select([
                'id',
                'pedido_detalle_id'
            ]);
    }

    public function giftcard()
    {
        return $this->belongsTo('App\Giftcard', 'id', 'pedido_detalle_id')
            ->select([
                'id',
                'pedido_detalle_id'
            ]);
    }
}
