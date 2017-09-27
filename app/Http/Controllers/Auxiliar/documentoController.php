<?php

namespace matriz\Http\Controllers\Auxiliar;
//*******agregar esta linea******//
use matriz\Models\Mantenimiento\tab_documento;
use matriz\Models\Mantenimiento\tab_cargo;
use matriz\Models\Autenticacion\tab_rol;
use matriz\Models\Mantenimiento\tab_ejecutores;
use matriz\Models\Mantenimiento\tab_ac_ae_predefinida;
use matriz\Models\Mantenimiento\tab_tipo_ejecutor;
use matriz\Models\Mantenimiento\tab_ambito_ejecutor;
use Input;
use Response;
use DB;
//*******************************//
use Illuminate\Http\Request;

use matriz\Http\Requests;
use matriz\Http\Controllers\Controller;

class documentoController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
    }
    /**
  	 * Show the form for creating a new resource.
  	 *
  	 * @return Response
  	 */
  	public function documento()
  	{
  		$response['success']  = 'true';
  		$response['data']  = tab_documento::select('id','inicial')->where('tipo', '=', "N")->orderby('id','ASC')->get()->toArray();
  		return Response::json($response, 200);
  	}

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function cargo()
    {
      $response['success']  = 'true';
      $response['data']  = tab_cargo::select('id','de_cargo')->where('in_activo', '=', true)->orderby('id','ASC')->get()->toArray();
      return Response::json($response, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function rol()
    {
      $response['success']  = 'true';
      $response['data']  = tab_rol::select('id','de_rol')->where('in_estatus', '=', true)->orderby('id','ASC')->get()->toArray();
      return Response::json($response, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function ejecutorTodo()
    {
      $response['success']  = 'true';
      $response['data']  = tab_ejecutores::select('id','id_ejecutor','tx_ejecutor')/*->where('in_activo', '=', true)*/->orderby('id','ASC')->get()->toArray();
      return Response::json($response, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function acAe()
    {
      $response['success']  = 'true';
      $response['data']  = tab_ac_ae_predefinida::select('id','nu_numero as numero', 'de_nombre as nombre')->where('id_padre', '=', Input::get('id_accion'))->where('in_activo', '=', true)->orderby('id','ASC')->get()->toArray();
      return Response::json($response, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function acAeActivo()
    {
      $response['success']  = 'true';
      $response['data']  = tab_ac_ae_predefinida::select('id','nu_numero', 'de_nombre')->where('id_padre', '=', Input::get('id_accion'))->where('in_activo', '=', true)->orderby('id','ASC')->get()->toArray();
      return Response::json($response, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function ejecutorAmbito()
    {
      $response['success']  = 'true';
      $response['data']  = tab_ambito_ejecutor::select('id','de_ambito_ejecutor')->where('in_activo', '=', true)->orderby('id','ASC')->get()->toArray();
      return Response::json($response, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function ejecutorTipo()
    {
      $response['success']  = 'true';
      $response['data']  = tab_tipo_ejecutor::select('id','de_tipo_ejecutor')->where('in_activo', '=', true)->orderby('id','ASC')->get()->toArray();
      return Response::json($response, 200);
    }

}
