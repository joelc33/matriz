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
	Route::get('inicio/bandeja', 'panelController@bandeja');
});

//*Modulos de Autenticacion*/
Route::group(['namespace' => 'Autenticacion'], function(){
	/*Llamadas al controlador autenticar*/
	Route::group(['prefix' => '/'], function(){
		Route::get('', 'autenticarController@index'); // Mostrar login
		Route::post('autenticar', 'autenticarController@validar'); // Verificar datos
		Route::get('autenticar', 'autenticarController@salir'); // Finalizar sesión
		Route::get('autenticar/captcha', 'externoController@captcha'); // Captcha
		Route::post('autenticar/recuperar', 'autenticarController@recuperar'); // Recuperar Password
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
		Route::get('cambiar/clave/{id}', 'usuarioController@cambioClave');
		Route::post('cambiar/clave', 'usuarioController@guardarCambioClave');
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
		Route::get('ac/ae', 'documentoController@acAe');
		Route::post('ac/ae/activo', 'documentoController@acAeActivo');
		Route::post('partida/buscar', 'buscarController@partida');
		Route::get('ejecutor/ambito', 'documentoController@ejecutorAmbito');
		Route::get('ejecutor/tipo', 'documentoController@ejecutorTipo');
		Route::get('objetivo/historico', 'documentoController@objetivoHistorico');
		Route::post('objetivo/nacional', 'documentoController@objetivoNacional');
		Route::post('objetivo/estrategico', 'documentoController@objetivoEstrategico');
		Route::post('objetivo/general', 'documentoController@objetivoGeneral');
		Route::get('plan/area', 'documentoController@planArea');
		Route::post('plan/ambito', 'documentoController@planAmbito');
		Route::post('plan/objetivo', 'documentoController@planObjetivo');
		Route::post('plan/macroproblema', 'documentoController@planMacroproblema');
		Route::post('plan/nudo', 'documentoController@planNudo');
		Route::get('ef', 'documentoController@ejercicioFiscal');
		Route::get('fondo/tipo', 'documentoController@fondoTipo');
		Route::get('recurso/tipo', 'documentoController@recursoTipo');
		Route::get('accion/tipo', 'documentoController@accionTipo');
		Route::get('ejecutor/activo', 'documentoController@ejecutorActivo');
		Route::get('poa/sector', 'documentoController@poaSector');
		Route::post('poa/subsector', 'documentoController@poaSubsector');
		Route::get('poa/situacion', 'documentoController@poaSituacion');
		Route::get('personal/tipo', 'documentoController@personalTipo');
		Route::get('personal/hijo', 'documentoController@personalHijo');
		Route::get('empleado/tipo', 'documentoController@empleadoTipo');
	});
});
//*Modulos de Reportes*/
Route::group(['namespace' => 'Reporte'], function(){
	//*Modulo de roles*/
	Route::group(['prefix' => 'jasper'], function(){
		Route::get('prueba', 'jasperController@prueba');
	});
	//*Modulo de roles*/
	Route::group(['prefix' => 'reporte'], function(){
		Route::get('libro/lista', 'reporteController@lista');
		Route::get('libro/ley', 'leyController@libro');
		Route::get('libro/distribucion', 'distribucionController@libro');
	});
});
//*Modulos de Proyecto*/
Route::group(['namespace' => 'Proyecto'], function(){
	//*Modulo de proyecto*/
	Route::group(['prefix' => 'proyecto'], function(){
		Route::get('nuevo', 'proyectoController@nuevo');
		Route::post('storeLista', 'proyectoController@storeLista');
	});
	//*Modulo de Proyecto Partidas*/
	Route::group(['prefix' => 'proyecto/ae/partida'], function(){
		Route::post('storeLista', 'proyectoaepartidaController@storeLista');
		Route::post('masivo', 'proyectoaepartidaController@procesarMasivo');
		Route::post('individual', 'proyectoaepartidaController@procesarIndividual');
	});
});
//*Modulos de Accion Centralizada*/
Route::group(['namespace' => 'Ac'], function(){
	//*Modulo de Accion Centralizada*/
	Route::group(['prefix' => 'ac'], function(){
		Route::get('nuevo', 'acController@nuevo');
		Route::post('storeLista', 'acController@storeLista');
		Route::post('guardar', 'acController@guardar');
		Route::post('guardar/{id}', 'acController@guardar');
	});
	//*Modulo de Accion Centralizada*/
	Route::group(['prefix' => 'ac/ae'], function(){
		Route::post('storeLista', 'acaeController@storeLista');
	});
	//*Modulo de Accion Centralizada Partidas*/
	Route::group(['prefix' => 'ac/ae/partida'], function(){
		Route::post('storeLista', 'acaepartidaController@storeLista');
		Route::post('masivo', 'acaepartidaController@procesarMasivo');
		Route::get('{ac}/{ae}/bajar', 'acaepartidaController@bajar');
	});
});
//*Modulos de Mantenimiento*/
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
		Route::post('habilitar', 'unidadmedidaController@habilitar');
	});
	//*Modulo de Ejecutores*/
	Route::group(['prefix' => 'mantenimiento/ejecutor'], function(){
		Route::get('lista', 'ejecutorController@lista');
		Route::post('storeLista', 'ejecutorController@storeLista');
		Route::get('nuevo', 'ejecutorController@nuevo');
		Route::get('editar/{id}', 'ejecutorController@editar');
		Route::post('guardar', 'ejecutorController@guardar');
		Route::post('guardar/{id}', 'ejecutorController@guardar');
		Route::post('eliminar', 'ejecutorController@eliminar');
		Route::post('habilitar', 'ejecutorController@habilitar');
	});
	//*Modulo de Sectores*/
	Route::group(['prefix' => 'mantenimiento/sector'], function(){
		Route::get('lista', 'sectorController@lista');
		Route::post('storeLista', 'sectorController@storeLista');
		Route::get('nuevo', 'sectorController@nuevo');
		Route::get('editar/{id}', 'sectorController@editar');
		Route::post('guardar', 'sectorController@guardar');
		Route::post('guardar/{id}', 'sectorController@guardar');
		Route::post('eliminar', 'sectorController@eliminar');
		Route::post('habilitar', 'sectorController@habilitar');
	});
	//*Modulo de Sectores*/
	Route::group(['prefix' => 'mantenimiento/objetivo'], function(){
		Route::get('lista', 'objetivoController@lista');
		Route::post('storeLista', 'objetivoController@storeLista');
		Route::get('nuevo', 'objetivoController@nuevo');
		Route::get('editar/{id}', 'objetivoController@editar');
		Route::post('guardar', 'objetivoController@guardar');
		Route::post('guardar/{id}', 'objetivoController@guardar');
		Route::post('eliminar', 'objetivoController@eliminar');
		Route::post('habilitar', 'objetivoController@habilitar');
	});
	//*Modulo de Planes del Zulia*/
	Route::group(['prefix' => 'mantenimiento/planzulia'], function(){
		Route::get('lista', 'planzuliaController@lista');
		Route::post('storeLista', 'planzuliaController@storeLista');
		Route::get('nuevo', 'planzuliaController@nuevo');
		Route::get('editar/{id}', 'planzuliaController@editar');
		Route::post('guardar', 'planzuliaController@guardar');
		Route::post('guardar/{id}', 'planzuliaController@guardar');
		Route::post('eliminar', 'planzuliaController@eliminar');
		Route::post('habilitar', 'planzuliaController@habilitar');
	});
	//*Modulo de Partidas*/
	Route::group(['prefix' => 'mantenimiento/partida'], function(){
		Route::get('lista', 'partidaController@lista');
		Route::post('storeLista', 'partidaController@storeLista');
		Route::get('nuevo', 'partidaController@nuevo');
		Route::get('editar/{id}', 'partidaController@editar');
		Route::post('guardar', 'partidaController@guardar');
		Route::post('guardar/{id}', 'partidaController@guardar');
		Route::post('eliminar', 'partidaController@eliminar');
		Route::post('habilitar', 'partidaController@habilitar');
	});
	//*Modulo de Cargos*/
	Route::group(['prefix' => 'mantenimiento/cargo'], function(){
		Route::get('lista', 'cargoController@lista');
		Route::post('storeLista', 'cargoController@storeLista');
		Route::get('nuevo', 'cargoController@nuevo');
		Route::get('editar/{id}', 'cargoController@editar');
		Route::post('guardar', 'cargoController@guardar');
		Route::post('guardar/{id}', 'cargoController@guardar');
		Route::post('eliminar', 'cargoController@eliminar');
		Route::post('habilitar', 'cargoController@habilitar');
	});
	//*Modulo de Apicaciones*/
	Route::group(['prefix' => 'mantenimiento/aplicacion'], function(){
		Route::get('lista', 'aplicacionController@lista');
		Route::post('storeLista', 'aplicacionController@storeLista');
		Route::get('nuevo', 'aplicacionController@nuevo');
		Route::get('editar/{id}', 'aplicacionController@editar');
		Route::post('guardar', 'aplicacionController@guardar');
		Route::post('guardar/{id}', 'aplicacionController@guardar');
		Route::post('eliminar', 'aplicacionController@eliminar');
		Route::post('habilitar', 'aplicacionController@habilitar');
	});
	//*Modulo de Fuente de Financiamiento*/
	Route::group(['prefix' => 'mantenimiento/fuentefinanciamiento'], function(){
		Route::get('lista', 'fuentefinanciamientoController@lista');
		Route::post('storeLista', 'fuentefinanciamientoController@storeLista');
		Route::get('nuevo', 'fuentefinanciamientoController@nuevo');
		Route::get('editar/{id}', 'fuentefinanciamientoController@editar');
		Route::post('guardar', 'fuentefinanciamientoController@guardar');
		Route::post('guardar/{id}', 'fuentefinanciamientoController@guardar');
		Route::post('eliminar', 'fuentefinanciamientoController@eliminar');
		Route::post('habilitar', 'fuentefinanciamientoController@habilitar');
	});
	//*Modulo de Fondo*/
	Route::group(['prefix' => 'mantenimiento/fondo'], function(){
		Route::get('lista', 'fondoController@lista');
		Route::post('storeLista', 'fondoController@storeLista');
		Route::get('nuevo', 'fondoController@nuevo');
		Route::get('editar/{id}', 'fondoController@editar');
		Route::post('guardar', 'fondoController@guardar');
		Route::post('guardar/{id}', 'fondoController@guardar');
		Route::post('eliminar', 'fondoController@eliminar');
		Route::post('habilitar', 'fondoController@habilitar');
	});
	//*Modulo de Recursos*/
	Route::group(['prefix' => 'mantenimiento/recurso'], function(){
		Route::get('lista', 'recursoController@lista');
		Route::post('storeLista', 'recursoController@storeLista');
		Route::get('nuevo', 'recursoController@nuevo');
		Route::get('editar/{id}', 'recursoController@editar');
		Route::post('guardar', 'recursoController@guardar');
		Route::post('guardar/{id}', 'recursoController@guardar');
		Route::post('eliminar', 'recursoController@eliminar');
		Route::post('habilitar', 'recursoController@habilitar');
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
		//*Modulo de tipo de accion especifica*/
		Route::get('ae/lista/{id}', 'tipoaccionaeController@lista');
		Route::post('ae/storeLista', 'tipoaccionaeController@storeLista');
		Route::get('ae/nuevo/{id}', 'tipoaccionaeController@nuevo');
		Route::get('ae/editar/{id}', 'tipoaccionaeController@editar');
		Route::post('ae/guardar', 'tipoaccionaeController@guardar');
		Route::post('ae/guardar/{id}', 'tipoaccionaeController@guardar');
		Route::post('ae/eliminar', 'tipoaccionaeController@eliminar');
		//*Modulo de tipo de accion partidas admitidas*/
		Route::get('partida/lista/{id}', 'tipoaccionpartidaController@lista');
		Route::post('partida/storeLista', 'tipoaccionpartidaController@storeLista');
		Route::get('partida/nuevo/{id}', 'tipoaccionpartidaController@nuevo');
		Route::get('partida/editar/{id}', 'tipoaccionpartidaController@editar');
		Route::post('partida/guardar', 'tipoaccionpartidaController@guardar');
		Route::post('partida/guardar/{id}', 'tipoaccionpartidaController@guardar');
		Route::post('partida/eliminar', 'tipoaccionpartidaController@eliminar');
	});
	//*Modulo de Presupuesto de Ingreso*/
	Route::group(['prefix' => 'mantenimiento/presupuestoingreso'], function(){
		Route::get('lista', 'presupuestoingresoController@lista');
		Route::post('storeLista', 'presupuestoingresoController@storeLista');
		Route::get('nuevo', 'presupuestoingresoController@nuevo');
		Route::get('editar/{id}', 'presupuestoingresoController@editar');
		Route::post('guardar', 'presupuestoingresoController@guardar');
		Route::post('guardar/{id}', 'presupuestoingresoController@guardar');
		Route::post('eliminar', 'presupuestoingresoController@eliminar');
		Route::post('habilitar', 'presupuestoingresoController@habilitar');
	});
	//*Modulo de Tipo de Personal*/
	Route::group(['prefix' => 'mantenimiento/tipopersonal'], function(){
		Route::get('lista', 'tipopersonalController@lista');
		Route::post('storeLista', 'tipopersonalController@storeLista');
		Route::get('nuevo', 'tipopersonalController@nuevo');
		Route::get('editar/{id}', 'tipopersonalController@editar');
		Route::post('guardar', 'tipopersonalController@guardar');
		Route::post('guardar/{id}', 'tipopersonalController@guardar');
		Route::post('eliminar', 'tipopersonalController@eliminar');
		Route::post('habilitar', 'tipopersonalController@habilitar');
	});
	//*Modulo de Clasificados por Tipo*/
	Route::group(['prefix' => 'mantenimiento/clasificadortipo'], function(){
		Route::get('lista', 'clasificadortipoController@lista');
		Route::post('storeLista', 'clasificadortipoController@storeLista');
		Route::get('nuevo', 'clasificadortipoController@nuevo');
		Route::get('editar/{id}', 'clasificadortipoController@editar');
		Route::post('guardar', 'clasificadortipoController@guardar');
		Route::post('guardar/{id}', 'clasificadortipoController@guardar');
		Route::post('eliminar', 'clasificadortipoController@eliminar');
		Route::post('habilitar', 'clasificadortipoController@habilitar');
	});
	//*Modulo de Clasificados por Tipo*/
	Route::group(['prefix' => 'mantenimiento/escalasalarial'], function(){
		Route::get('lista', 'escalasalarialController@lista');
		Route::post('storeLista', 'escalasalarialController@storeLista');
		Route::get('nuevo', 'escalasalarialController@nuevo');
		Route::get('editar/{id}', 'escalasalarialController@editar');
		Route::post('guardar', 'escalasalarialController@guardar');
		Route::post('guardar/{id}', 'escalasalarialController@guardar');
		Route::post('eliminar', 'escalasalarialController@eliminar');
		Route::post('habilitar', 'escalasalarialController@habilitar');
	});
});
