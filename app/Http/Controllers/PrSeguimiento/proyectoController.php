<?php

namespace matriz\Http\Controllers\PrSeguimiento;
//*******agregar esta linea******//
use matriz\Models\ProySegto\tab_proyecto;
use matriz\Models\Mantenimiento\tab_lapso;
use matriz\Models\Proyecto\tab_proyecto as proyecto;
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
  public function lista()
  {
    return View::make('seguimiento.proyecto.lista');
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
      ->join('mantenimiento.tab_ejecutores as t01', 'proyecto_seguimiento.tab_proyecto.id_tab_ejecutores', '=', 't01.id')
      ->join('mantenimiento.tab_lapso as t02', 'proyecto_seguimiento.tab_proyecto.id_tab_lapso', '=', 't02.id')
      ->select( 'proyecto_seguimiento.tab_proyecto.id', 'tx_ejecutor', 'proyecto_seguimiento.tab_proyecto.id_tab_ejecutores',
      'proyecto_seguimiento.tab_proyecto.in_activo',
      DB::raw("to_char(t02.fe_inicio, 'dd/mm/YYYY') as fe_inicio"),
      DB::raw("to_char(t02.fe_fin, 'dd/mm/YYYY') as fe_fin"), 'nu_codigo', 'de_nombre', 'proyecto_seguimiento.tab_proyecto.id_ejecutor' )
      ->where('proyecto_seguimiento.tab_proyecto.id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
      ->where('proyecto_seguimiento.tab_proyecto.in_activo', '=', true);

      $rol_planificador = array(3, 8);
      if (in_array(Session::get('rol'), $rol_planificador)) {
          $tab_proyecto->where('proyecto_seguimiento.tab_proyecto.id_tab_ejecutores', '=', Session::get('id_tab_ejecutores'));
      }

      if (Input::get("BuscarBy")=="true") {

        if($variable!=""){
          $tab_proyecto->where('tx_ejecutor', 'ILIKE', "%$variable%");
        }

        $response['success']  = 'true';
        $response['total'] = $tab_proyecto->count();
        $tab_proyecto->skip($start)->take($limit);
        $response['data']  = $tab_proyecto->orderby('proyecto_seguimiento.tab_proyecto.id','ASC')->get()->toArray();
      } else {
        $response['success']  = 'true';
        $response['total'] = $tab_proyecto->count();
        $tab_proyecto->skip($start)->take($limit);
        $response['data']  = $tab_proyecto->orderby('proyecto_seguimiento.tab_proyecto.id','ASC')->get()->toArray();
      }

      return Response::json($response, 200);
    } catch (\Illuminate\Database\QueryException $e) {
      return Response::json(array('success' => false, 'message' => utf8_encode( $e->getMessage())), 500);
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
    return View::make('seguimiento.proyecto.editar')->with('data',$data);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return Response
   */
  public function disponible()
  {

    $excluir = tab_proyecto::select('nu_codigo')
    ->where('id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
    ->where('id_tab_lapso', '=', Input::get('periodo'))
    ->get()->toArray();

    $tab_proyecto = proyecto::
    join('mantenimiento.tab_ejecutores as t01', 'public.t26_proyectos.id_ejecutor', '=', 't01.id_ejecutor')
    ->select( 'public.t26_proyectos.co_proyectos', 'public.t26_proyectos.id_ejecutor', 'id_ejercicio',
    'codigo_new_etapa', 'nombre as de_nombre', 'tx_ejecutor', DB::raw("id_proyecto as codigo"))
    ->where('edo_reg', '=', true)
    ->where('co_estatus', '=', 3)
    ->whereNotIn(DB::raw("id_proyecto"), $excluir)
    ->where('id_ejercicio', '=', Session::get('ejercicio'));

    $rol_planificador = array(3, 8);
    if (in_array(Session::get('rol'), $rol_planificador)) {
        $tab_proyecto->where('public.t26_proyectos.id_ejecutor', '=', Session::get('ejecutor'));
    }

    $response['success']  = 'true';
    $response['data']  = $tab_proyecto->orderby('public.t26_proyectos.id_ejecutor','ASC')
    ->orderby('public.t26_proyectos.id_proyecto','ASC')
    ->get()->toArray();

    return Response::json($response, 200);
  }

}
