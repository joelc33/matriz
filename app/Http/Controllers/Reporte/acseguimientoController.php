<?php

namespace matriz\Http\Controllers\Reporte;
//*******agregar esta linea******//
use matriz\Models\AcSegto\tab_meta_financiera;
use View;
use Input;
use Response;
use DB;
use Auth;
use TCPDF;
use Crypt;
use File;
use Blade;
use Session;
use Helper;
//*******************************//
use Illuminate\Http\Request;

use matriz\Http\Requests;
use matriz\Http\Controllers\Controller;

//*******clase extendida TCPDF******//
class PDFseguimientoAC extends TCPDF {

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

class acseguimientoController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function reporte()
	{
			return View::make('reporte.seguimiento.ac');
	}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function ficha()
    {



      $pdf = new PDFseguimientoAC('P', PDF_UNIT, 'LETTER', true, 'UTF-8', false);
      $pdf->SetCreator('Sistema POA, Yoser Perez');
      $pdf->SetAuthor('Yoser Perez');
      $pdf->SetTitle('Seguimiento AC');
      $pdf->SetSubject('Seguimiento AC');
      $pdf->SetKeywords('Seguimiento AC, PDF, Zulia, SPE, '.Session::get("ejercicio").'');
      $pdf->SetMargins(10,10,10);
      $pdf->SetTopMargin(30);
      $pdf->SetPrintHeader(true);
      $pdf->SetPrintFooter(true);
      // set auto page breaks
      $pdf->SetAutoPageBreak(TRUE, 10);
      $pdf->AddPage();
      //Cierre de Reporte
      //$pdf->writeHTML(Helper::htmlComprimir($htmlReporte), true, false, false, false, '');
      $pdf->lastPage();
      $pdf->output('SEGUIMIENTO_AC_'.Session::get("ejercicio").'_'.date("H:i:s").'.pdf', 'D');
    }

}
