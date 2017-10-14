<?php

namespace matriz\Http\Controllers\Reporte;
//*******agregar esta linea******//
use matriz\Models\Ac\tab_ac_responsable;
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
class PDFresponsable extends TCPDF {

	function encabezado($pdf){

      $pdf->Image(public_path().'/images/zulia_escudo.png', 10, 10, 20, 18, 'PNG', '', '', true, 150, '', false, false, 0, false, false, false);
      $pdf->setXY(30,15);
      $pdf->SetFont('','B',11);
      $pdf->MultiCell(190, 5, 'GOBERNACIÓN DEL ESTADO ZULIA', 0, 'L', 0, 0, '', '', true);
      $pdf->setXY(30,20);
      $pdf->MultiCell(190, 5, 'PLAN OPERATIVO ANUAL '.Session::get("ejercicio"), 0, 'L', 0, 0, '', '', true);

		return $pdf;
	}

	function pie($pdf){
		$pdf->setXY(10,-10);
		$pdf->SetFont('','',7);
		$pdf->SetTextColor(0,0,0);
		$pdf->writeHTMLCell(180,0, '', '', 'Palacio de los Cóndores, Plaza Bolívar, Maracaibo, Estado Zulia, Venezuela' , 0, 0, 0, true, 'C', true);
    $pdf->writeHTMLCell(15,0, '', '', $pdf->getAliasNumPage().'/'.$pdf->getAliasNbPages() , 0, 0, 0, true, 'C', true);

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
//*******************************//

class acresponsableController extends Controller
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
  public function responsable()
  {
		$htmlReporte = '
		<!-- Tabla 1 -->
		<table border="0.1" style="width:100%" style="font-size:9px" cellpadding="3">
		<thead>
		<tr align="left" bgcolor="#E6E6E6">
		<th colspan="5" style="width: 100%;"><b>LISTADO DE RESPONSABLES EJECUTOR: </b></th>
		</tr>
		<tr style="font-size:8px">
		<th align="center" bgcolor="#E6E6E6" style="width: 5%;"><b>XX</b></th>
		<th align="center" bgcolor="#E6E6E6" style="width: 55%;"><b>XX</b></th>
		<th align="center" bgcolor="#E6E6E6" style="width: 20%;"><b>XX</b></th>
		<th align="center" bgcolor="#E6E6E6" style="width: 20%;"><b>XX</b></th>
		</tr>
		</thead>
		';

		$htmlReporte.='
		<tbody>
		';

		$htmlReporte.='
		</tbody>
		</table>';

		$pdf = new PDFresponsable('P', PDF_UNIT, 'LETTER', true, 'UTF-8', false);
		$pdf->SetCreator('Sistema Nueva Etapa, Yoser Perez');
		$pdf->SetAuthor('Yoser Perez');
		$pdf->SetTitle('Ley de Presupuesto');
		$pdf->SetSubject('Ley de Presupuesto');
		$pdf->SetKeywords('Ley de Presupuesto, PDF, Zulia, SPE, '.Session::get("ejercicio").'');
		$pdf->SetMargins(10,10,10);
		$pdf->SetTopMargin(30);
		$pdf->SetPrintHeader(true);
		$pdf->SetPrintFooter(true);
		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, 10);
    $pdf->AddPage();
    //Cierre de Reporte
		$pdf->writeHTML($htmlReporte, true, false, false, false, '');
    $pdf->lastPage();
    $pdf->output('LISTADO_RESPONSABLES_'.Input::get('id_ejecutor').'_'.Session::get("ejercicio").'_'.date("H:i:s").'.pdf', 'D');
  }
}
