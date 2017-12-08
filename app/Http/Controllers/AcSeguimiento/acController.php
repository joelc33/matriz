<?php

namespace matriz\Http\Controllers\AcSeguimiento;
//*******agregar esta linea******//
use matriz\Models\AcSegto\tab_ac;
use matriz\Models\Ac\tab_ac as ac;
use matriz\Models\Mantenimiento\tab_lapso;
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

class acController extends Controller
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
    return View::make('seguimiento.ac.lista');
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
   * Show the form for creating a new resource.
   *
   * @return Response
   */
  public function nuevo()
  {
    $data = json_encode(array("id" => ""));
    return View::make('seguimiento.ac.editar')->with('data',$data);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return Response
   */
  public function disponible()
  {

    $tab_ac = ac::
    join('mantenimiento.tab_ejecutores as t01', 'public.t46_acciones_centralizadas.id_ejecutor', '=', 't01.id_ejecutor')
    ->join('mantenimiento.tab_ac_predefinida as t03', 'public.t46_acciones_centralizadas.id_accion', '=', 't03.id')
    ->select( 'public.t46_acciones_centralizadas.id', 'public.t46_acciones_centralizadas.id_ejecutor', 'id_ejercicio', 'id_accion', 'id_subsector', 'id_estatus',
       'sit_presupuesto', 'codigo_new_etapa', 'descripcion', 'monto', 'monto_calc',
       'fecha_inicio', 'fecha_fin', 'de_nombre', 'tx_ejecutor',
       DB::raw("'AC' || public.t46_acciones_centralizadas.id_ejecutor || id_ejercicio || lpad(id_accion::text, 5, '0') as codigo"))
    ->where('edo_reg', '=', true)
    ->where('id_estatus', '=', 3)
    ->where('id_ejercicio', '=', Session::get('ejercicio'));

    $rol_planificador = array(3, 8);
    if (in_array(Session::get('rol'), $rol_planificador)) {
        $tab_ac->where('public.t46_acciones_centralizadas.id_ejecutor', '=', Session::get('ejecutor'));
    }

    $response['success']  = 'true';
    $response['data']  = $tab_ac->orderby('public.t46_acciones_centralizadas.id_ejecutor','ASC')
    ->orderby('public.t46_acciones_centralizadas.id_accion','ASC')
    ->get()->toArray();

    return Response::json($response, 200);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function guardar($id = NULL)
  {
  DB::beginTransaction();
    if($id!=''||$id!=null){

       try {
      $validator= Validator::make(Input::all(), tab_ac::$validarEditar);
      if ($validator->fails()){
        return Response::json(array(
          'success' => false,
          'msg' => $validator->getMessageBag()->toArray()
        ));
      }
      $tabla = tab_ac::find($id);
      $tabla->co_aplicacion = Input::get("codigo");
      $tabla->de_aplicacion = Input::get("aplicacion");
      $tabla->save();

      DB::commit();
      return Response::json(array(
        'success' => true,
        'msg' => 'Registro Editado con Exito!'
      ));

          }catch (\Illuminate\Database\QueryException $e)
          {
        DB::rollback();
        return Response::json(array(
          'success' => false,
          'msg' => array('ERROR ('.$e->getCode().'):'=> $e->getMessage())
        ));
          }

    }else{

      $data = tab_lapso::select('id', 'id_tab_ejercicio_fiscal', 'id_tab_periodo', 'nu_lapso', 'fe_inicio',
         'fe_fin', 'in_activo')
      ->where('id', '=', Input::get('ejercicio'))
      ->first();

      $tab_ac = ac::
      join('mantenimiento.tab_ejecutores as t01', 'public.t46_acciones_centralizadas.id_ejecutor', '=', 't01.id_ejecutor')
      ->join('mantenimiento.tab_ac_predefinida as t03', 'public.t46_acciones_centralizadas.id_accion', '=', 't03.id')
      ->select( 'public.t46_acciones_centralizadas.id', 'public.t46_acciones_centralizadas.id_ejecutor',
      'id_ejercicio', 'id_accion', 'id_subsector', 'id_estatus',
         'sit_presupuesto', 'codigo_new_etapa', 'de_nombre', 'monto', 'monto_calc',
         'fecha_inicio', 'fecha_fin', 'de_nombre', 'tx_ejecutor', 't01.id as id_tab_ejecutores',
         'inst_mision', 'inst_vision', 'inst_objetivos', 'nu_po_beneficiar', 'nu_em_previsto',
       'tx_re_esperado', 'tx_pr_objetivo',
         DB::raw("'AC' || public.t46_acciones_centralizadas.id_ejecutor || id_ejercicio || lpad(id_accion::text, 5, '0') as codigo"))
      ->where('edo_reg', '=', true)
      ->where('id_estatus', '=', 3)
      ->where('public.t46_acciones_centralizadas.id', '=', Input::get('ac'))
      ->where('id_ejercicio', '=', $data->id_tab_ejercicio_fiscal)
      ->first();

       try {
      $validator = Validator::make(Input::all(), tab_ac::$validarCrear);
      if ($validator->fails()){
        return Response::json(array(
          'success' => false,
          'msg' => $validator->getMessageBag()->toArray()
        ));
      }
      $tabla = new tab_ac;
      $tabla->nu_codigo = $tab_ac->codigo;
      $tabla->id_tab_ejecutores = $tab_ac->id_ejecutor;
      $tabla->id_tab_ejercicio_fiscal = $tab_ac->id_ejercicio;
      $tabla->id_tab_ac_predefinida = $tab_ac->id_accion;
      $tabla->id_tab_sectores = $tab_ac->id_subsector;
      $tabla->id_tab_estatus = $tab_ac->id_estatus;
      $tabla->id_tab_situacion_presupuestaria = $tab_ac->sit_presupuesto;
      $tabla->id_tab_tipo_registro = 1;
      $tabla->co_new_etapa = $tab_ac->codigo_new_etapa;
      $tabla->de_ac = $tab_ac->de_nombre;
      $tabla->mo_ac = $tab_ac->monto;
      $tabla->mo_calculado = $tab_ac->monto_calc;
      $tabla->fe_inicio = $data->fe_inicio;
      $tabla->fe_fin = $data->fe_fin;
      $tabla->inst_mision = $tab_ac->inst_mision;
      $tabla->inst_vision = $tab_ac->inst_vision;
      $tabla->inst_objetivos = $tab_ac->inst_objetivos;
      $tabla->nu_po_beneficiar = $tab_ac->nu_po_beneficiar;
      $tabla->nu_em_previsto = $tab_ac->nu_em_previsto;
      $tabla->tx_re_esperado = $tab_ac->tx_re_esperado;
      $tabla->id_tab_lapso = $data->id;
      $tabla->in_activo = 'TRUE';
      $tabla->save();

      DB::commit();
      return Response::json(array(
        'success' => true,
        'msg' => 'Registro Guardado con Exito!'
      ));

          }catch (\Illuminate\Database\QueryException $e)
          {
        DB::rollback();
        return Response::json(array(
          'success' => false,
          'msg' => array('ERROR ('.$e->getCode().'):'=> $e->getMessage())
        ));
          }
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

    return View::make('seguimiento.ac.detalle')->with('data',$data);
  }

}
