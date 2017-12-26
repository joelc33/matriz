<?php

namespace matriz\Http\Controllers\AcSeguimiento;
//*******agregar esta linea******//
use matriz\Models\AcSegto\tab_ac;
use matriz\Models\AcSegto\tab_ac_ae;
use matriz\Models\AcSegto\tab_meta_fisica;
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

class formacuatroController extends Controller
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
    return View::make('seguimiento.ac.004.lista');
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

    return View::make('seguimiento.ac.004.detalle')->with('data',$data);
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

    return View::make('seguimiento.ac.004.datos.lista')->with('data',$data);
  }

  /**
  * Display a listing of the resource.
  *
  * @return Response
  */
  public function datosstoreLista()
  {
    try {
      $start  = Input::get('start', 0);
      $limit  = Input::get('limit', 20);
      $variable = Input::get('variable');

      $tab_ac = tab_ac_ae::select( 'ac_seguimiento.tab_ac_ae.id', 'id_tab_ac', 'id_tab_ac_ae_predefinida', 'id_tab_ejecutores', 'bien_servicio',
       'id_tab_unidad_medida', 'meta', 'ponderacion', 'id_tab_tipo_fondo', 'mo_ae',
       'mo_ae_calculado', 'ac_seguimiento.tab_ac_ae.in_activo', 'nu_numero',
       'de_nombre', 'de_unidad_medida', 'tx_ejecutor',
        DB::raw("to_char(fecha_inicio, 'dd-mm-YYYY') as fecha_inicio"),
        DB::raw("to_char(fecha_fin, 'dd-mm-YYYY') as fecha_fin") )
      ->join('mantenimiento.tab_ac_ae_predefinida as t01', 'ac_seguimiento.tab_ac_ae.id_tab_ac_ae_predefinida', '=', 't01.id')
      ->join('mantenimiento.tab_unidad_medida as t02', 'ac_seguimiento.tab_ac_ae.id_tab_unidad_medida', '=', 't02.id')
      ->join('mantenimiento.tab_ejecutores as t03', 'ac_seguimiento.tab_ac_ae.id_tab_ejecutores', '=', 't03.id_ejecutor')
      ->where('id_tab_ac', '=', Input::get('ac'))
      ->where('ac_seguimiento.tab_ac_ae.in_activo', '=', true);

      if (Input::get("BuscarBy")=="true") {

        if($variable!=""){
          $tab_ac->where('de_nombre', 'ILIKE', "%$variable%");
        }

        $response['success']  = 'true';
        $response['total'] = $tab_ac->count();
        $tab_ac->skip($start)->take($limit);
        $response['data']  = $tab_ac->orderby('ac_seguimiento.tab_ac_ae.id','ASC')->get()->toArray();
      } else {
        $response['success']  = 'true';
        $response['total'] = $tab_ac->count();
        $tab_ac->skip($start)->take($limit);
        $response['data']  = $tab_ac->orderby('ac_seguimiento.tab_ac_ae.id','ASC')->get()->toArray();
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
  public function editar($id)
  {
    $data = tab_ac_ae::select( 'id', 'id_tab_ac', 'id_tab_ac_ae_predefinida', 'id_tab_ejecutores', 'bien_servicio',
       'id_tab_unidad_medida', 'meta', 'ponderacion', 'id_tab_tipo_fondo', 'mo_ae',
       'mo_ae_calculado', 'fecha_inicio', 'fecha_fin',
       'in_activo' )
    ->where('id', '=', $id)
    ->first();
    return View::make('seguimiento.ac.004.actividad.lista')->with('data',$data);
  }

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function actividadstoreLista()
  {
    try {
      $start  = Input::get('start', 0);
      $limit  = Input::get('limit', 20);
      $variable = Input::get('variable');

      $tab_meta_fisica = tab_meta_fisica::select( 'ac_seguimiento.tab_meta_fisica.id', 'id_tab_ac_ae', 'codigo', 'nb_meta', 'id_tab_unidad_medida', 'tx_prog_anual',
       'fecha_inicio', 'fecha_fin', 'nb_responsable', 'ac_seguimiento.tab_meta_fisica.in_activo',
       'de_unidad_medida', 'in_cargado',
       DB::raw("to_char(fecha_inicio, 'dd-mm-YYYY') as fecha_inicio"),
       DB::raw("to_char(fecha_fin, 'dd-mm-YYYY') as fecha_fin") )
       ->join('mantenimiento.tab_unidad_medida as t01', 'ac_seguimiento.tab_meta_fisica.id_tab_unidad_medida', '=', 't01.id')
       ->where('id_tab_ac_ae', '=', Input::get('ac_ae'));

      if (Input::get("BuscarBy")=="true") {

        if($variable!=""){
          $tab_meta_fisica->where('nb_meta', 'ILIKE', "%$variable%");
        }

        $response['success']  = 'true';
        $response['total'] = $tab_meta_fisica->count();
        $tab_meta_fisica->skip($start)->take($limit);
        $response['data']  = $tab_meta_fisica->orderby('ac_seguimiento.tab_meta_fisica.id','ASC')->get()->toArray();
      } else {
        $response['success']  = 'true';
        $response['total'] = $tab_meta_fisica->count();
        $tab_meta_fisica->skip($start)->take($limit);
        $response['data']  = $tab_meta_fisica->orderby('ac_seguimiento.tab_meta_fisica.id','ASC')->get()->toArray();
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
  public function editarActividad($id)
  {
    $data = tab_meta_fisica::select( 'id', 'id_tab_ac_ae', 'codigo', 'nb_meta', 'id_tab_unidad_medida', 'tx_prog_anual',
       'fecha_inicio', 'fecha_fin', 'nb_responsable', 'in_activo', 'created_at',
       'updated_at', 'nu_meta_modificada', 'nu_meta_actualizada', 'nu_obtenido',
       'nu_corte', 'id_tab_municipio_detalle', 'id_tab_parroquia_detalle',
       'in_cargado', 'de_desvio' )
    ->where('id', '=', $id)
    ->first();

    return View::make('seguimiento.ac.004.actividad.editar')->with('data',$data);
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
      $validator= Validator::make(Input::all(), tab_meta_fisica::$validarEditarMeta);
      if ($validator->fails()){
        return Response::json(array(
          'success' => false,
          'msg' => $validator->getMessageBag()->toArray()
        ));
      }
      $tabla = tab_meta_fisica::find($id);
      $tabla->nb_meta = Input::get("actividad");
      $tabla->id_tab_unidad_medida = Input::get("unidad_medida");
      $tabla->tx_prog_anual = Input::get("programado_anual");
      $tabla->fecha_inicio = Input::get("fecha_inicio");
      $tabla->fecha_fin = Input::get("fecha_culminacion");
      $tabla->nb_responsable = Input::get("responsable");
      $tabla->de_desvio = Input::get("desvio");
      //$tabla->in_cargado = true;
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
      $validator = Validator::make(Input::all(), tab_meta_fisica::$validarCrearMeta);
      if ($validator->fails()){
        return Response::json(array(
          'success' => false,
          'msg' => $validator->getMessageBag()->toArray()
        ));
      }
      $tabla = new tab_meta_fisica;
      $tabla->nb_meta = Input::get("actividad");
      $tabla->id_tab_unidad_medida = Input::get("unidad_medida");
      $tabla->tx_prog_anual = Input::get("programado_anual");
      $tabla->fecha_inicio = Input::get("fecha_inicio");
      $tabla->fecha_fin = Input::get("fecha_culminacion");
      $tabla->nb_responsable = Input::get("responsable");
      $tabla->de_desvio = Input::get("desvio");
      $tabla->in_cargado = false;
      $tabla->in_activo = true;
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
