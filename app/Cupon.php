<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cupon extends Model
{
    //
    use SoftDeletes;

    protected $table = 'cupon';
    public $timestamps = true;
    protected $dates = ['deleted_at'];
    protected $guarded = ['id',"deleted_at","created_at","updated_at"];
    protected $with = ['plan'];

    public function plan()
    {
        return $this->hasOne('App\Plan', 'id', 'plan_id');
    }
}
