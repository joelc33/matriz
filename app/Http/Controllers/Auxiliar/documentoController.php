<?php

namespace matriz\Http\Controllers\Auxiliar;
//*******agregar esta linea******//
use matriz\Models\Mantenimiento\tab_documento;
use matriz\Models\Mantenimiento\tab_cargo;
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
      $response['data']  = tab_cargo::select('id','de_cargo')->orderby('id','ASC')->get()->toArray();
      return Response::json($response, 200);
    }

}
