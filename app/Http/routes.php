<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/*Route::get('/', function () {
    return view('welcome');
});*/

Route::group(['namespace' => 'Panel'], function(){
	Route::get('inicio', 'panelController@inicio');
});

//*Modulos de Autenticacion*/
Route::group(['namespace' => 'Autenticacion'], function(){
	/*Llamadas al controlador autenticar*/
	Route::group(['prefix' => '/'], function(){
		Route::get('', 'autenticarController@index'); // Mostrar login
		Route::post('autenticar', 'autenticarController@validar'); // Verificar datos
		Route::get('autenticar', 'autenticarController@salir'); // Finalizar sesión
		Route::get('autenticar/captcha', 'externoController@captcha'); // Captcha
		Route::get('ejercicio', 'ejercicioController@lista'); // Ejercicio Fiscal
		Route::post('ejercicio', 'ejercicioController@seleccionar'); // Ejercicio Fiscal
		Route::get('ejercicio/lista', 'ejercicioController@ejercicio'); // Ejercicio Fiscal
		//Route::get('recuperar', 'recuperarController@index'); // Resetear Clave
	});
	//*Modulo de roles*/
	Route::group(['prefix' => 'rol'], function(){
		Route::get('lista', 'rolController@lista');
		Route::post('storeLista', 'rolController@storeLista');
		Route::get('nuevo', 'rolController@nuevo');
		Route::post('guardar', 'rolController@guardar');
		Route::post('privilegio', 'rolController@privilegio');
		Route::post('guardarPrivilegio', 'rolController@guardarPrivilegio');
		Route::post('opcion', 'rolController@opcion');
		Route::post('opcion/storeLista', 'rolController@opcionStoreLista');
		Route::post('opcion/si', 'rolController@opcionSi');
		Route::post('opcion/no', 'rolController@opcionNo');
	});
	//*Modulo de usuarios*/
	Route::group(['prefix' => 'usuario'], function(){
		Route::get('lista', 'usuarioController@lista');
		Route::post('storeLista', 'usuarioController@storeLista');
		Route::get('nuevo', 'usuarioController@nuevo');
		Route::get('editar/{id}', 'usuarioController@editar');
		Route::post('guardar', 'usuarioController@guardar');
		Route::post('guardar/{id}', 'usuarioController@guardar');
		Route::get('contrasena', 'usuarioController@contrasena');
		Route::post('cambioContrasena', 'usuarioController@cambioContrasena');
		Route::get('rol', 'usuarioController@rol');
		Route::get('cargo', 'usuarioController@cargo');
		Route::get('documento', 'usuarioController@documento');
		Route::post('resetear', 'usuarioController@resetear');
		Route::post('deshabilitar', 'usuarioController@deshabilitar');
		Route::get('datos', 'usuarioController@datos');
		Route::post('cambios', 'usuarioController@cambios');
	});
	//*Modulo de validar*/
	Route::group(['prefix' => 'validar'], function(){
		Route::post('rif', 'documentoController@rif');
		Route::post('rif/completoP', 'documentoController@rifCompletoP');
		Route::post('rif/completoF', 'documentoController@rifCompletoF');
		Route::post('movil', 'documentoController@movil');
		Route::post('fijo', 'documentoController@fijo');
		Route::post('fax', 'documentoController@fax');
		Route::get('tf/licencia/{id}', 'externoController@tfLicencia');
		Route::get('{id}/guia/sa', 'externoController@guiaSal');
	});
});
//*Modulos de Tablas Auxiliares*/
Route::group(['namespace' => 'Auxiliar'], function(){
	//*Modulo de roles*/
	Route::group(['prefix' => 'auxiliar'], function(){
		Route::get('documento', 'documentoController@documento');
		Route::get('cargo', 'documentoController@cargo');
		Route::get('rol', 'documentoController@rol');
		Route::get('ejecutor/todo', 'documentoController@ejecutorTodo');
	});
});
//*Modulos de Reportes*/
Route::group(['namespace' => 'Reporte'], function(){
	//*Modulo de roles*/
	Route::group(['prefix' => 'jasper'], function(){
		Route::get('prueba', 'jasperController@prueba');
	});
});
//*Modulos de Reportes*/
Route::group(['namespace' => 'Mantenimiento'], function(){
	//*Modulo de roles*/
	Route::group(['prefix' => 'mantenimiento/unidadmedida'], function(){
		Route::get('lista', 'unidadmedidaController@lista');
		Route::post('storeLista', 'unidadmedidaController@storeLista');
		Route::get('nuevo', 'unidadmedidaController@nuevo');
		Route::get('editar/{id}', 'unidadmedidaController@editar');
		Route::post('guardar', 'unidadmedidaController@guardar');
		Route::post('guardar/{id}', 'unidadmedidaController@guardar');
		Route::post('eliminar', 'unidadmedidaController@eliminar');
	});
	//*Modulo de tipo de accion*/
	Route::group(['prefix' => 'mantenimiento/tipoaccion'], function(){
		Route::get('lista', 'tipoaccionController@lista');
		Route::post('storeLista', 'tipoaccionController@storeLista');
		Route::get('nuevo', 'tipoaccionController@nuevo');
		Route::get('editar/{id}', 'tipoaccionController@editar');
		Route::post('guardar', 'tipoaccionController@guardar');
		Route::post('guardar/{id}', 'tipoaccionController@guardar');
		Route::post('eliminar', 'tipoaccionController@eliminar');
	});
});
