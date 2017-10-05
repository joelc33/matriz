<?php

namespace matriz\Http\Controllers\Ac;
//*******agregar esta linea******//
use matriz\Models\Ac\tab_ac_ae_partida;
use matriz\Models\Mantenimiento\tab_ac_ae_partida as mmt_ac_ae_partida;
use matriz\Models\Mantenimiento\tab_ac_ae_predefinida;
use matriz\Models\Ac\tab_ac_ae;
use matriz\Models\Ac\tab_ac;
use View;
use Validator;
use Input;
use Response;
use DB;
use Session;
use PHPExcel_IOFactory;
//*******************************//
use Illuminate\Http\Request;

use matriz\Http\Requests;
use matriz\Http\Controllers\Controller;

class acaepartidaController extends Controller
{
  protected $tab_ac_ae_partida;

  public function __construct(tab_ac_ae_partida $tab_ac_ae_partida)
  {
    $this->middleware('auth');
    $this->tab_ac_ae_partida = $tab_ac_ae_partida;
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
      $limit  = Input::get('limit', 30);
      $variable = Input::get('variable');
      $ac = Input::get('id_accion_centralizada');
      $ae = Input::get('id_accion_especifica');

      $tab_ac_ae_partida = $this->tab_ac_ae_partida
      //->join('mantenimiento.tab_partidas as t01','t01.co_partida','=','public.t54_ac_ae_partidas.co_partida')
      ->join('mantenimiento.tab_partidas as t01', function ($j) {
        $j->on('t01.co_partida','=','public.t54_ac_ae_partidas.co_partida')
          ->on('t01.id_tab_ejercicio_fiscal','=','public.t54_ac_ae_partidas.id_tab_ejercicio_fiscal');
      })
      ->select( 'public.t54_ac_ae_partidas.co_partida', 'tx_nombre', 'monto' )
      ->where('id_accion_centralizada', '=', $ac)
      ->where('id_accion', '=', $ae);

      if (Input::get("BuscarBy")=="true") {

        if($variable!=""){
          $tab_ac_ae_partida->where('public.t54_ac_ae_partidas.co_partida', 'ILIKE', "%$variable%");
        }

        $response['success']  = 'true';
        $response['total'] = $tab_ac_ae_partida->count();
        $tab_ac_ae_partida->skip($start)->take($limit);
        $response['data']  = $tab_ac_ae_partida->orderby('public.t54_ac_ae_partidas.co_partida','ASC')->get()->toArray();
      } else {
        $response['success']  = 'true';
        $response['total'] = $tab_ac_ae_partida->count();
        $tab_ac_ae_partida->skip($start)->take($limit);
        $response['data']  = $tab_ac_ae_partida->orderby('public.t54_ac_ae_partidas.co_partida','ASC')->get()->toArray();
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
  public function procesarMasivo()
  {

    $file = Input::file('archivo');

    $validator = Validator::make(
      ['file'      => $file, 'extension' => strtolower($file->getClientOriginalExtension()),],
      ['file'=> 'required', 'extension' => 'required|in:xls,xlsx', ]
    );

    if ($validator->fails()){
      $data = json_encode(array('success' => false, 'msg' => $validator->getMessageBag()->toArray()));
      $response = Response::make($data);
      $response->header('Content-Type', 'text/html');
      return $response;
    }else{
      try {
        //*************Inicio de Carga Masiva*************//
        $path = Input::file('archivo')->getRealPath();

        //Funciones extras
        function get_cell($cell, $objPHPExcel){
          //seleccionar una celda
          $objCell = ($objPHPExcel->getActiveSheet()->getCell($cell));
          //tomar valor de la celda
          return $objCell->getvalue();
        }

        function pp(&$var){
          $var = chr(ord($var)+1);
          return true;
        }

        if(strtolower($file->getClientOriginalExtension()) == 'xls')
        {
          // Extension excel 97
          $ext = 'Excel5';
        }
        elseif(strtolower($file->getClientOriginalExtension()) == 'xlsx')
        {
          // Extension excel 2007 y 2010
          $ext = 'Excel2007';
        }

        //creando el lector
    		$objReader = PHPExcel_IOFactory::createReader($ext);

    		//cargamos el archivo
    		$objPHPExcel = $objReader->load($path);

    		$dim = $objPHPExcel->getActiveSheet()->calculateWorksheetDimension();

    		// list coloca en array $start y $end
    		list($start, $end) = explode(':', $dim);

    		if(!preg_match('#([A-Z]+)([0-9]+)#', $start, $rslt)){
    			return false;
    		}
    		list($start, $start_h, $start_v) = $rslt;
    		if(!preg_match('#([A-Z]+)([0-9]+)#', $end, $rslt)){
    			return false;
    		}
    		list($end, $end_h, $end_v) = $rslt;

          $contador = 0;
          $abecedario = range('F', 'Z');

          DB::beginTransaction();
          try {

          $borrar_ac_ae_partida = tab_ac_ae_partida::where('id_accion_centralizada' , '=', Input::get('accion_centralizada'))->delete();

          foreach($abecedario as $abc){
            $contenido = get_cell($abc.'9', $objPHPExcel);
              if($contenido!=''||$contenido!=null){
                $contador = $contador+1;

                $consulta_ae = tab_ac_ae::select( 'id_accion')
                ->join('mantenimiento.tab_ac_ae_predefinida as t01','t01.id','=','public.t47_ac_accion_especifica.id_accion')
                ->where('id_accion_centralizada', '=', Input::get('accion_centralizada'))
                ->where('nu_numero', '=', $contador)
                ->first();

              //empieza  lectura vertical
              $start_v=10;
              $end_v=1923;

              for($v=$start_v; $v<=$end_v; $v++){
                //empieza lectura horizontal
                for($h=$start_h; ord($h)<=ord($end_h); pp($h)){
                  $cellValue1 = get_cell("A".$v, $objPHPExcel);
                  $cellValue2 = get_cell("B".$v, $objPHPExcel);
                  $cellValue3 = get_cell("C".$v, $objPHPExcel);
                  $cellValue4 = get_cell("D".$v, $objPHPExcel);
                  $cellValue5 = get_cell("E".$v, $objPHPExcel);
                  //$cellValue6 = get_cell("F".$v, $objPHPExcel);
                  //$cellValue7 = get_cell("G".$v, $objPHPExcel);
                  $cellValue8 = get_cell($abc.$v, $objPHPExcel);
                }

                if ($cellValue8>0) {

                  $mensajes = array(
                    'monto.regex'=>'En la celda: '.$abc.$v.' el monto no debe poseer decimales.',
                    //'aplicacion.required'=>'Para la celda: '.$abc.$v.' el campo Aplicacion es requerido.',
                    //'aplicacion.exists'=>'Para la celda: '.$abc.$v.' el codigo de aplicacion no existe por favor verificar.',
                    'partida.exists'=>'Para la celda: '.$abc.$v.' el codigo de partida no existe por favor verificar.'
                  );

                  //$partidaCrear = $cellValue1.$cellValue2.$cellValue3.$cellValue4.$cellValue5;
                  $partidaCrear = $cellValue1.$cellValue2.$cellValue3.$cellValue4;

                  $datos = array(
                    'accion_centralizada' => Input::get('accion_centralizada'),
                    'accion_especifica' => $consulta_ae->id_accion,
                    'partida' => $partidaCrear,
                    //'aplicacion' => $cellValue6,
                    'monto' => floatval($cellValue8)
                  );

                  $validador = Validator::make($datos, tab_ac_ae_partida::$validar_campo, $mensajes);

                  if ($validador->fails()) {
                    $data = json_encode(array('success' => false, 'msg' => $validador->getMessageBag()->toArray()));
                    $response = Response::make($data);
                    $response->header('Content-Type', 'text/html');
                    return $response;
                  }

                    if (mmt_ac_ae_partida::where('id_tab_ac_ae_predefinida', '=', $consulta_ae->id_accion)
                    ->where('nu_partida', '=', $partidaCrear)
                    ->where('in_activo', '=', true)
                    ->exists()) {

                    }else {

                      $validar_ae = tab_ac_ae_predefinida::select( 'id', 'nu_numero', 'de_nombre')
                      ->where('id', '=', $consulta_ae->id_accion)
                      ->first();

                      $data = json_encode(array('success' => false, 'msg' => array('ERROR:'=> 'Para la celda: '.$abc.$v.' la Partida: '.$partidaCrear.', Monto: '.$cellValue8.', No se encuentra dentro de las partidas admitidas para: <br>'.$validar_ae->nu_numero.' - '.$validar_ae->de_nombre)));
                      $response = Response::make($data);
                      $response->header('Content-Type', 'text/html');
                      return $response;

                    }

                    $partida = new tab_ac_ae_partida;
                    $partida->id_accion_centralizada = Input::get('accion_centralizada');
                    $partida->id_accion = $consulta_ae->id_accion;
                    $partida->id_tab_ejercicio_fiscal = Session::get('ejercicio');
                    //$partida->nu_aplicacion = $cellValue6;
                    $partida->co_partida = $partidaCrear;
                    $partida->monto = floatval($cellValue8);
                    $partida->edo_reg = TRUE;
                    $partida->save();

                    $calculo_ac_ae = tab_ac_ae::select(DB::raw("calcular_monto(id_accion_centralizada, id_accion) as nu_monto"))
                    ->where('id_accion_centralizada', '=', Input::get('accion_centralizada'))
                    ->where('id_accion', '=', $consulta_ae->id_accion)
                    ->first();

                    $ac_ae = tab_ac_ae::updateOrCreate(array('id_accion_centralizada' => Input::get('accion_centralizada'), 'id_accion' => $consulta_ae->id_accion));
                    $ac_ae->monto_calc = $calculo_ac_ae->nu_monto;
                    $ac_ae->save();

                    $calculo_ac = tab_ac::select(DB::raw("calcular_monto(id) as nu_monto"))
                    ->where('id', '=', Input::get('accion_centralizada'))
                    ->first();

                    $ac = tab_ac::find(Input::get('accion_centralizada'));
                    $ac->monto_calc = $calculo_ac_ae->nu_monto;
                    $ac->save();

                }
              }
            }
          }

          DB::commit();

          $data = json_encode(array('success' => true, 'msg' => 'Archivo procesado exitosamente!'));
          $response = Response::make($data);
          $response->header('Content-Type', 'text/html');
          return $response;

        }catch (\Illuminate\Database\QueryException $e)
        {
          DB::rollback();

          $data = json_encode(array('success' => false, 'msg' => array('ERROR ('.$e->getCode().'):'=> $e->getMessage())));
          $response = Response::make($data);
          $response->header('Content-Type', 'text/html');
          return $response;
        }

      } catch (\Exception $e) {
        $data = json_encode(array('success' => false, 'msg' => array('ERROR ('.$e->getCode().'):'=> $e->getMessage())));
        $response = Response::make($data);
        $response->header('Content-Type', 'text/html');
        return $response;
      }
    }
  }
}
