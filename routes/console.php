<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');

Artisan::command('mydb:timestamp {table_name}', function ($table_name) {
    Schema::table($table_name, function ($table) {
        $table->timestamps();
    });
    $this->info("Timestamps add successful");
})->describe('Add timestamps column to table');

Artisan::command('mydb:softdelete {table_name}', function ($table_name) {
    Schema::table($table_name, function ($table) {
        $table->softDeletes();
    });
    $this->info("softDeletes add successful");
})->describe('Add softDeletes column to table');

Artisan::command('mydb:datetime', function() {
    $query = DB::select('select now() as fecha_actual from dual');
    dd($query);
    // $this-info($query[0]->fecha_actual);
})->describe('Return database datetime');

Artisan::command('php_serve:datetime', function() {
    $this->info(date("Y-m-d H:i:s"));
})->describe('Return server datetime');
/*Artisan::command('mydb:list {table_name}', function ($table_name) {
    $headers = ['nombres', 'apellidos'];
	$clientes = Cliente::all(['nombres', 'apellidos'])->toArray();
	$this->table($headers, $clientes);

})->describe('Add timestamps column to table');*/