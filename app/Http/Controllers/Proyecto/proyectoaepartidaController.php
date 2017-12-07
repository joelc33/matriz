<?php

namespace matriz\Http\Controllers\Proyecto;
//*******agregar esta linea******//
use matriz\Models\Proyecto\tab_proyecto_ae_partida;
use matriz\Models\Proyecto\tab_proyecto_ae;
use matriz\Models\Proyecto\tab_proyecto;
use matriz\Models\Proyecto\tmp_proyecto_ae_partida;
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

class proyectoaepartidaController extends Controller
{
  protected $tab_proyecto_ae_partida;

  public function __construct(tab_proyecto_ae_partida $tab_proyecto_ae_partida)
  {
    $this->middleware('auth');
    $this->tab_proyecto_ae_partida = $tab_proyecto_ae_partida;
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

          $borrar_pr_ae_partida = tmp_proyecto_ae_partida::where('id_proyecto' , '=', Input::get('id_proyecto'))->delete();

          $update_ae_partida = DB::select( DB::raw("UPDATE t42_proyecto_acc_espec_partida SET edo_reg=false
      				FROM t39_proyecto_acc_espec
      				WHERE t42_proyecto_acc_espec_partida.co_proyecto_acc_espec = t39_proyecto_acc_espec.co_proyecto_acc_espec
      				AND id_proyecto = :proyecto;"), array( 'proyecto' => Input::get('id_proyecto')));

          foreach($abecedario as $abc){
            $contenido = get_cell($abc.'9', $objPHPExcel);
              if($contenido!=''||$contenido!=null){
                $contador = $contador+1;
                $tx_codigo = str_pad($contador, 4, '0', STR_PAD_LEFT);

                $delete_cursor = DB::select( DB::raw("DROP TABLE IF EXISTS a_cursor;"));

                $sql_secuencia = DB::select( DB::raw("SELECT tx_codigo, lpad((row_number() OVER (ORDER BY tx_codigo))::text, 4, '0') as num_tabla,
            sp_verificar_hijo_ae(co_proyecto_acc_espec) as in_foraneo into temp a_cursor
            FROM t39_proyecto_acc_espec
            WHERE edo_reg is true and id_proyecto = :proyecto and
            sp_verificar_hijo_ae(co_proyecto_acc_espec) is true order by 1 asc;"), array( 'proyecto' => Input::get('id_proyecto')));

            $select_cursor = DB::select( DB::raw("select num_tabla,tx_codigo, in_foraneo from a_cursor where in_foraneo is false and num_tabla = :codigo;"), array( 'codigo' => $tx_codigo));

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
                    'proyecto' => Input::get('id_proyecto'),
                    'partida' => $partidaCrear,
                    //'aplicacion' => $cellValue6,
                    'monto' => floatval($cellValue8)
                  );

                  $validador = Validator::make($datos, tab_proyecto_ae_partida::$validar_campo, $mensajes);

                  if ($validador->fails()) {
                    $data = json_encode(array('success' => false, 'msg' => $validador->getMessageBag()->toArray()));
                    $response = Response::make($data);
                    $response->header('Content-Type', 'text/html');
                    return $response;
                  }

                    $partida = new tmp_proyecto_ae_partida;
                    $partida->id_proyecto = Input::get('id_proyecto');
                    $partida->id_tab_ejercicio_fiscal = Session::get('ejercicio');
                    $partida->tx_codigo = $select_cursor[0]->tx_codigo;
                    $partida->tx_pa = $cellValue1;
                    $partida->tx_ge = $cellValue2;
                    $partida->tx_es = $cellValue3;
                    $partida->tx_se = $cellValue4;
                    //$partida->tx_sse = $cellValue5;
                    //$partida->nu_aplicacion = $cellValue6;
                    $partida->tx_denominacion = $cellValue5;
                    $partida->nu_monto = floatval($cellValue8);
                    $partida->edo_reg = TRUE;
                    $partida->save();

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

  /**
   * Show the form for creating a new resource.
   *
   * @return Response
   */
  public function procesarIndividual()
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

          DB::beginTransaction();
          try {

          $borrar_pr_ae_partida = tmp_proyecto_ae_partida::where('id_proyecto' , '=', Input::get('id_proyecto'))->delete();

          $update_ae_partida = tab_proyecto_ae_partida::where('co_proyecto_acc_espec', '=', Input::get('co_proyecto_acc_espec'))
          ->update(array('edo_reg' => FALSE));

          $contenido = get_cell('F9', $objPHPExcel);
          if($contenido!=''||$contenido!=null){
              $contador = $contador+1;
              $tx_codigo = Input::get('tx_codigo');

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
                  $cellValue8 = get_cell("F".$v, $objPHPExcel);
                }

                if ($cellValue8>0) {

                  $mensajes = array(
                    'monto.regex'=>'En la celda: F'.$v.' el monto no debe poseer decimales.',
                    //'aplicacion.required'=>'Para la celda: '.$abc.$v.' el campo Aplicacion es requerido.',
                    //'aplicacion.exists'=>'Para la celda: '.$abc.$v.' el codigo de aplicacion no existe por favor verificar.',
                    'partida.exists'=>'Para la celda: F'.$v.' el codigo de partida no existe por favor verificar.'
                  );

                  //$partidaCrear = $cellValue1.$cellValue2.$cellValue3.$cellValue4.$cellValue5;
                  $partidaCrear = $cellValue1.$cellValue2.$cellValue3.$cellValue4;

                  $datos = array(
                    'proyecto' => Input::get('id_proyecto'),
                    'partida' => $partidaCrear,
                    //'aplicacion' => $cellValue6,
                    'monto' => floatval($cellValue8)
                  );

                  $validador = Validator::make($datos, tab_proyecto_ae_partida::$validar_campo, $mensajes);

                  if ($validador->fails()) {
                    $data = json_encode(array('success' => false, 'msg' => $validador->getMessageBag()->toArray()));
                    $response = Response::make($data);
                    $response->header('Content-Type', 'text/html');
                    return $response;
                  }

                    $partida = new tmp_proyecto_ae_partida;
                    $partida->id_proyecto = Input::get('id_proyecto');
                    $partida->id_tab_ejercicio_fiscal = Session::get('ejercicio');
                    $partida->tx_codigo = $tx_codigo;
                    $partida->tx_pa = $cellValue1;
                    $partida->tx_ge = $cellValue2;
                    $partida->tx_es = $cellValue3;
                    $partida->tx_se = $cellValue4;
                    //$partida->tx_sse = $cellValue5;
                    //$partida->nu_aplicacion = $cellValue6;
                    $partida->tx_denominacion = $cellValue5;
                    $partida->nu_monto = floatval($cellValue8);
                    $partida->edo_reg = TRUE;
                    $partida->save();

                }
              }
            }

          DB::commit();

          $data = json_encode(array('success' => true, 'msg' => 'Archivo procesado exitosamente!<br>Se leyeron '.$end_v.' Filas.'));
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
