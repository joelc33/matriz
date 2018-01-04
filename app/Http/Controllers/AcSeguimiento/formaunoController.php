<?php

namespace matriz\Http\Controllers\AcSeguimiento;
//*******agregar esta linea******//
use matriz\Models\AcSegto\tab_ac;
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

class formaunoController extends Controller
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
    return View::make('seguimiento.ac.001.lista');
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
      ->join('mantenimiento.tab_ejecutores as t01', 'ac_seguimiento.tab_ac.id_tab_ejecutores', '=', 't01.id')
      ->join('mantenimiento.tab_lapso as t02', 'ac_seguimiento.tab_ac.id_tab_lapso', '=', 't02.id')
      ->select( 'ac_seguimiento.tab_ac.id', 'tx_ejecutor', 'ac_seguimiento.tab_ac.id_tab_ejecutores',
      'ac_seguimiento.tab_ac.in_activo',
      DB::raw("to_char(t02.fe_inicio, 'dd/mm/YYYY') as fe_inicio"),
      DB::raw("to_char(t02.fe_fin, 'dd/mm/YYYY') as fe_fin"), 'nu_codigo', 'de_ac', 'in_001', 'ac_seguimiento.tab_ac.id_ejecutor' )
      ->where('ac_seguimiento.tab_ac.id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
      ->where('ac_seguimiento.tab_ac.in_activo', '=', true);

      $rol_planificador = array(3, 8);
      if (in_array(Session::get('rol'), $rol_planificador)) {
          $tab_ac->where('ac_seguimiento.tab_ac.id_tab_ejecutores', '=', Session::get('id_tab_ejecutores'));
      }

      if (Input::get("BuscarBy")=="true") {

        if($variable!=""){
          $tab_ac->where('nu_codigo', 'ILIKE', "%$variable%");
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
      return Response::json(array('success' => false, 'message' => utf8_encode( $e->getMessage())), 500);
    }
  }

  /**
  * Display a listing of the resource.
  *
  * @return Response
  */
  public function detalle()
  {
    $data = tab_ac::join('mantenimiento.tab_ejecutores as t01', 'ac_seguimiento.tab_ac.id_tab_ejecutores', '=', 't01.id')
    ->join('mantenimiento.tab_lapso as t02', 'ac_seguimiento.tab_ac.id_tab_lapso', '=', 't02.id')
    ->select( 'ac_seguimiento.tab_ac.id', 'tx_ejecutor', 'ac_seguimiento.tab_ac.id_tab_ejecutores',
    'ac_seguimiento.tab_ac.in_activo',
    DB::raw("to_char(t02.fe_inicio, 'dd/mm/YYYY') as fe_inicio"),
    DB::raw("to_char(t02.fe_fin, 'dd/mm/YYYY') as fe_fin"), 'nu_codigo', 'de_ac' )
    ->where('ac_seguimiento.tab_ac.id', '=', Input::get('codigo'))
    ->first();

    return View::make('seguimiento.ac.001.detalle')->with('data',$data);
  }

  /**
  * Display a listing of the resource.
  *
  * @return Response
  */
  public function datos($id)
  {
    $data = tab_ac::select( 'id', 'nu_codigo', 'id_tab_ejecutores', 'id_tab_ejercicio_fiscal', 'id_tab_ac_predefinida',
       'id_tab_sectores', 'id_tab_estatus', 'id_tab_situacion_presupuestaria',
       'id_tab_tipo_registro', 'co_new_etapa', 'de_ac', 'mo_ac', 'mo_calculado',
       'fe_inicio', 'fe_fin', 'inst_mision', 'inst_vision', 'inst_objetivos',
       'nu_po_beneficiar', 'nu_em_previsto', 'tx_re_esperado', 'in_activo', 'id_tab_lapso' )
    ->where('id', '=', $id)
    ->first();

    //return View::make('seguimiento.ac.001.datos.lista')->with('data',$data);
    return View::make('seguimiento.ac.001.datos.editar')->with('data',$data);
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
      $tabla->inst_mision = Input::get("mision");
      $tabla->inst_vision = Input::get("vision");
      $tabla->inst_objetivos = Input::get("objetivos");
      $tabla->in_001 = true;
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

       try {
      $validator = Validator::make(Input::all(), tab_ac::$validarCrear);
      if ($validator->fails()){
        return Response::json(array(
          'success' => false,
          'msg' => $validator->getMessageBag()->toArray()
        ));
      }
      $tabla = new tab_ac;
      $tabla->inst_mision = Input::get("mision");
      $tabla->inst_vision = Input::get("vision");
      $tabla->inst_objetivos = Input::get("objetivos");
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


}
