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
//Route::apiResource('culqi_suscripcion', 'CulqiSuscripcionController');
Route::apiResource('pedido', 'PedidoController');
Route::apiResource('suscripcion', 'SuscripcionController');
Route::apiResource('pedido_detalle', 'PedidoDetalleController');
Route::apiResource('contacto-log', 'ContactoLogController');
Route::apiResource('mailing-suscripcion', 'MailingSuscripcionController');
Route::apiResource('reclamacion', 'ReclamacionController');

//Route::get('suscripcion/{suscripcion}/confirm', 'SuscripcionController@confirm');
//Route::get('suscripcion/{suscripcion}/cancel', 'SuscripcionController@cancel');
//Route::get('suscripcion/export/excel', 'SuscripcionController@export');

Route::post('pedido/giftcard', 'PedidoController@giftcard');
Route::put('pedido/{pedido}/confirm', 'PedidoController@confirm');
Route::put('pedido/{pedido}/cancel', 'PedidoController@cancel');

Route::get('giftcard', 'GiftcardController@index');
Route::get('giftcard/{giftcard}', 'GiftcardController@show');
Route::post('giftcard/validar', 'GiftcardController@validar');
Route::put('giftcard/canjear', 'GiftcardController@canjear');
Route::get('giftcard/pedido/{pedido}', 'GiftcardController@getByPedido');

Route::prefix('admin')->middleware('auth:api')->group(function () {
    Route::apiResource('plan', 'PlanController');
});
