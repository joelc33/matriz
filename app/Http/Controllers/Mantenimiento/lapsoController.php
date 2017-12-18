<?php

namespace matriz\Http\Controllers\Mantenimiento;
//*******agregar esta linea******//
use matriz\Models\Mantenimiento\tab_lapso;
use View;
use Validator;
use Input;
use Response;
use DB;
//*******************************//
use Illuminate\Http\Request;

use matriz\Http\Requests;
use matriz\Http\Controllers\Controller;

class lapsoController extends Controller
{
  protected $tab_lapso;

  public function __construct(tab_lapso $tab_lapso)
  {
    $this->middleware('auth');
    $this->tab_lapso = $tab_lapso;
  }

  /**
  * Display a listing of the resource.
  *
  * @return Response
  */
  public function lista()
  {
    return View::make('mantenimiento.lapso.lista');
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

      $tab_lapso = $this->tab_lapso
      ->join('mantenimiento.tab_periodo as t01','t01.id','=','mantenimiento.tab_lapso.id_tab_periodo')
      ->select( 'mantenimiento.tab_lapso.id', 'id_tab_ejercicio_fiscal', 'de_periodo', 'nu_lapso', 'de_lapso',
      DB::raw("to_char(fe_inicio, 'dd/mm/YYYY') as fe_inicio"),
      DB::raw("to_char(fe_fin, 'dd/mm/YYYY') as fe_fin"), 'mantenimiento.tab_lapso.in_activo' );

      if (Input::get("BuscarBy")=="true") {

        if($variable!=""){
          $tab_lapso->where('de_periodo', 'ILIKE', "%$variable%");
        }

        $response['success']  = 'true';
        $response['total'] = $tab_lapso->count();
        $tab_lapso->skip($start)->take($limit);
        $response['data']  = $tab_lapso->orderby('mantenimiento.tab_lapso.id','ASC')->get()->toArray();
      } else {
        $response['success']  = 'true';
        $response['total'] = $tab_lapso->count();
        $tab_lapso->skip($start)->take($limit);
        $response['data']  = $tab_lapso->orderby('mantenimiento.tab_lapso.id','ASC')->get()->toArray();
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
    return View::make('mantenimiento.lapso.editar')->with('data',$data);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return Response
   */
  public function editar($id)
  {
    $data = tab_lapso::select('id', 'id_tab_ejercicio_fiscal', 'id_tab_periodo', 'nu_lapso', 'fe_inicio',
       'fe_fin', 'in_activo', 'de_lapso')
    ->where('id', '=', $id)
    ->first();
    return View::make('mantenimiento.lapso.editar')->with('data',$data);
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
      $validator= Validator::make(Input::all(), tab_lapso::$validarEditar);
      if ($validator->fails()){
        return Response::json(array(
          'success' => false,
          'msg' => $validator->getMessageBag()->toArray()
        ));
      }
      $tabla = tab_lapso::find($id);
      $tabla->id_tab_ejercicio_fiscal = Input::get("ejercicio");
      $tabla->id_tab_periodo = Input::get("periodo");
      $tabla->fe_inicio = Input::get("fecha_inicio");
      $tabla->fe_fin = Input::get("fecha_cierre");
      $tabla->de_lapso = Input::get("descripcion");
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
      $validator = Validator::make(Input::all(), tab_lapso::$validarCrear);
      if ($validator->fails()){
        return Response::json(array(
          'success' => false,
          'msg' => $validator->getMessageBag()->toArray()
        ));
      }
      $tabla = new tab_lapso;
      $tabla->id_tab_ejercicio_fiscal = Input::get("ejercicio");
      $tabla->id_tab_periodo = Input::get("periodo");
      $tabla->nu_lapso = 1;
      $tabla->fe_inicio = Input::get("fecha_inicio");
      $tabla->fe_fin = Input::get("fecha_cierre");
      $tabla->de_lapso = Input::get("descripcion");
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
   * Show the form for creating a new resource.
   *
   * @return Response
   */
  public function eliminar()
  {
    DB::beginTransaction();
    try {
      $tabla = tab_lapso::find(Input::get("id"));
      $tabla->in_activo = 'FALSE';
      $tabla->save();
      DB::commit();

      $response['success']  = 'true';
      $response['msg']  = 'Registro Deshabilitado con Exito!';
      return Response::json($response, 200);

    }catch (\Illuminate\Database\QueryException $e)
    {
      DB::rollback();

      $response['success']  = 'false';
      $response['msg']  = array('ERROR ('.$e->getCode().'):'=> $e->getMessage());
      return Response::json($response, 200);
    }
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return Response
   */
  public function habilitar()
  {
    DB::beginTransaction();
    try {
      $tabla = tab_lapso::find(Input::get("id"));
      $tabla->in_activo = 'TRUE';
      $tabla->save();
      DB::commit();

      $response['success']  = 'true';
      $response['msg']  = 'Registro Habilitado con Exito!';
      return Response::json($response, 200);

    }catch (\Illuminate\Database\QueryException $e)
    {
      DB::rollback();

      $response['success']  = 'false';
      $response['msg']  = array('ERROR ('.$e->getCode().'):'=> $e->getMessage());
      return Response::json($response, 200);
    }
  }

}
