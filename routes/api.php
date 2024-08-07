<?php

use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;

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
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('logout',   'API\Auth\AuthController@logout');
    Route::get('perfil',   'API\Auth\AuthController@me');
});

Route::post('signin',   'API\Auth\AuthController@login');
Route::post('refresh',  'API\Auth\AuthController@refresh');

Route::group(['middleware'=>'auth'],function($router){
    Route::apiResource('user',          'API\Admin\UserController');
    Route::get('user-catalogs',         'API\Admin\UserController@getCatalogs');

    Route::apiResource('permission',    'API\Admin\PermissionController');
    Route::apiResource('role',          'API\Admin\RoleController');
    Route::apiResource('profile',       'API\ProfileController')->only([ 'show', 'update']);

    //Modulos del Sistema
    Route::apiResource('lesiones',              'API\Modulos\LesionesController');
    Route::get('lesiones-anios',                'API\Modulos\LesionesController@getAnios');
    Route::get('catalogos',                     'API\Modulos\CatalogosController@getCatalogos');
    Route::get('catalogo-localidad',            'API\Modulos\CatalogosController@getCatalogoLocalidad');
    Route::get('catalogo-unidad',               'API\Modulos\CatalogosController@getCatalogoClues');
    Route::get('lista-vehiculos',               'API\Modulos\LesionesController@getVehiculos');
    Route::get('imagenes/{id}',                 'API\Modulos\LesionesController@getImagenes');
    Route::get('documentos/{id}',               'API\Modulos\LesionesController@getDocumentos');
    Route::delete('imagenes/{id}',              'API\Modulos\LesionesController@delImagenes');
    Route::delete('documentos/{id}',            'API\Modulos\LesionesController@delDocumentos');
    Route::post('subir-fotos',                  'API\Modulos\LesionesController@fotografias');
    Route::post('subir-documentos',             'API\Modulos\LesionesController@documentos');
    Route::get('document-download/{id}',        'API\Modulos\LesionesController@downloadDocumentos');
    Route::get('reporte-lesiones',              'API\Modulos\ReporteLesionesController@reporte');
    Route::get('permisos-lesiones',             'API\Modulos\PermisosController@lesiones');
   
    /**
     *  Modulo de Reportes
     */
    Route::get('ejecutar-query',                    'API\Admin\DevReporterController@executeQuery');
    Route::get('exportar-query',                    'API\Admin\DevReporterController@exportExcel');

    /**
     * Catalogo
     */
});

Route::middleware('auth')->get('/avatar-images', function (Request $request) {
    $avatars_path = public_path() . config('ng-client.path') . '/assets/avatars';
    $image_files = glob( $avatars_path . '/*', GLOB_MARK );

    $root_path = public_path() . config('ng-client.path');

    $clean_path = function($value)use($root_path) {
        return str_replace($root_path,'',$value);
    };
    
    $avatars = array_map($clean_path, $image_files);

    return response()->json(['images'=>$avatars], HttpResponse::HTTP_OK);
});