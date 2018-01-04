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

      $tab_meta_financiera = tab_meta_financiera::select( 'tx_nombre', DB::raw('sum(mo_presupuesto) as mo_presupuesto'),
      DB::raw('sum(mo_modificado_anual) as mo_modificado_anual'),
      DB::raw('sum(mo_actualizado_anual) as mo_actualizado_anual'),
      DB::raw('sum(mo_comprometido) as mo_comprometido'),
      DB::raw('sum(mo_causado) as mo_causado'),
      DB::raw('sum(mo_pagado) as mo_pagado'),
      'ac_seguimiento.tab_meta_financiera.co_partida' )
      ->join('ac_seguimiento.tab_meta_fisica as t01', 'ac_seguimiento.tab_meta_financiera.id_tab_meta_fisica', '=', 't01.id')
      ->join('ac_seguimiento.tab_ac_ae as t02', 't01.id_tab_ac_ae', '=', 't02.id')
      ->join('ac_seguimiento.tab_ac as t03', 't02.id_tab_ac', '=', 't03.id')
      //->join('mantenimiento.tab_partidas as t04', 't04.co_partida', '=', 'ac_seguimiento.tab_meta_financiera.co_partida')
      ->join('mantenimiento.tab_partidas as t04', function ($j) {
        $j->on('t04.co_partida','=','ac_seguimiento.tab_meta_financiera.co_partida')
          ->on('t04.id_tab_ejercicio_fiscal','=','t03.id_tab_ejercicio_fiscal');
      })
      ->where('t03.id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
      ->where('ac_seguimiento.tab_meta_financiera.in_activo', '=', true)
      ->groupBy('ac_seguimiento.tab_meta_financiera.co_partida')
      ->groupBy('tx_nombre');

      $rol_planificador = array(3, 8);
      if (in_array(Session::get('rol'), $rol_planificador)) {
          $tab_ac->where('t03.id_tab_ejecutores', '=', Session::get('id_tab_ejecutores'));
      }

      if (Input::get("BuscarBy")=="true") {

        if($variable!=""){
          $tab_meta_financiera->where('co_partida', 'ILIKE', "%$variable%");
        }

        $response['success']  = 'true';
        $response['total'] = $tab_meta_financiera->count();
        $tab_meta_financiera->skip($start)->take($limit);
        $response['data']  = $tab_meta_financiera->orderby('ac_seguimiento.tab_meta_financiera.co_partida','ASC')->get()->toArray();
      } else {
        $response['success']  = 'true';
        $response['total'] = $tab_meta_financiera->count();
        $tab_meta_financiera->skip($start)->take($limit);
        $response['data']  = $tab_meta_financiera->orderby('ac_seguimiento.tab_meta_financiera.co_partida','ASC')->get()->toArray();
      }

      return Response::json($response, 200);
    } catch (\Illuminate\Database\QueryException $e) {
      return Response::json(array('success' => false, 'message' => utf8_encode( $e->getMessage())), 500);
    }
  }


}
