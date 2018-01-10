<?php

namespace matriz\Http\Controllers\Reporte;
//*******agregar esta linea******//
use matriz\Models\Ac\tab_ac_ae;
use matriz\Models\Ac\tab_meta_financiera;
use View;
use Validator;
use Input;
use Response;
use DB;
use Session;
use TCPDF;
//*******************************//
use Illuminate\Http\Request;

use matriz\Http\Requests;
use matriz\Http\Controllers\Controller;

//*******clase extendida TCPDF******//
class ReportePDF extends TCPDF {

  function encabezado($pdf){
    $pdf->Image(public_path().'/images/zulia_escudo_negro.png', 15, 3, 20, 16, 'PNG', '', '', true, 150, '', false, false, 0, false, false, false);
    $pdf->setXY(35,7);
    $pdf->SetFont('','B',11);
    $pdf->MultiCell(190, 5, 'GOBERNACIÓN DEL ESTADO ZULIA', 0, 'L', 0, 0, '', '', true);
    $pdf->setXY(35,14);
    $pdf->MultiCell(190, 5, 'PLAN OPERATIVO ANUAL '.Session::get('ejercicio'), 0, 'L', 0, 0, '', '', true);
    $pdf->setY(23);
    return $pdf;
  }

  function pie($pdf){
    $pdf->setXY(10,-10);
    $pdf->SetFont('','',8);
    $pdf->ln(0);
    $pdf->writeHTMLCell(200,0, '', '', 'AC'.'-'.$pdf->getAliasNumPage().'/'.$pdf->getAliasNbPages(), 0, 0, 0, true, 'R', true);
    $pdf->ln(0);
    $pdf->writeHTMLCell(190,0, '', '', 'Palacio de los Cóndores, Plaza Bolívar, Maracaibo, Estado Zulia, Venezuela', 0, 0, 0, true, 'C', true);
    return $pdf;
  }

  public function Footer()
  {
    self::pie($this);
  }

  public function Header()
  {
    self::encabezado($this);
  }
}

class acController extends Controller
{
  protected $tab_meta_financiera;

  public function __construct(tab_meta_financiera $tab_meta_financiera)
  {
    $this->middleware('auth');
    $this->tab_meta_financiera = $tab_meta_financiera;
  }
  /**
  * Display a listing of the resource.
  *
  * @return Response
  */
  public function lista()
  {
    $data = json_encode(array("id_ejecutor" => Session::get('ejecutor')));
    return View::make('reporte.poa.ac')->with('data',$data);
  }

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function resumen()
  {

    /***distribucion***/
    $htmlReporte = '
    <!-- Tabla 1 -->
    <table border="0.1" style="width:100%" style="font-size:9px" cellpadding="3">
    <thead>
    <tr align="left" bgcolor="#E6E6E6">
    <th colspan="5" style="width: 100%;"><b>RESUMEN DE ACCIONES CENTRALIZADAS POA: '.Session::get('ejercicio').' </b></th>
    </tr>
    <tr style="font-size:8px">
    <th align="center" bgcolor="#E6E6E6" style="width: 40%;"><b>ACCIONES CENTRALIZADAS</b></th>
    <th align="center" bgcolor="#E6E6E6" style="width: 40%;"><b>ACCIONES ESPECIFICAS</b></th>
    <th align="center" bgcolor="#E6E6E6" style="width: 20%;"><b>MONTO</b></th>
    </tr>
    </thead>
    ';

    $htmlReporte.='
    <tbody>
    ';

    //Query
    $consulta1 = tab_ac_ae::join('public.t46_acciones_centralizadas as t01','t01.id','=','public.t47_ac_accion_especifica.id_accion_centralizada')
    ->join('mantenimiento.tab_ac_ae_predefinida as t02','t02.id','=','public.t47_ac_accion_especifica.id_accion')
    ->join('mantenimiento.tab_ac_predefinida as t03','t03.id','=','t01.id_accion')
    ->join('mantenimiento.tab_ejecutores as t04','t04.id_ejecutor','=','t01.id_ejecutor')
    ->select(DB::raw("'AC' || t01.id_ejecutor || t01.id_ejercicio || lpad(t01.id_accion::text, 5, '0') as id_ac"),
    DB::raw('t02.nu_numero as nu_ae'), 't03.de_nombre as de_ac', 't02.de_nombre as de_ac_ae',
    'public.t47_ac_accion_especifica.monto as mo_ae')
    ->where('t01.id_ejercicio', '=', Session::get('ejercicio') )
    ->orderBy('t01.id', 'public.t47_ac_accion_especifica.id_accion','ASC')
    ->get();

    $i = 0;
    $acumulado = 0;

    foreach ($consulta1 as $key => $value) {
    // Set cell An to the "name" column from the database (assuming you have a column called name)
      $i++;
      $acumulado = $acumulado+$value->mo_ae;
      $htmlReporte.='
      <tr style="font-size:8px" nobr="true">
        <td style="width: 40%;" align="justify">'.$value->id_ac.'-'.$value->de_ac.'</td>
        <td style="width: 40%;" align="justify">'.$value->nu_ae.'-'.$value->de_ac_ae.'</td>
        <td style="width: 20%;">'.number_format($value->mo_ae, 2, ',', '.').'</td>
      </tr>';

    }

    $htmlReporte.='
    <tr style="font-size:8px" nobr="true">
      <td style="width: 80%;" align="right"><b>TOTAL</b></td>
      <td style="width: 20%;">'.number_format($acumulado, 2, ',', '.').'</td>
    </tr>';

    $htmlReporte.='
    </tbody>
    </table>';

    $pdf = new ReportePDF("P", PDF_UNIT, 'Letter', true, 'UTF-8', false);
    $pdf->SetCreator('Yoser Perez');
    $pdf->SetAuthor('POA, SPE');
    $pdf->SetTitle('Reporte');
    $pdf->SetSubject('Reporte');
    $pdf->SetKeywords('Planilla, PDF, SPE');
    $pdf->SetMargins(10,10,10);
    $pdf->SetTopMargin(20);
    $pdf->SetPrintHeader(true);
    $pdf->SetPrintFooter(true);
    // set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, 15);
    $pdf->AddPage();
    //Cuerpo de la planilla
    $pdf->writeHTML($htmlReporte, true, false, false, false, '');
    $pdf->lastPage();
    $pdf->output('AC_RESUMEN_'.date("H:i:s").'.pdf', 'D');

  }

  /**
  * Display a listing of the resource.
  *
  * @return Response
  */
  public function ubica()
  {
    $data = json_encode(array("id_ejecutor" => Session::get('ejecutor')));
    return View::make('reporte.poa.ubicaac')->with('data',$data);
  }

  /**
  * Display a listing of the resource.
  *
  * @return Response
  */
  public function ubicacion()
  {

    try {

    //Query
    $tab_meta_financiera = $this->tab_meta_financiera
    ->join('t69_metas_ac as t69','t69.co_metas','=','t70_metas_ac_detalle.co_metas')
    ->join('t47_ac_accion_especifica as t47', function ($j) {
      $j->on('t47.id_accion_centralizada','=','t69.id_accion_centralizada')
        ->on('t47.id_accion','=','t69.co_ac_acc_espec');
    })
    ->join('t46_acciones_centralizadas as t46','t46.id','=','t47.id_accion_centralizada')
    ->join('mantenimiento.tab_ejecutores as t24','t24.id_ejecutor','=','t47.id_ejecutor')
    ->join('mantenimiento.tab_municipio_detalle as t13','t13.id','=','t70_metas_ac_detalle.co_municipio')
    ->join('mantenimiento.tab_fuente_financiamiento as t06','t06.id','=','t70_metas_ac_detalle.co_fuente')
    ->join('mantenimiento.tab_ac_ae_predefinida as t02', 't47.id_accion', '=', 't02.id')
    ->join('mantenimiento.tab_ac_predefinida as t03', 't46.id_accion', '=', 't03.id')
    ->select( 't03.de_nombre as de_proyecto', 'nu_numero', 't02.de_nombre as de_ae',
    DB::raw("'AC' || t46.id_ejecutor || id_ejercicio || lpad(t46.id_accion::text, 5, '0') as id_proyecto"),
    DB::raw(" t24.id_ejecutor||' - '|| tx_ejecutor as ejecutor"),
    DB::raw("t69.codigo ||' - '|| t69.nb_meta as de_actividad"),
    'de_municipio', 'mo_presupuesto', 'de_fuente_financiamiento')
    ->where('t70_metas_ac_detalle.co_municipio', '=', Input::get('id_tab_municipio'))
    ->where('t47.id_ejecutor', '=', Input::get('ejecutor'))
    ->where('t46.id_ejercicio', '=', Session::get('ejercicio'))
    ->where('t46.edo_reg', '=', true)
    ->where('t47.edo_reg', '=', true)
    ->where('t69.edo_reg', '=', true)
    ->where('t70_metas_ac_detalle.edo_reg', '=', true)
    ->orderBy(DB::raw('t46.id_accion', 't47.id_accion', 't69.codigo'), 'ASC')
    ->get();

    /*if (!empty(Input::get('fuente_financiamiento'))) {
      $consulta->where('t70_metas_ac_detalle.co_fuente', '=', Input::get('fuente_financiamiento'));
    }

    if (!empty(Input::get('ejecutor'))) {
      $consulta->where('t47.id_ejecutor', '=', Input::get('ejecutor'));
    }*/

    /*->when(Input::get('fuente_financiamiento'), function ($query) {
      return $query->where('t68_metas_detalle.co_fuente', '=', Input::get('fuente_financiamiento'));
    })
    ->when(Input::get('ejecutor'), function ($query) {
      return $query->where('t24.id_ejecutor', '=', Input::get('ejecutor'));
    })*/

    /*$tab_meta_financiera->orderBy(DB::raw('t46.id_accion', 't47.id_accion', 't69.codigo'), 'ASC')
    ->get();*/

    /***distribucion fisica***/
    $htmlReporte = '
    <!-- Tabla 1 -->
    <table border="0.1" style="width:100%" style="font-size:9px" cellpadding="3">
    <thead>
    <tr align="center" bgcolor="#BDBDBD">
    <th colspan="6" style="width: 100%;"><b>DISTRIBUCIÓN DE ACCION CENTRALIZADA POR MUNICIPIO - AÑO '.Session::get('ejercicio').'</b></th>
    </tr>
    <tr style="font-size:6px">
    <th align="center" bgcolor="#BDBDBD" style="width: 10%;">COD. AC</th>
    <th align="center" bgcolor="#BDBDBD" style="width: 15%;">DESCRIPCION AC</th>
    <th align="center" bgcolor="#BDBDBD" style="width: 15%;">ACCION ESPECIFICA</th>
    <th align="center" bgcolor="#BDBDBD" style="width: 15%;">ENTE EJECUTOR RESPONSABLE</th>
    <th align="center" bgcolor="#BDBDBD" style="width: 15%;">ACTIVIDAD</th>
    <th align="center" bgcolor="#BDBDBD" style="width: 10%;">MUNICIPIO</th>
    <th align="center" bgcolor="#BDBDBD" style="width: 10%;">MONTO</th>
    <th align="center" bgcolor="#BDBDBD" style="width: 10%;">FUENTE FINANCIAMIENTO</th>
    </tr>
    </thead>
    ';

    $htmlReporte.='
    <tbody>
    ';

    		foreach ($tab_meta_financiera as $key => $value) {
    		// Set cell An to the "name" column from the database (assuming you have a column called name)

    			$htmlReporte.='
    			<tr style="font-size:7px" nobr="true">
    				<td style="width: 10%;">'.$value->id_proyecto.'</td>
    				<td style="width: 15%;" align="justify">'.$value->de_proyecto.'</td>
    				<td style="width: 15%;" align="justify">'.$value->nu_numero.' - '.$value->de_ae.'</td>
    				<td style="width: 15%;" align="justify">'.$value->ejecutor.'</td>
    				<td style="width: 15%;" align="justify">'.$value->de_actividad.'</td>
    				<td style="width: 10%;" align="center">'.$value->de_municipio.'</td>
    				<td style="width: 10%;">'.number_format($value->mo_presupuesto, 2, ',', '.').'</td>
    				<td style="width: 10%;" align="center">'.$value->de_fuente_financiamiento.'</td>
    			</tr>';

    		}

    $htmlReporte.='
    </tbody>
    </table>';

    $pdf = new ReportePDF("L", PDF_UNIT, 'Letter', true, 'UTF-8', false);
    $pdf->SetCreator('Yoser Perez');
    $pdf->SetAuthor('POA, SPE');
    $pdf->SetTitle('ACCION CENTRALIZADA - UBICACIÓN GEOGRAFICA');
    $pdf->SetSubject('Reporte');
    $pdf->SetKeywords('Planilla, PDF, SPE');
    $pdf->SetMargins(10,10,10);
    $pdf->SetTopMargin(20);
    $pdf->SetPrintHeader(true);
    $pdf->SetPrintFooter(true);
    // set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, 15);
    $pdf->AddPage();
    //Cuerpo de la planilla
    $pdf->writeHTML($htmlReporte, true, false, false, false, '');
    $pdf->lastPage();
    $pdf->output('UBICACION_AC_'.Session::get('ejercicio').'_'.date("H:i:s").'.pdf', 'D');

    DB::commit();

  }catch (\Illuminate\Database\QueryException $e)
  {
    DB::rollback();
    header('Content-Type: text/html');
    echo json_encode(array(
      'success' => false,
      'msg' => array('ERROR ('.$e->getCode().'):'=> $e->getMessage())
      //'msg' => array('ERROR ('.$e->getCode().'):'=> 'CODIGO['.$e->getCode().']: Error en Transaccion, verfique e intente de nuevo.')
    ));
  }

}

/**
* Display a listing of the resource.
*
* @return Response
*/
public function ubicacionTodo()
{

  try {

  //Query
  $tab_meta_financiera = $this->tab_meta_financiera
  ->join('t69_metas_ac as t69','t69.co_metas','=','t70_metas_ac_detalle.co_metas')
  ->join('t47_ac_accion_especifica as t47', function ($j) {
    $j->on('t47.id_accion_centralizada','=','t69.id_accion_centralizada')
      ->on('t47.id_accion','=','t69.co_ac_acc_espec');
  })
  ->join('t46_acciones_centralizadas as t46','t46.id','=','t47.id_accion_centralizada')
  ->join('mantenimiento.tab_ejecutores as t24','t24.id_ejecutor','=','t47.id_ejecutor')
  ->join('mantenimiento.tab_municipio_detalle as t13','t13.id','=','t70_metas_ac_detalle.co_municipio')
  ->join('mantenimiento.tab_fuente_financiamiento as t06','t06.id','=','t70_metas_ac_detalle.co_fuente')
  ->join('mantenimiento.tab_ac_ae_predefinida as t02', 't47.id_accion', '=', 't02.id')
  ->join('mantenimiento.tab_ac_predefinida as t03', 't46.id_accion', '=', 't03.id')
  ->select( 't03.de_nombre as de_proyecto', 'nu_numero', 't02.de_nombre as de_ae',
  DB::raw("'AC' || t46.id_ejecutor || id_ejercicio || lpad(t46.id_accion::text, 5, '0') as id_proyecto"),
  DB::raw(" t24.id_ejecutor||' - '|| tx_ejecutor as ejecutor"),
  DB::raw("t69.codigo ||' - '|| t69.nb_meta as de_actividad"),
  'de_municipio', 'mo_presupuesto', 'de_fuente_financiamiento')
  ->where('t46.id_ejercicio', '=', Session::get('ejercicio'))
  ->where('t46.edo_reg', '=', true)
  ->where('t47.edo_reg', '=', true)
  ->where('t69.edo_reg', '=', true)
  ->where('t70_metas_ac_detalle.edo_reg', '=', true)
  ->orderBy(DB::raw('t46.id_accion', 't47.id_accion', 't69.codigo'), 'ASC')
  ->get();

  /***distribucion fisica***/
  $htmlReporte = '
  <!-- Tabla 1 -->
  <table border="0.1" style="width:100%" style="font-size:9px" cellpadding="3">
  <thead>
  <tr align="center" bgcolor="#BDBDBD">
  <th colspan="6" style="width: 100%;"><b>DISTRIBUCIÓN DE ACCION CENTRALIZADA POR MUNICIPIO - AÑO '.Session::get('ejercicio').'</b></th>
  </tr>
  <tr style="font-size:6px">
  <th align="center" bgcolor="#BDBDBD" style="width: 10%;">COD. AC</th>
  <th align="center" bgcolor="#BDBDBD" style="width: 15%;">DESCRIPCION AC</th>
  <th align="center" bgcolor="#BDBDBD" style="width: 15%;">ACCION ESPECIFICA</th>
  <th align="center" bgcolor="#BDBDBD" style="width: 15%;">ENTE EJECUTOR RESPONSABLE</th>
  <th align="center" bgcolor="#BDBDBD" style="width: 15%;">ACTIVIDAD</th>
  <th align="center" bgcolor="#BDBDBD" style="width: 10%;">MUNICIPIO</th>
  <th align="center" bgcolor="#BDBDBD" style="width: 10%;">MONTO</th>
  <th align="center" bgcolor="#BDBDBD" style="width: 10%;">FUENTE FINANCIAMIENTO</th>
  </tr>
  </thead>
  ';

  $htmlReporte.='
  <tbody>
  ';

      foreach ($tab_meta_financiera as $key => $value) {
      // Set cell An to the "name" column from the database (assuming you have a column called name)

        $htmlReporte.='
        <tr style="font-size:7px" nobr="true">
          <td style="width: 10%;">'.$value->id_proyecto.'</td>
          <td style="width: 15%;" align="justify">'.$value->de_proyecto.'</td>
          <td style="width: 15%;" align="justify">'.$value->nu_numero.' - '.$value->de_ae.'</td>
          <td style="width: 15%;" align="justify">'.$value->ejecutor.'</td>
          <td style="width: 15%;" align="justify">'.$value->de_actividad.'</td>
          <td style="width: 10%;" align="center">'.$value->de_municipio.'</td>
          <td style="width: 10%;">'.number_format($value->mo_presupuesto, 2, ',', '.').'</td>
          <td style="width: 10%;" align="center">'.$value->de_fuente_financiamiento.'</td>
        </tr>';

      }

  $htmlReporte.='
  </tbody>
  </table>';

  $pdf = new ReportePDF("L", PDF_UNIT, 'Letter', true, 'UTF-8', false);
  $pdf->SetCreator('Yoser Perez');
  $pdf->SetAuthor('POA, SPE');
  $pdf->SetTitle('ACCION CENTRALIZADA - UBICACIÓN GEOGRAFICA');
  $pdf->SetSubject('Reporte');
  $pdf->SetKeywords('Planilla, PDF, SPE');
  $pdf->SetMargins(10,10,10);
  $pdf->SetTopMargin(20);
  $pdf->SetPrintHeader(true);
  $pdf->SetPrintFooter(true);
  // set auto page breaks
  $pdf->SetAutoPageBreak(TRUE, 15);
  $pdf->AddPage();
  //Cuerpo de la planilla
  $pdf->writeHTML($htmlReporte, true, false, false, false, '');
  $pdf->lastPage();
  $pdf->output('UBICACION_AC_'.Session::get('ejercicio').'_'.date("H:i:s").'.pdf', 'D');

  DB::commit();

}catch (\Illuminate\Database\QueryException $e)
{
  DB::rollback();
  header('Content-Type: text/html');
  echo json_encode(array(
    'success' => false,
    'msg' => array('ERROR ('.$e->getCode().'):'=> $e->getMessage())
    //'msg' => array('ERROR ('.$e->getCode().'):'=> 'CODIGO['.$e->getCode().']: Error en Transaccion, verfique e intente de nuevo.')
  ));
}

}

}
