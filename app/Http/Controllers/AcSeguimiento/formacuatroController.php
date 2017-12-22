<?php

namespace matriz\Http\Controllers\AcSeguimiento;
//*******agregar esta linea******//
use matriz\Models\AcSegto\tab_ac;
use matriz\Models\AcSegto\tab_ac_ae;
use matriz\Models\AcSegto\tab_meta_fisica;
use matriz\Models\AcSegto\tab_meta_financiera;
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

class formacuatroController extends Controller
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
    return View::make('seguimiento.ac.004.lista');
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

      $tab_ac = $this->tab_ac
      ->join('mantenimiento.tab_ejecutores as t01', 'ac_seguimiento.tab_ac.id_tab_ejecutores', '=', 't01.id_ejecutor')
      ->join('mantenimiento.tab_lapso as t02', 'ac_seguimiento.tab_ac.id_tab_lapso', '=', 't02.id')
      ->select( 'ac_seguimiento.tab_ac.id', 'tx_ejecutor', 'ac_seguimiento.tab_ac.id_tab_ejecutores',
      'ac_seguimiento.tab_ac.in_activo',
      DB::raw("to_char(t02.fe_inicio, 'dd/mm/YYYY') as fe_inicio"),
      DB::raw("to_char(t02.fe_fin, 'dd/mm/YYYY') as fe_fin"), 'nu_codigo', 'de_ac' );

      if (Input::get("BuscarBy")=="true") {

        if($variable!=""){
          $tab_ac->where('de_aplicacion', 'ILIKE', "%$variable%");
        }

        $response['success']  = 'true';
        $response['total'] = $tab_ac->count();
        $tab_ac->skip($start)->take($limit);
        $response['data']  = $tab_ac->orderby('ac_seguimiento.tab_ac.id','ASC')->get()->toArray();
      } else {
        $response['success']  = 'true';
        $response['total'] = $tab_ac->count();
        $tab_ac->skip($start)->take($limit);
        $response['data']  = $tab_ac->orderby('ac_seguimiento.tab_ac.id','ASC')->get()->toArray();
      }

      return Response::json($response, 200);
    } catch (\Illuminate\Database\QueryException $e) {
      return Response::json(array('success' => false, 'message' => utf8_encode( $e->getMessage())), 200);
    }
  }

  /**
  * Display a listing of the resource.
  *
  * @return Response
  */
  public function detalle()
  {
    $data = tab_ac::join('mantenimiento.tab_ejecutores as t01', 'ac_seguimiento.tab_ac.id_tab_ejecutores', '=', 't01.id_ejecutor')
    ->join('mantenimiento.tab_lapso as t02', 'ac_seguimiento.tab_ac.id_tab_lapso', '=', 't02.id')
    ->select( 'ac_seguimiento.tab_ac.id', 'tx_ejecutor', 'ac_seguimiento.tab_ac.id_tab_ejecutores',
    'ac_seguimiento.tab_ac.in_activo',
    DB::raw("to_char(t02.fe_inicio, 'dd/mm/YYYY') as fe_inicio"),
    DB::raw("to_char(t02.fe_fin, 'dd/mm/YYYY') as fe_fin"), 'nu_codigo', 'de_ac' )
    ->where('ac_seguimiento.tab_ac.id', '=', Input::get('codigo'))
    ->first();

    return View::make('seguimiento.ac.004.detalle')->with('data',$data);
  }

}
