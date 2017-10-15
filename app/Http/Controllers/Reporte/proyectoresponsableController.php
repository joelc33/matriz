<?php

namespace matriz\Http\Controllers\Reporte;
//*******agregar esta linea******//
use matriz\Models\Proyecto\tab_proyecto_responsable;
use matriz\Models\Mantenimiento\tab_ejecutores;
use View;
use Validator;
use Input;
use Response;
use DB;
use Session;
use TCPDF;
use Helper;
use PHPExcel_IOFactory;
use PHPExcel;
use PHPExcel_Writer_Excel2007;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;
use PHPExcel_Cell_DataType;
use matriz\Http\Controllers\Reporte\PDFresponsable;
//*******************************//
use Illuminate\Http\Request;

use matriz\Http\Requests;
use matriz\Http\Controllers\Controller;

//*******clase extendida TCPDF******//
class PDFresponsablePR extends TCPDF {

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

class proyectoresponsableController extends Controller
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

		$ejecutor = tab_ejecutores::select('id', 'id_ejecutor', 'tx_ejecutor')
		->where('id_ejecutor', '=', Input::get('id_ejecutor'))
		->first();

		$htmlReporte = '
		<!-- Tabla 1 -->
		<table border="0.1" style="width:100%" style="font-size:9px" cellpadding="3">
		<thead>
		<tr align="left" bgcolor="#E6E6E6">
		<th colspan="5" style="width: 100%;"><b>LISTADO DE RESPONSABLES EJECUTOR: '.$ejecutor->id_ejecutor.' - '.$ejecutor->tx_ejecutor.' </b></th>
		</tr>
		<tr style="font-size:8px">
		<th align="center" bgcolor="#E6E6E6" style="width: 20%;"><b>PROYECTO</b></th>
		<th align="center" bgcolor="#E6E6E6" style="width: 20%;"><b>TITULAR</b></th>
		<th align="center" bgcolor="#E6E6E6" style="width: 20%;"><b>PLANIFICADOR</b></th>
		<th align="center" bgcolor="#E6E6E6" style="width: 20%;"><b>ADMINISTRADOR</b></th>
    <th align="center" bgcolor="#E6E6E6" style="width: 20%;"><b>TÉCNICO</b></th>
		</tr>
		</thead>
		';

		$htmlReporte.='
		<tbody>';

		$responsable = tab_proyecto_responsable::join('public.t26_proyectos as t01', 'public.t37_proyecto_responsables.id_proyecto', '=', 't01.id_proyecto')
		->select( 'co_proyecto_responsables', 'public.t37_proyecto_responsables.id_proyecto',
       'responsable_nombres', 'reponsable_cedula',
       'responsable_correo', 'responsable_telefono', 'tecnico_nombres', 'tecnico_cedula',
       'tecnico_correo', 'tecnico_telefono', 'tecnico_unidad', 'registrador_nombres',
       'registrador_cedula', 'registrador_correo', 'registrador_telefono',
       'administrador_nombres', 'administrador_cedula', 'administrador_correo',
       'administrador_telefono', 'administrador_unidad', 'nombre')
		->where('id_ejecutor', '=', Input::get('id_ejecutor'))
		->where('id_ejercicio', '=', Session::get('ejercicio'))
    ->where('t01.edo_reg', '=', TRUE)
		->orderBy('id_proyecto','ASC')
		->get();

		foreach ($responsable as $key => $value) {

		$htmlReporte.='
		<tr style="font-size:7px" nobr="true">
			<td rowspan="5" style="width: 20%;">'.$value->id_proyecto.' - '.$value->nombre.'</td>
			<td style="width: 9%;"><b>Cédula</b></td>
			<td style="width: 11%;">'.$value->reponsable_cedula.'</td>
			<td style="width: 9%;"><b>Cédula</b></td>
			<td style="width: 11%;">'.$value->registrador_cedula.'</td>
			<td style="width: 9%;"><b>Cédula</b></td>
			<td style="width: 11%;">'.$value->administrador_cedula.'</td>
      <td style="width: 9%;"><b>Cédula</b></td>
      <td style="width: 11%;">'.$value->tecnico_cedula.'</td>
		</tr>
		<tr style="font-size:7px">
			<td><b>Nombre</b></td>
			<td>'.$value->responsable_nombres.'</td>
			<td><b>Nombre</b></td>
			<td>'.$value->registrador_nombres.'</td>
			<td><b>Nombre</b></td>
			<td>'.$value->administrador_nombres.'</td>
      <td><b>Nombre</b></td>
      <td>'.$value->tecnico_nombres.'</td>
		</tr>
		<tr style="font-size:7px">
			<td><b>Unidad de Adscripción</b></td>
			<td>'.$value->responsable_unidad.'</td>
			<td><b>Unidad de Adscripción</b></td>
			<td>'.$value->registrador_unidad.'</td>
			<td><b>Unidad de Adscripción</b></td>
			<td>'.$value->administrador_unidad.'</td>
      <td><b>Unidad de Adscripción</b></td>
      <td>'.$value->tecnico_unidad.'</td>
		</tr>
		<tr style="font-size:7px">
			<td><b>Correo electrónico</b></td>
			<td>'.$value->responsable_correo.'</td>
			<td><b>Correo electrónico</b></td>
			<td>'.$value->registrador_correo.'</td>
			<td><b>Correo electrónico</b></td>
			<td>'.$value->administrador_correo.'</td>
      <td><b>Correo electrónico</b></td>
      <td>'.$value->tecnico_correo.'</td>
		</tr>
		<tr style="font-size:7px">
			<td><b>Teléfono</b></td>
			<td>'.$value->responsable_telefono.'</td>
			<td><b>Teléfono</b></td>
			<td>'.$value->registrador_telefono.'</td>
			<td><b>Teléfono</b></td>
			<td>'.$value->administrador_telefono.'</td>
      <td><b>Teléfono</b></td>
      <td>'.$value->tecnico_telefono.'</td>
		</tr>
		';
		}

		$htmlReporte.='
		</tbody>
		</table>';

		$pdf = new PDFresponsablePR('P', PDF_UNIT, 'LETTER', true, 'UTF-8', false);
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
		$pdf->writeHTML(Helper::htmlComprimir($htmlReporte), true, false, false, false, '');
    $pdf->lastPage();
    $pdf->output('LISTADO_RESPONSABLES_PR_'.Input::get('id_ejecutor').'_'.Session::get("ejercicio").'_'.date("H:i:s").'.pdf', 'D');
  }

  /**
  * Display a listing of the resource.
  *
  * @return Response
  */
  public function responsableTodo()
  {

    $htmlReporte = '
    <!-- Tabla 1 -->
    <table border="0.1" style="width:100%" style="font-size:9px" cellpadding="3">
    <thead>
    <tr align="left" bgcolor="#E6E6E6">
    <th colspan="5" style="width: 100%;"><b>LISTADO DE RESPONSABLES POR PROYECTOS</b></th>
    </tr>
    <tr style="font-size:8px">
    <th align="center" bgcolor="#E6E6E6" style="width: 10%;"><b>EJECUTOR</b></th>
    <th align="center" bgcolor="#E6E6E6" style="width: 10%;"><b>PROYECTO</b></th>
    <th align="center" bgcolor="#E6E6E6" style="width: 20%;"><b>TITULAR</b></th>
		<th align="center" bgcolor="#E6E6E6" style="width: 20%;"><b>PLANIFICADOR</b></th>
		<th align="center" bgcolor="#E6E6E6" style="width: 20%;"><b>ADMINISTRADOR</b></th>
    <th align="center" bgcolor="#E6E6E6" style="width: 20%;"><b>TÉCNICO</b></th>
    </tr>
    </thead>
    ';

    $htmlReporte.='
    <tbody>';

    $responsable = tab_proyecto_responsable::join('public.t26_proyectos as t01', 'public.t37_proyecto_responsables.id_proyecto', '=', 't01.id_proyecto')
    ->join('mantenimiento.tab_ejecutores as t02', 't01.id_ejecutor', '=', 't02.id_ejecutor')
    ->select( 'co_proyecto_responsables', 'public.t37_proyecto_responsables.id_proyecto',
       'responsable_nombres', 'reponsable_cedula',
       'responsable_correo', 'responsable_telefono', 'tecnico_nombres', 'tecnico_cedula',
       'tecnico_correo', 'tecnico_telefono', 'tecnico_unidad', 'registrador_nombres',
       'registrador_cedula', 'registrador_correo', 'registrador_telefono',
       'administrador_nombres', 'administrador_cedula', 'administrador_correo',
       'administrador_telefono', 'administrador_unidad', 'nombre', 't01.id_ejecutor', 'tx_ejecutor')
    ->where('id_ejercicio', '=', Session::get('ejercicio'))
    ->where('t01.edo_reg', '=', TRUE)
    ->orderBy('id_proyecto','ASC')
    ->get();

    foreach ($responsable as $key => $value) {

    $htmlReporte.='
    <tr style="font-size:7px" nobr="true">
      <td rowspan="5" style="width: 10%;">'.$value->id_ejecutor.' -  '.$value->tx_ejecutor.'</td>
      <td rowspan="5" style="width: 10%;">'.$value->id_proyecto.' - '.$value->nombre.'</td>
      <td style="width: 9%;"><b>Cédula</b></td>
			<td style="width: 11%;">'.$value->reponsable_cedula.'</td>
			<td style="width: 9%;"><b>Cédula</b></td>
			<td style="width: 11%;">'.$value->registrador_cedula.'</td>
			<td style="width: 9%;"><b>Cédula</b></td>
			<td style="width: 11%;">'.$value->administrador_cedula.'</td>
      <td style="width: 9%;"><b>Cédula</b></td>
      <td style="width: 11%;">'.$value->tecnico_cedula.'</td>
    </tr>
    <tr style="font-size:7px">
			<td><b>Nombre</b></td>
			<td>'.$value->responsable_nombres.'</td>
			<td><b>Nombre</b></td>
			<td>'.$value->registrador_nombres.'</td>
			<td><b>Nombre</b></td>
			<td>'.$value->administrador_nombres.'</td>
      <td><b>Nombre</b></td>
      <td>'.$value->tecnico_nombres.'</td>
		</tr>
		<tr style="font-size:7px">
			<td><b>Unidad de Adscripción</b></td>
			<td>'.$value->responsable_unidad.'</td>
			<td><b>Unidad de Adscripción</b></td>
			<td>'.$value->registrador_unidad.'</td>
			<td><b>Unidad de Adscripción</b></td>
			<td>'.$value->administrador_unidad.'</td>
      <td><b>Unidad de Adscripción</b></td>
      <td>'.$value->tecnico_unidad.'</td>
		</tr>
		<tr style="font-size:7px">
			<td><b>Correo electrónico</b></td>
			<td>'.$value->responsable_correo.'</td>
			<td><b>Correo electrónico</b></td>
			<td>'.$value->registrador_correo.'</td>
			<td><b>Correo electrónico</b></td>
			<td>'.$value->administrador_correo.'</td>
      <td><b>Correo electrónico</b></td>
      <td>'.$value->tecnico_correo.'</td>
		</tr>
		<tr style="font-size:7px">
			<td><b>Teléfono</b></td>
			<td>'.$value->responsable_telefono.'</td>
			<td><b>Teléfono</b></td>
			<td>'.$value->registrador_telefono.'</td>
			<td><b>Teléfono</b></td>
			<td>'.$value->administrador_telefono.'</td>
      <td><b>Teléfono</b></td>
      <td>'.$value->tecnico_telefono.'</td>
		</tr>
    ';
    }

    $htmlReporte.='
    </tbody>
    </table>';

    $pdf = new PDFresponsablePR('P', PDF_UNIT, 'LETTER', true, 'UTF-8', false);
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
    $pdf->SetAutoPageBreak(TRUE, 15);
    $pdf->AddPage();
    //Cierre de Reporte
    $pdf->writeHTML(Helper::htmlComprimir($htmlReporte), true, false, false, false, '');
    $pdf->lastPage();
    $pdf->output('LISTADO_RESPONSABLES_PR_'.Session::get("ejercicio").'_'.date("H:i:s").'.pdf', 'D');
  }

}
