<?php

namespace matriz\Http\Controllers\Reporte;
//*******agregar esta linea******//
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
//*******************************//
use Illuminate\Http\Request;

use matriz\Http\Requests;
use matriz\Http\Controllers\Controller;

class leyController extends Controller
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
    public function libro()
    {
      $pdf = new TCPDF('P', PDF_UNIT, 'LETTER', true, 'UTF-8', false);
      $pdf->SetCreator('Sistema Nueva Etapa, Yoser Perez');
      $pdf->SetAuthor('Yoser Perez');
      $pdf->SetTitle('Ley de Presupuesto');
      $pdf->SetSubject('Ley de Presupuesto');
      $pdf->SetKeywords('Ley de Presupuesto, PDF, Zulia, SPE, '.Session::get("ejercicio").'');
      $pdf->SetMargins(10,10,10);
      $pdf->SetTopMargin(10);
      $pdf->SetPrintHeader(false);
      $pdf->SetPrintFooter(false);
      // set auto page breaks
      $pdf->SetAutoPageBreak(TRUE, 10);
      $pdf->AddPage();

      /******Portada Titulo I*********/
      $pdf->SetAlpha(0.3);
  		$pdf->Image(public_path().'/images/mapa_bandera.jpg', 20, 40, 190, 190, 'JPG', '', '', false, 170, '', false, false, 0);
  		$pdf->ln(30);
  		$pdf->setAlpha(1);
  		$pdf->SetFont('','',8);

      // reset font stretching  reset font spacing
      $pdf->setFontStretching(100);
      $pdf->setFontSpacing(1);
      //
      $pdf->SetY(15);
      $pdf->SetFont('','B',14);
      $pdf->SetTextColor(0,0,0);
      $pdf->MultiCell(190, 5, 'GOBERNACIÓN BOLIVARIANA DEL ZULIA', 0, 'C', 0, 0, '', '', true);
      $pdf->ln(230);
      $pdf->SetFont('','B',12);
      //$pdf->MultiCell(190, 5, 'TITULO I', 0, 'R', 0, 0, '', '', true);
      $pdf->writeHTML('<b><u>TITULO I<u/></b>', true, false, true, false, 'R');
      $pdf->ln(1);
      $pdf->MultiCell(195, 5, 'DISPOSICIONES GENERALES', 0, 'R', 0, 0, '', '', true);
      $pdf->ln(10);
      // set border width
      $pdf->SetLineWidth(0.508);
      $pdf->SetDrawColor(0,0,0);
      $pdf->SetFillColor(0,0,0);
      $pdf->setCellHeightRatio(0);
      $pdf->Cell(195, 0, '', 'B', 1, 'R', 1, '', 0, false, 'T', 'R');
      $pdf->ln(2);
      $pdf->Cell(195, 0, '', 'B', 1, 'R', 1, '', 0, false, 'T', 'R');
      // reset font stretching  reset font spacing
      $pdf->setFontStretching(100);
      $pdf->setFontSpacing(0);

      $pdf->AddPage();

      /******Portada Titulo II*********/
      $pdf->SetAlpha(0.3);
      $pdf->Image(public_path().'/images/mapa_bandera.jpg', 20, 40, 190, 190, 'JPG', '', '', false, 170, '', false, false, 0);
      $pdf->ln(30);
      $pdf->setAlpha(1);
      $pdf->SetFont('','',8);

      // reset font stretching  reset font spacing
      $pdf->setFontStretching(100);
      $pdf->setFontSpacing(1);
      //
      $pdf->SetY(15);
      $pdf->SetFont('','B',14);
      $pdf->SetTextColor(0,0,0);
      $pdf->MultiCell(190, 5, 'GOBERNACIÓN BOLIVARIANA DEL ZULIA', 0, 'C', 0, 0, '', '', true);
      $pdf->ln(230);
      $pdf->SetFont('','B',12);
      //$pdf->MultiCell(190, 5, 'TITULO I', 0, 'R', 0, 0, '', '', true);
      $pdf->writeHTML('<b><u>TITULO II<u/></b>', true, false, true, false, 'R');
      $pdf->ln(7);
      $pdf->MultiCell(195, 5, 'PRESUPUESTO DE INGRESOS', 0, 'R', 0, 0, '', '', true);
      $pdf->ln(7);
      // set border width
      $pdf->SetLineWidth(0.508);
      $pdf->SetDrawColor(0,0,0);
      $pdf->SetFillColor(0,0,0);
      $pdf->setCellHeightRatio(0);
      $pdf->Cell(195, 0, '', 'B', 1, 'R', 1, '', 0, false, 'T', 'R');
      $pdf->ln(2);
      $pdf->Cell(195, 0, '', 'B', 1, 'R', 1, '', 0, false, 'T', 'R');
      // reset font stretching  reset font spacing
      $pdf->setFontStretching(100);
      $pdf->setFontSpacing(0);

      $pdf->AddPage();





      //Cierre de Reporte
      $pdf->lastPage();
      $pdf->output('LEY_DE_PRESUPUESTO_'.Session::get("ejercicio").'_'.date("H:i:s").'.pdf', 'D');
    }
}
