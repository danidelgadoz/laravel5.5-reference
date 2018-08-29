<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pedido extends Model
{
    use SoftDeletes;

    protected $table = 'pedido';
    public $timestamps = true;
    protected $dates = ['deleted_at'];
    protected $guarded = ['id',"deleted_at","created_at","updated_at"];
//    protected $with = ['cliente', 'cupon', 'factura'];

    public function cliente()
    {
        return $this->hasOne('App\Cliente', 'id', 'cliente_id')
            ->select([
                'id',
                'first_name',
                'last_name',
                'email',
                'address',
                'address_city',
                'country_code',
                'phone_number'
            ]);
    }

    public function factura()
    {
        return $this->hasOne('App\Factura', 'id', 'factura_id');
    }

    public function cupon()
    {
        return $this->hasOne('App\Cupon', 'id', 'cupon_id');
    }

    public function detalles()
    {
        return $this->hasMany('App\PedidoDetalle', 'pedido_id', 'id')
            ->select([
                'id',
                'precio_unitario',
                'cantidad',
                'total',
                'is_giftcard',
                'mailing_owner_address',
                'mailign_owner_name',
                'producto_id',
                'pedido_id',
            ]);
    }

    public function envio()
    {
        return $this->hasOne('App\Envio', 'pedido_id', 'id');
    }
}
