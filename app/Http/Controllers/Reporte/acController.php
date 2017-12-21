<?php

namespace matriz\Http\Controllers\Reporte;
//*******agregar esta linea******//
use matriz\Models\Ac\tab_ac_ae;
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
    $pdf->ln(0);
    $pdf->writeHTMLCell(205,0, '', '', 'PR'.'-'.$pdf->getAliasNumPage().'/'.$pdf->getAliasNbPages(), 0, 0, 0, true, 'R', true);
    $pdf->ln(0);
    $pdf->writeHTMLCell(205,0, '', '', 'Palacio de los Cóndores, Plaza Bolívar, Maracaibo, Estado Zulia, Venezuela', 0, 0, 0, true, 'C', true);
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
  public function __construct()
  {
    $this->middleware('auth');
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

}
