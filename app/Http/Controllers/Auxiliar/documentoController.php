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
use matriz\Models\Mantenimiento\tab_planes;
use matriz\Models\Mantenimiento\tab_planes_zulia;
use matriz\Models\Mantenimiento\tab_ejercicio_fiscal;
use matriz\Models\Mantenimiento\tab_tipo_fondo;
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

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function objetivoHistorico()
    {
      $response['success']  = 'true';
      $response['data']  = tab_planes::select('co_objetivo_historico','tx_descripcion')
      ->where('nu_nivel', '=', 1)
      ->where('in_activo', '=', true)
      ->orderby('co_objetivo_historico','ASC')->get()->toArray();
      return Response::json($response, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function objetivoNacional()
    {
      $response['success']  = 'true';
      $response['data']  = tab_planes::select('co_objetivo_nacional','tx_descripcion')
      ->where('co_objetivo_historico', '=', Input::get('co_objetivo_historico'))
      ->where('nu_nivel', '=', 2)
      ->where('in_activo', '=', true)
      ->orderby('co_objetivo_nacional','ASC')->get()->toArray();
      return Response::json($response, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function objetivoEstrategico()
    {
      $response['success']  = 'true';
      $response['data']  = tab_planes::select('co_objetivo_estrategico','tx_descripcion')
      ->where('co_objetivo_historico', '=', Input::get('co_objetivo_historico'))
      ->where('co_objetivo_nacional', '=', Input::get('co_objetivo_nacional'))
      ->where('nu_nivel', '=', 3)
      ->where('in_activo', '=', true)
      ->orderby('co_objetivo_estrategico','ASC')->get()->toArray();
      return Response::json($response, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function objetivoGeneral()
    {
      $response['success']  = 'true';
      $response['data']  = tab_planes::select('co_objetivo_general','tx_descripcion')
      ->where('co_objetivo_historico', '=', Input::get('co_objetivo_historico'))
      ->where('co_objetivo_nacional', '=', Input::get('co_objetivo_nacional'))
      ->where('co_objetivo_estrategico', '=', Input::get('co_objetivo_estrategico'))
      ->where('nu_nivel', '=', 4)
      ->where('in_activo', '=', true)
      ->orderby('co_objetivo_estrategico','ASC')->get()->toArray();
      return Response::json($response, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function planArea()
    {
      $response['success']  = 'true';
      $response['data']  = tab_planes_zulia::select('co_area_estrategica','tx_descripcion')
      ->where('nu_nivel', '=', 0)
      ->where('in_activo', '=', true)
      ->orderby('co_area_estrategica','ASC')->get()->toArray();
      return Response::json($response, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function planAmbito()
    {
      $response['success']  = 'true';
      $response['data']  = tab_planes_zulia::select('co_ambito_zulia','tx_descripcion')
      ->where('co_area_estrategica', '=', Input::get('co_area_estrategica'))
      ->where('nu_nivel', '=', 1)
      ->where('in_activo', '=', true)
      ->orderby('co_ambito_zulia','ASC')->get()->toArray();
      return Response::json($response, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function planObjetivo()
    {
      $response['success']  = 'true';
      $response['data']  = tab_planes_zulia::select('co_objetivo_zulia','tx_descripcion')
      ->where('co_ambito_zulia', '=', Input::get('co_ambito_zulia'))
      ->where('nu_nivel', '=', 2)
      ->where('in_activo', '=', true)
      ->orderby('co_objetivo_zulia','ASC')->get()->toArray();
      return Response::json($response, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function planMacroproblema()
    {
      $response['success']  = 'true';
      $response['data']  = tab_planes_zulia::select('co_macroproblema','tx_descripcion')
      ->where('co_ambito_zulia', '=', Input::get('co_ambito_zulia'))
      ->where('nu_nivel', '=', 3)
      ->where('in_activo', '=', true)
      ->orderby('co_macroproblema','ASC')->get()->toArray();
      return Response::json($response, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function planNudo()
    {
      $response['success']  = 'true';
      $response['data']  = tab_planes_zulia::select('co_nodo','tx_descripcion')
      ->where('co_macroproblema', '=', Input::get('co_macroproblema'))
      ->where('nu_nivel', '=', 4)
      ->where('in_activo', '=', true)
      ->orderby('co_nodo','ASC')->get()->toArray();
      return Response::json($response, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function ejercicioFiscal()
    {
      $response['success']  = 'true';
      $response['data']  = tab_ejercicio_fiscal::select('id')->orderby('id','ASC')->get()->toArray();
      return Response::json($response, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function fondoTipo()
    {
      $response['success']  = 'true';
      $response['data']  = tab_tipo_fondo::select('id', 'de_tipo_fondo')->orderby('id','ASC')->get()->toArray();
      return Response::json($response, 200);
    }

}
