<?php

namespace matriz\Http\Controllers\Auxiliar;
//*******agregar esta linea******//
use matriz\Models\Mantenimiento\tab_documento;
use matriz\Models\Mantenimiento\tab_cargo;
use matriz\Models\Autenticacion\tab_rol;
use matriz\Models\Mantenimiento\tab_ejecutores;
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
    public function ejecutor()
    {
      $response['success']  = 'true';
      $response['data']  = tab_ejecutores::select('id','id_ejecutor','tx_ejecutor')->where('in_activo', '=', true)->orderby('id','ASC')->get()->toArray();
      return Response::json($response, 200);
    }

}
