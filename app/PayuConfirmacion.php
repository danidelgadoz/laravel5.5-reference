<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PayuConfirmacion extends Model
{
    protected $table = 'payu_confirmacion';
    public $timestamps = true;
    protected $guarded = ['id',"created_at","updated_at"];
}
