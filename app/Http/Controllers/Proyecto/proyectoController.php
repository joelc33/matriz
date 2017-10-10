<?php

namespace matriz\Http\Controllers\Proyecto;
//*******agregar esta linea******//
use matriz\Models\Proyecto\tab_proyecto;
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

class proyectoController extends Controller
{
  protected $tab_proyecto;

  public function __construct(tab_proyecto $tab_proyecto)
  {
    $this->middleware('auth');
    $this->tab_proyecto = $tab_proyecto;
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

      $tab_proyecto = $this->tab_proyecto
      ->join('mantenimiento.tab_ejecutores as t01', 'public.t26_proyectos.id_ejecutor', '=', 't01.id_ejecutor')
      ->join('mantenimiento.tab_estatus as t02', 'public.t26_proyectos.co_estatus', '=', 't02.id')
      ->select( 'co_proyectos', 'id_ejercicio', 'id_proyecto', 'nombre', 'monto',
      'tx_ejecutor', 'de_estatus as tx_estatus',
      DB::raw("monto_cargado(id_proyecto) as mo_registrado"),
      DB::raw("coalesce(null, co_estatus = 3) as reabrir"),
      DB::raw("coalesce(null, co_estatus = 1) as eliminar"))
      ->where('edo_reg', '=', TRUE)
      ->where('id_ejercicio', '=', Session::get('ejercicio'));

      $rol_planificador = array(3, 8);
      if (in_array(Session::get('rol'), $rol_planificador)) {
          $tab_proyecto->where('public.t26_proyectos.id_ejecutor', '=', Session::get('ejecutor'));
      }

      if (Input::get("BuscarBy")=="true") {

        if($variable!=""){
          //$tab_proyecto->where('tx_ejecutor', 'ILIKE', "%$variable%");
          $tab_proyecto->whereRaw("tx_ejecutor||nombre||id_proyecto ILIKE '%".$variable."%'");
        }

        $response['success']  = 'true';
        $response['total'] = $tab_proyecto->count();
        $tab_proyecto->skip($start)->take($limit);
        $response['data']  = $tab_proyecto->orderby('id_proyecto','ASC')->get()->toArray();
      } else {
        $response['success']  = 'true';
        $response['total'] = $tab_proyecto->count();
        $tab_proyecto->skip($start)->take($limit);
        $response['data']  = $tab_proyecto->orderby('id_proyecto','ASC')->get()->toArray();
      }

      return Response::json($response, 200);
    } catch (\Illuminate\Database\QueryException $e) {
      return Response::json(array('success' => false, 'message' => utf8_encode( $e->getMessage())), 200);
    }
  }
}
