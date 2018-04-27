<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    //
    use SoftDeletes;

    protected $table = 'plan';
    public $timestamps = true;
    protected $dates = ['deleted_at'];
    protected $guarded = ['id',"deleted_at","created_at","updated_at"];

    public function cupon()
    {
        return $this->hasMany('App\Cupon', 'plan_id', 'id');
    }
}
