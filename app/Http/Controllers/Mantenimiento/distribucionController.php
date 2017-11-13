<?php

namespace matriz\Http\Controllers\Mantenimiento;
//*******agregar esta linea******//
use matriz\Models\Mantenimiento\tab_distribucion_municipio;
use View;
use Validator;
use Input;
use Response;
use DB;
//*******************************//
use Illuminate\Http\Request;

use matriz\Http\Requests;
use matriz\Http\Controllers\Controller;

class distribucionController extends Controller
{
  protected $tab_distribucion_municipio;

  public function __construct(tab_distribucion_municipio $tab_distribucion_municipio)
  {
    $this->middleware('auth');
    $this->tab_distribucion_municipio = $tab_distribucion_municipio;
  }

  /**
  * Display a listing of the resource.
  *
  * @return Response
  */
  public function lista()
  {
    return View::make('mantenimiento.distribucion.lista');
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

      $tab_distribucion_municipio = $this->tab_distribucion_municipio
      ->join('mantenimiento.tab_tipo_personal as t01','t01.id','=','mantenimiento.tab_distribucion_municipio.id_tab_tipo_personal')
      ->select( 'mantenimiento.tab_distribucion_municipio.id', 'id_tab_ejercicio_fiscal', 'id_tab_tipo_personal', 'nu_masculino',
       'nu_femenino', 'mo_sueldo', 'mo_compensacion', 'mo_primas',
       'mantenimiento.tab_distribucion_municipio.in_activo', 'nu_codigo', 'de_tipo_personal' );

      if (Input::get("BuscarBy")=="true") {

        if($variable!=""){
          $tab_distribucion_municipio->where('de_tipo_personal', 'ILIKE', "%$variable%");
        }

        $response['success']  = 'true';
        $response['total'] = $tab_distribucion_municipio->count();
        $tab_distribucion_municipio->skip($start)->take($limit);
        $response['data']  = $tab_distribucion_municipio->orderby('mantenimiento.tab_distribucion_municipio.id','ASC')->get()->toArray();
      } else {
        $response['success']  = 'true';
        $response['total'] = $tab_distribucion_municipio->count();
        $tab_distribucion_municipio->skip($start)->take($limit);
        $response['data']  = $tab_distribucion_municipio->orderby('mantenimiento.tab_distribucion_municipio.id','ASC')->get()->toArray();
      }

      return Response::json($response, 200);
    } catch (\Illuminate\Database\QueryException $e) {
      return Response::json(array('success' => false, 'message' => utf8_encode( $e->getMessage())), 200);
    }
  }
}
