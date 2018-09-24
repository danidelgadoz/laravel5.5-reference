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

Route::prefix('')->middleware('auth:api')->group(function () {
    Route::apiResource('beneficio', 'BeneficioController');
    Route::apiResource('beneficio_imagen', 'BeneficioImagenController');
    Route::apiResource('categoria', 'CategoriaController', ['parameters' => ['categoria' => 'categoria']]);
    Route::apiResource('cliente', 'ClienteController');
    Route::apiResource('contacto-log', 'ContactoLogController');
    Route::apiResource('cupon', 'CuponController');
    Route::apiResource('evento', 'EventoController');
    Route::apiResource('evento_imagen', 'EventoImagenController');
    Route::apiResource('factura', 'FacturaController');
    Route::get('giftcard', 'GiftcardController@index');
    Route::get('giftcard/{giftcard}', 'GiftcardController@show');
    Route::get('giftcard/pedido/{pedido}', 'GiftcardController@getByPedido');
    Route::apiResource('noticia', 'NoticiaController', ['parameters' => ['noticia' => 'noticia']]);
    Route::apiResource('noticia_imagen', 'NoticiaImagenController');
    Route::apiResource('mailing-suscripcion', 'MailingSuscripcionController');
    Route::apiResource('payu_confirmacion', 'PayuConfirmacionController');
    Route::get('payu_confirmacion/pedido/{pedido}', 'PayuConfirmacionController@getByPedido');
    Route::apiResource('pedido', 'PedidoController');
    Route::put('pedido/{pedido}/confirm', 'PedidoController@confirm');
    Route::put('pedido/{pedido}/cancel', 'PedidoController@cancel');
    Route::apiResource('pedido_detalle', 'PedidoDetalleController');
    Route::apiResource('producto', 'ProductoController');
    Route::apiResource('reclamacion', 'ReclamacionController');
    Route::apiResource('suscripcion', 'SuscripcionController');
});

Route::get('beneficio', 'BeneficioController@index');
Route::get('beneficio/{beneficio}', 'BeneficioController@show');
Route::get('beneficio/{beneficio}/relacionadas', 'BeneficioController@getRelated');
Route::post('contacto-log', 'ContactoLogController@store');
Route::post('cupon/validar', 'CuponController@validar');
Route::get('evento', 'EventoController@index');
Route::get('evento/{evento}', 'EventoController@show');
Route::get('evento/{evento}/relacionadas', 'EventoController@getRelated');
Route::post('giftcard/validar', 'GiftcardController@validar');
Route::put('giftcard/canjear', 'GiftcardController@canjear');
Route::get('noticia', 'NoticiaController@index');
Route::get('noticia/{noticia}', 'NoticiaController@show');
Route::get('noticia/filter/featured', 'NoticiaController@getFeatured');
Route::get('noticia/{noticia}/relacionadas', 'NoticiaController@getRelated');
Route::post('mailing-suscripcion', 'MailingSuscripcionController@store');
Route::post('payu/confirmacion', 'PedidoController@payuConfirmation');
Route::post('pedido', 'PedidoController@store');
Route::get('pedido/{pedido}', 'PedidoController@show');
Route::get('producto', 'ProductoController@index');
Route::post('reclamacion', 'ReclamacionController@store');
Route::get('suscripcion/export/excel', 'SuscripcionController@export');


Route::get('env', function () {
    $env = [
        'MAIL_HOST' => env('MAIL_HOST'),
        'CRAFTIMES_EMAIL_CONTACTO' => env('CRAFTIMES_EMAIL_CONTACTO'),
        'CRAFTIMES_EMAIL_CC_PEDIDO' => env('CRAFTIMES_EMAIL_CC_PEDIDO')
    ];
    dd($env) ;
});