<?php

namespace matriz\Http\Controllers\Mantenimiento;
//*******agregar esta linea******//
use matriz\Models\Mantenimiento\tab_distribucion_municipio;
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
      ->join('mantenimiento.tab_municipio as t01','t01.id','=','mantenimiento.tab_distribucion_municipio.id_tab_municipio')
      ->select( 'mantenimiento.tab_distribucion_municipio.id', 'id_tab_ejercicio_fiscal', 'id_tab_municipio', 'co_partida', 'nu_base_censo',
       'nu_factor_poblacion', 'cuatrocinco_ppi', 'cincocero_fpp', 'superficie_km',
       'superficie_factor', 'extension_territorio', 'mo_total', 'de_municipio' )
       ->where('id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'));

      if (Input::get("BuscarBy")=="true") {

        if($variable!=""){
          $tab_distribucion_municipio->where('de_municipio', 'ILIKE', "%$variable%");
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
    return View::make('mantenimiento.distribucion.editar')->with('data',$data);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return Response
   */
  public function editar($id)
  {
    $data = tab_distribucion_municipio::select('id', 'id_tab_ejercicio_fiscal', 'id_tab_municipio', 'co_partida', 'nu_base_censo',
       'nu_factor_poblacion', 'cuatrocinco_ppi', 'cincocero_fpp', 'superficie_km',
       'superficie_factor', 'extension_territorio', 'mo_total')
    ->where('id', '=', $id)
    ->first();
    return View::make('mantenimiento.distribucion.editar')->with('data',$data);
  }

}
