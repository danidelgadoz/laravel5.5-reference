<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MailingSuscripcion extends Model
{
    use SoftDeletes;

    protected $table = 'mailing_suscripcion';
    public $timestamps = true;
    protected $dates = ['deleted_at'];
    protected $guarded = ['id',"deleted_at","created_at","updated_at"];
}
