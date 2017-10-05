<?php

namespace matriz\Http\Controllers\Ac;
//*******agregar esta linea******//
use matriz\Models\Ac\tab_ac_ae;
use View;
use Validator;
use Input;
use Response;
use DB;
//*******************************//
use Illuminate\Http\Request;

use matriz\Http\Requests;
use matriz\Http\Controllers\Controller;

class acaeController extends Controller
{
  protected $tab_ac_ae;

  public function __construct(tab_ac_ae $tab_ac_ae)
  {
    $this->middleware('auth');
    $this->tab_ac_ae = $tab_ac_ae;
  }

  /**
  * Display a listing of the resource.
  *
  * @return Response
  */
  public function lista()
  {
    return View::make('ac.ae.lista');
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

      $tab_ac_ae = $this->tab_ac_ae
      ->select( 'id', 'co_aplicacion', 'de_aplicacion', 'in_activo' );

      if (Input::get("BuscarBy")=="true") {

        if($variable!=""){
          $tab_ac_ae->where('de_aplicacion', 'ILIKE', "%$variable%");
        }

        $response['success']  = 'true';
        $response['total'] = $tab_ac_ae->count();
        $tab_ac_ae->skip($start)->take($limit);
        $response['data']  = $tab_ac_ae->orderby('id','ASC')->get()->toArray();
      } else {
        $response['success']  = 'true';
        $response['total'] = $tab_ac_ae->count();
        $tab_ac_ae->skip($start)->take($limit);
        $response['data']  = $tab_ac_ae->orderby('id','ASC')->get()->toArray();
      }

      return Response::json($response, 200);
    } catch (\Illuminate\Database\QueryException $e) {
      return Response::json(array('success' => false, 'message' => utf8_encode( $e->getMessage())), 200);
    }
  }
}
