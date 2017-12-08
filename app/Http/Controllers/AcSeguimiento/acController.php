<?php

namespace matriz\Http\Controllers\AcSeguimiento;
//*******agregar esta linea******//
use matriz\Models\AcSegto\tab_ac;
use matriz\Models\Ac\tab_ac as ac;
use View;
use Validator;
use Input;
use Response;
use DB;
use Session;
//*******************************//
use Illuminate\Http\Request;

use matriz\Http\Requests;
use matriz\Http\Controllers\Controller;

class acController extends Controller
{
  protected $tab_ac;

  public function __construct(tab_ac $tab_ac)
  {
    $this->middleware('auth');
    $this->tab_ac = $tab_ac;
  }

  /**
  * Display a listing of the resource.
  *
  * @return Response
  */
  public function lista()
  {
    return View::make('seguimiento.ac.lista');
  }

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function storeLista()
  {
    try {
      $start  = Input::get('start', 0);
      $limit  = Input::get('limit', 20);
      $variable = Input::get('variable');

      $tab_aplicacion = $this->tab_aplicacion
      ->select( 'id', 'co_aplicacion', 'de_aplicacion', 'in_activo' );

      if (Input::get("BuscarBy")=="true") {

        if($variable!=""){
          $tab_aplicacion->where('de_aplicacion', 'ILIKE', "%$variable%");
        }

        $response['success']  = 'true';
        $response['total'] = $tab_aplicacion->count();
        $tab_aplicacion->skip($start)->take($limit);
        $response['data']  = $tab_aplicacion->orderby('id','ASC')->get()->toArray();
      } else {
        $response['success']  = 'true';
        $response['total'] = $tab_aplicacion->count();
        $tab_aplicacion->skip($start)->take($limit);
        $response['data']  = $tab_aplicacion->orderby('id','ASC')->get()->toArray();
      }

      return Response::json($response, 200);
    } catch (\Illuminate\Database\QueryException $e) {
      return Response::json(array('success' => false, 'message' => utf8_encode( $e->getMessage())), 200);
    }
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return Response
   */
  public function nuevo()
  {
    $data = json_encode(array("id" => ""));
    return View::make('seguimiento.ac.editar')->with('data',$data);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return Response
   */
  public function disponible()
  {

    $tab_ac = ac::
    join('mantenimiento.tab_ejecutores as t01', 'public.t46_acciones_centralizadas.id_ejecutor', '=', 't01.id_ejecutor')
    ->join('mantenimiento.tab_ac_predefinida as t03', 'public.t46_acciones_centralizadas.id_accion', '=', 't03.id')
    ->select( 'public.t46_acciones_centralizadas.id', 'public.t46_acciones_centralizadas.id_ejecutor', 'id_ejercicio', 'id_accion', 'id_subsector', 'id_estatus',
       'sit_presupuesto', 'codigo_new_etapa', 'descripcion', 'monto', 'monto_calc',
       'fecha_inicio', 'fecha_fin', 'de_nombre', 'tx_ejecutor',
       DB::raw("'AC' || public.t46_acciones_centralizadas.id_ejecutor || id_ejercicio || lpad(id_accion::text, 5, '0') as codigo"))
    ->where('edo_reg', '=', true)
    ->where('id_estatus', '=', 3)
    ->where('id_ejercicio', '=', Session::get('ejercicio'));

    $rol_planificador = array(3, 8);
    if (in_array(Session::get('rol'), $rol_planificador)) {
        $tab_ac->where('public.t46_acciones_centralizadas.id_ejecutor', '=', Session::get('ejecutor'));
    }

    $response['success']  = 'true';
    $response['data']  = $tab_ac->orderby('public.t46_acciones_centralizadas.id_ejecutor','ASC')
    ->orderby('public.t46_acciones_centralizadas.id_accion','ASC')
    ->get()->toArray();

    return Response::json($response, 200);
  }

}
