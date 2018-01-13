<?php

namespace matriz\Http\Controllers\PrSeguimiento;
//*******agregar esta linea******//
use matriz\Models\ProySegto\tab_proyecto;
use matriz\Models\Mantenimiento\tab_lapso;
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
      ->join('mantenimiento.tab_ejecutores as t01', 'ac_seguimiento.tab_proyecto.id_tab_ejecutores', '=', 't01.id')
      ->join('mantenimiento.tab_lapso as t02', 'ac_seguimiento.tab_proyecto.id_tab_lapso', '=', 't02.id')
      ->select( 'ac_seguimiento.tab_proyecto.id', 'tx_ejecutor', 'ac_seguimiento.tab_proyecto.id_tab_ejecutores',
      'ac_seguimiento.tab_proyecto.in_activo',
      DB::raw("to_char(t02.fe_inicio, 'dd/mm/YYYY') as fe_inicio"),
      DB::raw("to_char(t02.fe_fin, 'dd/mm/YYYY') as fe_fin"), 'nu_codigo', 'de_ac', 'ac_seguimiento.tab_proyecto.id_ejecutor' )
      ->where('ac_seguimiento.tab_proyecto.id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
      ->where('ac_seguimiento.tab_proyecto.in_activo', '=', true);

      $rol_planificador = array(3, 8);
      if (in_array(Session::get('rol'), $rol_planificador)) {
          $tab_proyecto->where('ac_seguimiento.tab_proyecto.id_tab_ejecutores', '=', Session::get('id_tab_ejecutores'));
      }

      if (Input::get("BuscarBy")=="true") {

        if($variable!=""){
          $tab_proyecto->where('tx_ejecutor', 'ILIKE', "%$variable%");
        }

        $response['success']  = 'true';
        $response['total'] = $tab_proyecto->count();
        $tab_proyecto->skip($start)->take($limit);
        $response['data']  = $tab_proyecto->orderby('ac_seguimiento.tab_proyecto.id','ASC')->get()->toArray();
      } else {
        $response['success']  = 'true';
        $response['total'] = $tab_proyecto->count();
        $tab_proyecto->skip($start)->take($limit);
        $response['data']  = $tab_proyecto->orderby('ac_seguimiento.tab_proyecto.id','ASC')->get()->toArray();
      }

      return Response::json($response, 200);
    } catch (\Illuminate\Database\QueryException $e) {
      return Response::json(array('success' => false, 'message' => utf8_encode( $e->getMessage())), 500);
    }
  }
}
