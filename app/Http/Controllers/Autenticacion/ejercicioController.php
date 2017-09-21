<?php

namespace matriz\Http\Controllers\Autenticacion;
//*******agregar esta linea******//
use matriz\Models\Mantenimiento\tab_ejercicio_fiscal;
use matriz\Models\Mantenimiento\tab_ejecutores;
use Session;
use Response;
use Validator;
use DB;
use View;
use URL;
use Input;
//*******************************//
use Illuminate\Http\Request;

use matriz\Http\Requests;
use matriz\Http\Controllers\Controller;

class ejercicioController extends Controller
{
    public function __construct()
    {
      $this->middleware('optimizar');
      $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function lista()
    {
        $data = tab_ejecutores::select('id', 'de_correo', 'de_telefono', 'in_verificado')
        ->where('id_ejecutor', '=', Session::get('ejecutor'))
        ->first();

        return View::make('autenticar.ejercicio.form')->with('data',$data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function ejercicio()
    {
      $response['success']  = 'true';
      $response['data']  = tab_ejercicio_fiscal::select('id','in_activo',
      DB::raw('mantenimiento.sp_periodo_activo(id::integer) as de_estatus')
      )->orderby('id','ASC')->get()->toArray();
      return Response::json($response, 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function seleccionar()
    {

      $data = tab_ejecutores::select('id', 'de_correo', 'de_telefono', 'in_verificado')
      ->where('id_ejecutor', '=', Session::get('ejecutor'))
      ->first();

      if($data->in_verificado==true){

        $validator= Validator::make(Input::all(), tab_ejercicio_fiscal::$seleccionar);
        if ($validator->fails()){
          return Response::json(array(
            'success' => false,
            'msg' => $validator->getMessageBag()->toArray()
          ));
        }

        Session::put('ejercicio', Input::get('ejercicio'));

        /*Uso para poa*/
        //ini_set('session.save_path',realpath(dirname(storage_path()) . '/formulacion'));
        session_start();
        $_SESSION['ejercicio_fiscal']=Input::get('ejercicio');
        session_write_close();
        /*fin*/

        return Response::json(array(
          'success' => true,
          'msg' => 'Ejercicio Seleccionado!',
          'url' => URL::to('inicio')
        ));

      }elseif($data->in_verificado==false){

        DB::beginTransaction();
        try {

        $validator= Validator::make(Input::all(), tab_ejecutores::$datosEjecutor);
        if ($validator->fails()){
          return Response::json(array(
            'success' => false,
            'msg' => $validator->getMessageBag()->toArray()
          ));
        }

        $tabla = tab_ejecutores::updateOrCreate(array('id_ejecutor' => Session::get('ejecutor')));
        $tabla->de_correo = Input::get("correo");
        $tabla->de_telefono = Input::get("telefono");
        $tabla->in_verificado = true;
        $tabla->save();

        Session::put('ejercicio', Input::get('ejercicio'));

        DB::commit();

        return Response::json(array(
          'success' => true,
          'msg' => 'Ejercicio Seleccionado!',
          'url' => URL::to('inicio')
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
