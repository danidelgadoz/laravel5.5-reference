<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('cliente', 'ClienteController');
Route::apiResource('cupon', 'CuponController');
Route::apiResource('factura', 'FacturaController');
Route::apiResource('suscripcion', 'SuscripcionController');
Route::apiResource('compra', 'CompraController');
Route::apiResource('delivery', 'DeliveryController');
Route::apiResource('suscripcion_pagada', 'SuscripcionPagadaController');
Route::apiResource('contacto-log', 'ContactoLogController');
Route::apiResource('mailing-suscripcion', 'MailingSuscripcionController');
Route::apiResource('reclamacion', 'ReclamacionController');
Route::get('suscripcion/{suscripcion}/confirm', 'SuscripcionController@confirm');
Route::get('suscripcion/{suscripcion}/cancel', 'SuscripcionController@cancel');
Route::get('suscripcion/export/excel', 'SuscripcionController@export');
Route::post('compra/giftcard', 'CompraController@giftcard');
Route::post('suscripcion_pagada/giftcard/validar', 'SuscripcionPagadaController@validarGiftcard');
Route::put('suscripcion_pagada/giftcard/canjear', 'SuscripcionPagadaController@canjearGiftcard');
Route::put('compra/{compra}/confirm', 'CompraController@confirm');
Route::put('compra/{compra}/cancel', 'CompraController@cancel');

Route::prefix('admin')->middleware('auth:api')->group(function () {
    Route::apiResource('plan', 'PlanController');
});
