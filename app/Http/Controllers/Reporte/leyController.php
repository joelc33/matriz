<?php

namespace matriz\Http\Controllers\Reporte;
//*******agregar esta linea******//
use matriz\Models\Mantenimiento\tab_presupuesto_ingreso;
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
      $pdf->SetLineWidth(0.150);
      $pdf->setCellHeightRatio(2);

      $pdf->AddPage();

      $pdf->SetFont('','B',8);
  		$pdf->MultiCell(55, 5, 'GOBERNACIÓN DEL ESTADO ZULIA', 0, 'L', 0, 0, '', '', true);
      $pdf->SetFont('','B',11);
      $pdf->MultiCell(90, 5, 'PRESUPUESTO DE INGRESOS', 0, 'C', 0, 0, '', '', true);
      $pdf->ln(8);
      $pdf->SetFont('','B',8);
      $pdf->MultiCell(55, 5, 'PRESUPUESTO '.Session::get("ejercicio"), 0, 'L', 0, 0, '', '', true);
      $pdf->MultiCell(90, 5, '(EN BOLÍVARES) ', 0, 'C', 0, 0, '', '', true);
      $pdf->ln(-10);
      $pdf->MultiCell(196, 18, '', 1, 'C', 0, 0, '', '', true);
      $pdf->ln(19);
      $pdf->SetFont('','',8);
      //$pdf->MultiCell(195, 220, '', 1, 'C', 0, 0, '', '', true);

      $tabla_presupuesto_ingreso = '
      <table border="0.5" style="width:100%" cellpadding="2" cellspacing="0">
      <thead>
      <tr style="font-size: 9px;">
      <th style="text-align: center;width:20%" colspan="4">
      <strong>CODIGO <br>(Recursos)</strong>
      </th>
      <th style="text-align: center;width:60%;font-size: 10px;" rowspan="3"><strong><br>DENOMINACION</strong></th>
      <th style="text-align: center;width:20%;font-size: 10px;" rowspan="3"><strong><br>MONTO</strong></th>
      </tr>
      <tr style="font-size: 6px">
      <th style="text-align: center;width:5%" rowspan="2"><strong><br>RAMO</strong></th>
      <th style="text-align: center;width:15%" colspan="3"><strong>SUB-RAMOS</strong></th>
      </tr>
      <tr style="font-size: 6px">
      <th style="text-align: center;width:5%"><strong>GEN.</strong></th>
      <th style="text-align: center;width:5%"><strong>ESP.</strong></th>
      <th style="text-align: center;width:5%"><strong>SUB-ESP.</strong></th>
      </tr>
      </thead>
      <tbody>';

      $tab_presupuesto_ingreso = tab_presupuesto_ingreso::select( 'id', 'id_tab_ejercicio_fiscal',
      'nu_partida', 'de_partida', 'mo_partida','in_activo' )
      ->where('in_activo', '=', TRUE)
      ->where('id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
      ->orderBy('nu_partida','ASC')
      ->get();

      foreach ($tab_presupuesto_ingreso as $key => $value) {

        $tabla_presupuesto_ingreso.='
        <tr>
        <td style="text-align: center;width:5%">'.substr($value->nu_partida, 0, 3).'</td>
        <td style="text-align: center;width:5%">'.substr(substr($value->nu_partida, 0, 5), 3).'</td>
        <td style="text-align: center;width:5%">'.substr(substr($value->nu_partida, 0, 7), 5).'</td>
        <td style="text-align: center;width:5%">'.substr(substr($value->nu_partida, 0, 9), 7).'</td>
        <td style="text-align: left;width:60%">'.$value->de_partida.'</td>
        <td style="text-align: rigth;width:20%">'.number_format($value->mo_partida, 2, ',', '.').'</td>
        </tr>';

      }

      $tabla_presupuesto_ingreso.='
      </tbody>
      </table>';

      $pdf->writeHTML($tabla_presupuesto_ingreso, true, false, false, false, '');

      //Cierre de Reporte
      $pdf->lastPage();
      $pdf->output('LEY_DE_PRESUPUESTO_'.Session::get("ejercicio").'_'.date("H:i:s").'.pdf', 'D');
    }
}
