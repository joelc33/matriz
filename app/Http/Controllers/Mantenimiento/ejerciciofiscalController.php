<?php

namespace matriz\Http\Controllers\Mantenimiento;
//*******agregar esta linea******//
use matriz\Models\Mantenimiento\tab_ejercicio_fiscal;
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

class ejerciciofiscalController extends Controller
{
  protected $tab_ejercicio_fiscal;

  public function __construct(tab_ejercicio_fiscal $tab_ejercicio_fiscal)
  {
    $this->middleware('auth');
    $this->tab_ejercicio_fiscal = $tab_ejercicio_fiscal;
  }

  /**
  * Display a listing of the resource.
  *
  * @return Response
  */
  public function lista()
  {
    return View::make('mantenimiento.ejercicio.lista');
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

      $tab_ejercicio_fiscal = $this->tab_ejercicio_fiscal
      ->select( 'id', 'in_activo' );

      if (Input::get("BuscarBy")=="true") {

        if($variable!=""){
          $tab_ejercicio_fiscal->where('id', 'ILIKE', "%$variable%");
        }

        $response['success']  = 'true';
        $response['total'] = $tab_ejercicio_fiscal->count();
        $tab_ejercicio_fiscal->skip($start)->take($limit);
        $response['data']  = $tab_ejercicio_fiscal->orderby('id','DESC')->get()->toArray();
      } else {
        $response['success']  = 'true';
        $response['total'] = $tab_ejercicio_fiscal->count();
        $tab_ejercicio_fiscal->skip($start)->take($limit);
        $response['data']  = $tab_ejercicio_fiscal->orderby('id','DESC')->get()->toArray();
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
    $ejercicio = Session::get('ejercicio')+1;
    $data = json_encode(array("nu_anio" => $ejercicio));
    return View::make('mantenimiento.ejercicio.editar')
    ->with('data',$data)
    ->with('ejercicio',$ejercicio);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return Response
   */
  public function editar($id)
  {
    $data = tab_ejercicio_fiscal::select('id', 'in_activo')
    ->where('id', '=', $id)
    ->first();
    return View::make('mantenimiento.ejercicio.editar')->with('data',$data);
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
      $validator= Validator::make(Input::all(), tab_ejercicio_fiscal::$validarEditar);
      if ($validator->fails()){
        return Response::json(array(
          'success' => false,
          'msg' => $validator->getMessageBag()->toArray()
        ));
      }
      $tabla = tab_ejercicio_fiscal::find($id);
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
      $validator = Validator::make(Input::all(), tab_ejercicio_fiscal::$validarCrear);
      if ($validator->fails()){
        return Response::json(array(
          'success' => false,
          'msg' => $validator->getMessageBag()->toArray()
        ));
      }
      $tabla = new tab_ejercicio_fiscal;
      $tabla->id = Input::get("periodo");
      $tabla->in_activo = 'TRUE';
      $tabla->save();

      DB::commit();
      return Response::json(array(
        'success' => true,
        'msg' => 'Ejercicio creado con Exito!'
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
  public function habilitar()
  {
    DB::beginTransaction();
    try {
      $tabla = tab_ejercicio_fiscal::find(Input::get("periodo"));
      $tabla->in_activo = 'TRUE';
      $tabla->save();
      DB::commit();

      $response['success']  = 'true';
      $response['msg']  = 'Periodo Habilitado con Exito!';
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
  public function cerrar()
  {
    DB::beginTransaction();
    try {
      $tabla = tab_ejercicio_fiscal::find(Input::get("periodo"));
      $tabla->in_activo = 'FALSE';
      $tabla->save();
      DB::commit();

      $response['success']  = 'true';
      $response['msg']  = 'Periodo cerrado con Exito!';
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
  public function cronograma($id)
  {
    $data = tab_ejercicio_fiscal::select('id as periodo', 'in_activo')
    ->where('id', '=', $id)
    ->first();

    return View::make('mantenimiento.ejercicio.cronograma.lista')->with('data',$data);
  }
}
