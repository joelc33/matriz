<?php

namespace matriz\Http\Controllers\AcSeguimiento;
//*******agregar esta linea******//
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

class ejecucionController extends Controller
{
  protected $tab_meta_financiera;

  public function __construct(tab_meta_financiera $tab_meta_financiera)
  {
    $this->middleware('auth');
    $this->tab_meta_financiera = $tab_meta_financiera;
  }

  /**
  * Display a listing of the resource.
  *
  * @return Response
  */
  public function lista()
  {
    return View::make('seguimiento.ac.ejecucion.lista');
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

      $tab_meta_financiera = tab_meta_financiera::select( 'ac_seguimiento.tab_meta_financiera.id', 'id_tab_meta_fisica',
       'ac_seguimiento.tab_meta_financiera.id_tab_municipio_detalle', 'ac_seguimiento.tab_meta_financiera.id_tab_parroquia_detalle',
       'mo_presupuesto', 'co_partida', 'id_tab_fuente_financiamiento', 'ac_seguimiento.tab_meta_financiera.in_activo',
       'ac_seguimiento.tab_meta_financiera.in_cargado', 'de_fuente_financiamiento' )
       ->join('mantenimiento.tab_fuente_financiamiento as t02', 'ac_seguimiento.tab_meta_financiera.id_tab_fuente_financiamiento', '=', 't02.id')
       ->where('id_tab_meta_fisica', '=', $id)
       ->where('ac_seguimiento.tab_meta_financiera.in_activo', '=', true);

      if (Input::get("BuscarBy")=="true") {

        if($variable!=""){
          $tab_meta_financiera->where('de_fuente_financiamiento', 'ILIKE', "%$variable%");
        }

        $response['success']  = 'true';
        $response['total'] = $tab_meta_financiera->count();
        $tab_meta_financiera->skip($start)->take($limit);
        $response['data']  = $tab_meta_financiera->orderby('ac_seguimiento.tab_meta_financiera.id','ASC')->get()->toArray();
      } else {
        $response['success']  = 'true';
        $response['total'] = $tab_meta_financiera->count();
        $tab_meta_financiera->skip($start)->take($limit);
        $response['data']  = $tab_meta_financiera->orderby('ac_seguimiento.tab_meta_financiera.id','ASC')->get()->toArray();
      }

      return Response::json($response, 200);
    } catch (\Illuminate\Database\QueryException $e) {
      return Response::json(array('success' => false, 'message' => utf8_encode( $e->getMessage())), 200);
    }
  }


}
