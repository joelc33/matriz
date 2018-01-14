<?php

namespace matriz\Http\Controllers\PrSeguimiento;
//*******agregar esta linea******//
use matriz\Models\ProySegto\tab_proyecto;
use matriz\Models\ProySegto\tab_forma_005;
use View;
use Validator;
use Input;
use Response;
use DB;
use Session;
use Auth;
//*******************************//
use Illuminate\Http\Request;

use matriz\Http\Requests;
use matriz\Http\Controllers\Controller;

class formacincoController extends Controller
{

  protected $tab_proyecto;
  protected $tab_forma_005;

  public function __construct(tab_proyecto $tab_proyecto, tab_forma_005 $tab_forma_005)
  {
    $this->middleware('auth');
    $this->tab_proyecto = $tab_proyecto;
    $this->tab_forma_005 = $tab_forma_005;
  }

  /**
  * Display a listing of the resource.
  *
  * @return Response
  */
  public function lista()
  {
    return View::make('seguimiento.proyecto.005.lista');
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
      DB::raw("to_char(t02.fe_fin, 'dd/mm/YYYY') as fe_fin"), 'nu_codigo', 'de_nombre as de_proyecto',
      'proyecto_seguimiento.tab_proyecto.id_ejecutor', 'in_005' )
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
  * Display a listing of the resource.
  *
  * @return Response
  */
  public function detalle()
  {
    $data = tab_proyecto::join('mantenimiento.tab_ejecutores as t01', 'proyecto_seguimiento.tab_proyecto.id_tab_ejecutores', '=', 't01.id')
    ->join('mantenimiento.tab_lapso as t02', 'proyecto_seguimiento.tab_proyecto.id_tab_lapso', '=', 't02.id')
    ->select( 'proyecto_seguimiento.tab_proyecto.id', 'tx_ejecutor', 'proyecto_seguimiento.tab_proyecto.id_tab_ejecutores',
    'proyecto_seguimiento.tab_proyecto.in_activo',
    DB::raw("to_char(t02.fe_inicio, 'dd/mm/YYYY') as fe_inicio"),
    DB::raw("to_char(t02.fe_fin, 'dd/mm/YYYY') as fe_fin"), 'nu_codigo', 'de_nombre as de_proyecto' )
    ->where('proyecto_seguimiento.tab_proyecto.id', '=', Input::get('codigo'))
    ->first();

    return View::make('seguimiento.proyecto.005.detalle')->with('data',$data);
  }

  /**
  * Display a listing of the resource.
  *
  * @return Response
  */
  public function datos($id)
  {
    $data = tab_proyecto::select( 'id', 'nu_codigo', 'id_tab_ejercicio_fiscal', 'id_ejecutor', 'id_tab_ejecutores',
       'id_tab_tipo_registro', 'de_nombre', 'id_tab_estatus_proyecto', 'co_new_etapa',
       'fe_inicio', 'fe_fin', 'de_objetivo', 'de_proyecto', 'id_tab_situacion_presupuestaria',
       'mo_proyecto', 'clase_sector', 'clase_subsector', 'plan_operativo', 'id_tab_estatus',
       'in_activo', 'created_at', 'updated_at', 'id_tab_lapso', 'id_tab_origen',
       'in_001', 'in_005', 'in_bloquear_001', 'in_bloquear_005' )
    ->where('id', '=', $id)
    ->first();

    if (tab_forma_005::where('id_tab_proyecto', '=', $id)
    ->where('id_tab_estatus', '=', 5)
    ->where('in_001', '=', false)->exists()) {

      $data = tab_forma_005::select( 'id', 'id_tab_proyecto', 'inst_mision', 'inst_vision', 'inst_objetivos',
       'in_001', 'created_at', 'updated_at', 'de_observacion', 'id_usuario_solicita',
       'id_usuario_procesa', 'id_tab_estatus', 'in_activo as in_bloquear_001' )
      ->where('id_tab_ac', '=', $id)
      ->where('id_tab_estatus', '=', 5)
      ->where('in_001', '=', false)
      ->first();

    }

    return View::make('seguimiento.proyecto.005.datos.editar')->with('data',$data);
  }


}
