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
Route::apiResource('delivery', 'DeliveryController');
Route::apiResource('contacto-log', 'ContactoLogController');
Route::apiResource('mailing-suscripcion', 'MailingSuscripcionController');
Route::get('suscripcion/{suscripcion}/confirm', 'SuscripcionController@confirm');

Route::prefix('admin')->middleware('auth:api')->group(function () {
    Route::apiResource('plan', 'PlanController');
});
