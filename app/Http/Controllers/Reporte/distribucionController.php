<?php

namespace matriz\Http\Controllers\Reporte;
//*******agregar esta linea******//
use matriz\Models\Mantenimiento\tab_presupuesto_ingreso;
use matriz\Models\Mantenimiento\tab_partidas;
use matriz\Models\Mantenimiento\tab_objetivo_sectorial;
use matriz\Models\Ac\tab_ac;
use matriz\Models\Ac\tab_ac_ae_partida;
use matriz\Models\Proyecto\tab_proyecto;
use matriz\Models\Proyecto\tab_proyecto_ae_partida;
use matriz\Models\Proyecto\vista_distribucion_presupuesto;
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

class distribucionController extends Controller
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
    $pdf->SetTitle('Distribución de Presupuesto');
    $pdf->SetSubject('Distribución de Presupuesto');
    $pdf->SetKeywords('Distribución de Presupuesto, PDF, Zulia, SPE, '.Session::get("ejercicio").'');
    $pdf->SetMargins(10,10,10);
    $pdf->SetTopMargin(10);
    $pdf->SetPrintHeader(false);
    $pdf->SetPrintFooter(false);
    // set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, 10);
    //$pdf->AddPage();

    $distribucion_uno = vista_distribucion_presupuesto::
    select( 'co_sector', 'tx_descripcion' )
    ->where('ef_uno', '=', Session::get('ejercicio'))
    ->groupBy('co_sector')
    ->groupBy('tx_descripcion')
    ->orderBy('co_sector','ASC')
    ->get();

    foreach ($distribucion_uno as $key => $value_distribucion_uno) {

      $pdf->AddPage();

      // reset font stretching  reset font spacing
      $pdf->setFontStretching(100);
      $pdf->setFontSpacing(0);
      $pdf->SetLineWidth(0.150);
      $pdf->setCellHeightRatio(2);

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
      $pdf->writeHTML('<b><u>SECTOR: '.$value_distribucion_uno->co_sector.'<u/></b>', true, false, true, false, 'R');
      $pdf->ln(1);
      $pdf->MultiCell(195, 5, mb_strtoupper($value_distribucion_uno->tx_descripcion, 'UTF-8'), 0, 'R', 0, 0, '', '', true);
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
      $pdf->SetLineWidth(0.150);
      $pdf->setCellHeightRatio(2);

      $distribucion_dos = vista_distribucion_presupuesto::
      select( 'nu_original', 'de_nombre' )
      ->where('ef_uno', '=', Session::get('ejercicio'))
      ->where('co_sector', '=', $value_distribucion_uno->co_sector)
      ->groupBy('nu_original')
      ->groupBy('de_nombre')
      ->orderBy('nu_original','ASC')
      ->get();

      foreach ($distribucion_dos as $key => $value_distribucion_dos) {

        $pdf->AddPage();

        // reset font stretching  reset font spacing
        $pdf->setFontStretching(100);
        $pdf->setFontSpacing(0);
        $pdf->SetLineWidth(0.150);
        $pdf->setCellHeightRatio(2);

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
        $pdf->ln(210);
        $pdf->SetFont('','B',12);
        //$pdf->MultiCell(190, 5, 'TITULO I', 0, 'R', 0, 0, '', '', true);
        $pdf->writeHTML('<b><u>PROYECTO Y/O ACCIÓN CENTRALIZADA: '.substr($value_distribucion_dos->nu_original, -2).'<u/></b>', true, false, true, false, 'R');
        $pdf->ln(1);
        $pdf->MultiCell(195, 5, mb_strtoupper($value_distribucion_dos->de_nombre, 'UTF-8'), 0, 'R', 0, 0, '', '', true);
        $pdf->ln(30);
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


        $distribucion_tres = vista_distribucion_presupuesto::
        select( 'co_sector', 'tx_descripcion', 'id_ejecutor', 'tx_ejecutor', 'nu_original', 'de_nombre' )
        ->where('ef_uno', '=', Session::get('ejercicio'))
        ->where('co_sector', '=', $value_distribucion_uno->co_sector)
        ->where('nu_original', '=', $value_distribucion_dos->nu_original)
        ->groupBy('co_sector')
        ->groupBy('tx_descripcion')
        ->groupBy('id_ejecutor')
        ->groupBy('tx_ejecutor')
        ->groupBy('nu_original')
        ->groupBy('de_nombre')
        ->orderBy('co_sector','ASC')
        ->orderBy('id_ejecutor','ASC')
        ->orderBy('nu_original','ASC')
        ->get();

        foreach ($distribucion_tres as $key => $value_distribucion_tres) {

          $pdf->AddPage();
          $movimiento = 0;

          $pdf->SetFont('','B',8);
          $pdf->MultiCell(55, 5, 'GOBERNACIÓN DEL ESTADO ZULIA', 0, 'L', 0, 0, '', '', true);
          $pdf->SetFont('','B',8);
          $pdf->setCellHeightRatio(1);
          $pdf->MultiCell(90, 5, 'CRÉDITOS PRESUPUESTARIOS DEL PROYECTO Y/O ACCIÓN CENTRALIZADA A NIVEL DE PROYECTOS Y/O ACCIÓN CENTRALIZADA', 0, 'C', 0, 0, '', '', true);
          $pdf->setCellHeightRatio(2);
          $pdf->ln(8);
          $pdf->SetFont('','B',8);
          $pdf->MultiCell(55, 5, 'PRESUPUESTO '.Session::get("ejercicio"), 0, 'L', 0, 0, '', '', true);
          $pdf->MultiCell(90, 5, '(EN BOLÍVARES)', 0, 'C', 0, 0, '', '', true);
          $pdf->ln(-10);
          $pdf->MultiCell(196, 18, '', 1, 'C', 0, 0, '', '', true);
          $pdf->ln(19);
          $pdf->SetFont('','B',7);
          $pdf->setCellHeightRatio(1.2);

          $pdf->MultiCell(29, 10, chr(10).'SECTOR', 1, 'L', 0, 0, '', '', true);
          $pdf->SetFont('','',6);
          $pdf->MultiCell(91, 10, chr(10).$value_distribucion_tres->co_sector.' - '.mb_strtoupper($value_distribucion_tres->tx_descripcion, 'UTF-8'), 1, 'L', 0, 0, '', '', true);
          $pdf->SetFont('','B',7);
          $pdf->MultiCell(20, 20, chr(10).'UNIDAD EJECUTORA', 1, 'C', 0, 0, '', '', true);
          $pdf->SetFont('','',6);
          $pdf->MultiCell(56, 20, chr(10).$value_distribucion_tres->id_ejecutor.' - '.mb_strtoupper($value_distribucion_tres->tx_ejecutor, 'UTF-8'), 1, 'L', 0, 0, '', '', true);
          $pdf->ln(10);
          $pdf->SetFont('','B',6);
          $pdf->MultiCell(29, 10, chr(10).'PROYECTO Y/O ACCIÓN CENTRALIZADA', 1, 'L', 0, 0, '', '', true);
          $pdf->SetFont('','',6);
          $pdf->MultiCell(91, 10, substr($value_distribucion_tres->nu_original, -2).' - '.mb_strtoupper($value_distribucion_tres->de_nombre, 'UTF-8'), 1, 'L', 0, 0, '', '', true);
          $pdf->ln(10);
          $pdf->MultiCell(196, 220, '', 1, 'C', 0, 0, '', '', true);
          $pdf->ln(0);
          $pdf->SetFont('','B',6);
          $pdf->MultiCell(7, 5, '', 0, 'L', 0, 0, '', '', true);
          $pdf->MultiCell(22, 5, 'SUB - PARTIDA', 1, 'L', 0, 0, '', '', true);
          $pdf->ln(30);
          $pdf->SetFont('','B',6);
          $pdf->StartTransform();
          $pdf->Rotate(90);
          $pdf->MultiCell(30, 7, 'PARTIDA', 1, 'L', 0, 0, '', '', true);
          $pdf->ln(7);
          $pdf->MultiCell(25, 5, 'GENERICA', 1, 'L', 0, 0, '', '', true);
          $pdf->MultiCell(5, 5, '', 0, 'L', 0, 0, '', '', true);
          $pdf->ln(5);
          $pdf->MultiCell(25, 5, 'ESPECIFICA', 1, 'L', 0, 0, '', '', true);
          $pdf->MultiCell(5, 5, '', 0, 'L', 0, 0, '', '', true);
          $pdf->ln(5);
          $pdf->MultiCell(25, 5, 'SUB ESPECIFICA', 1, 'L', 0, 0, '', '', true);
          $pdf->MultiCell(5, 5, '', 0, 'L', 0, 0, '', '', true);
          $pdf->ln(5);
          $pdf->MultiCell(25, 7, 'SUB SUB ESPECIFICA', 1, 'L', 0, 0, '', '', true);
          $pdf->MultiCell(5, 5, '', 0, 'L', 0, 0, '', '', true);
          $pdf->ln(30);
          $pdf->StopTransform();
          $pdf->ln(-82);
          $pdf->SetFont('','B',8);
          $pdf->setCellHeightRatio(10);
          $pdf->MultiCell(29, 30, '', 0, 'C', 0, 0, '', '', true);
          $pdf->MultiCell(91, 30, 'DENOMINACIÓN', 1, 'C', 0, 0, '', '', true);
          $pdf->SetFont('','B',6);
          $pdf->setCellHeightRatio(1.2);
          $pdf->MultiCell(20, 30, chr(10).chr(10).chr(10).'TOTAL PROYECTO Y/O ACCIÓN CENTRALIZADA', 1, 'C', 0, 0, '', '', true);
          $pdf->MultiCell(56, 5, chr(10).'ACCIÓNES ESPECIFICAS', 0, 'C', 0, 0, '', '', true);
          $pdf->ln(0);
          $pdf->MultiCell(140, 30, '', 0, 'C', 0, 0, '', '', true);
          $pdf->ln(5);
          $pdf->MultiCell(140, 30, '', 0, 'C', 0, 0, '', '', true);
          $pdf->MultiCell(20, 25, '', 1, 'C', 0, 0, '', '', true);
          $pdf->MultiCell(20, 25, '', 1, 'C', 0, 0, '', '', true);
          $pdf->MultiCell(16, 25, '', 1, 'C', 0, 0, '', '', true);
          $pdf->ln(25);
          $pdf->setCellHeightRatio(1);
          $pdf->MultiCell(7, 190, '', 1, 'C', 0, 0, '', '', true);
          $pdf->MultiCell(5, 190, '', 1, 'C', 0, 0, '', '', true);
          $pdf->MultiCell(5, 190, '', 1, 'C', 0, 0, '', '', true);
          $pdf->MultiCell(5, 190, '', 1, 'C', 0, 0, '', '', true);
          $pdf->MultiCell(7, 190, '', 1, 'C', 0, 0, '', '', true);
          $pdf->MultiCell(91, 190, '', 1, 'C', 0, 0, '', '', true);
          $pdf->MultiCell(20, 190, '', 1, 'C', 0, 0, '', '', true);
          $pdf->MultiCell(20, 190, '', 1, 'C', 0, 0, '', '', true);
          $pdf->MultiCell(20, 190, '', 1, 'C', 0, 0, '', '', true);
          $pdf->MultiCell(16, 190, '', 1, 'C', 0, 0, '', '', true);
          $pdf->ln(2);
          $pdf->SetFont('','',7);
          $pdf->setCellHeightRatio(1);

          $distribucion_cuatro = vista_distribucion_presupuesto::
          join('mantenimiento.tab_partidas as t01', 't01.co_partida', '=', DB::raw('left(public.vista_distribucion_presupuesto.co_partida, 3)'))
          ->select( 't01.co_partida', 'tx_nombre',  DB::raw('sum(monto) as mo_partida') )
          ->where('ef_uno', '=', Session::get('ejercicio'))
          ->where('t01.id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
          ->where('co_sector', '=', $value_distribucion_uno->co_sector)
          ->where('nu_original', '=', $value_distribucion_dos->nu_original)
          ->where('id_ejecutor', '=', $value_distribucion_tres->id_ejecutor)
          ->groupBy('t01.co_partida')
          ->groupBy('tx_nombre')
          ->orderBy('co_partida','ASC')
          ->get();

          foreach ($distribucion_cuatro as $key => $value_distribucion_cuatro) {

            $pdf->SetFont('','',6);
            $pdf->MultiCell(7, 5, $value_distribucion_cuatro->co_partida, 0, 'L', 0, 0, '', '', true);
            $pdf->MultiCell(5, 5, '', 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(5, 5, '', 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(5, 5, '', 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(7, 5, '', 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(91, 5, $value_distribucion_cuatro->tx_nombre, 0, 'L', 0, 0, '', '', true);
            $pdf->MultiCell(20, 5, number_format($value_distribucion_cuatro->mo_partida, 2, ',', '.'), 0, 'R', 0, 0, '', '', true);
            $pdf->MultiCell(20, 5, '', 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(20, 5, '', 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(16, 5, '', 0, 'C', 0, 0, '', '', true);
            //$pdf->ln(4);

            $condicionPartida = strlen($value_distribucion_cuatro->tx_nombre);
            if ($condicionPartida >= 70) {
              $pdf->ln(8);
            }else {
              $pdf->ln(4);
            }

            $start_y = $pdf->GetY();

            if ($start_y >= 260) {

              // reset font stretching  reset font spacing
              $pdf->setFontStretching(100);
              $pdf->setFontSpacing(0);
              $pdf->SetLineWidth(0.150);
              $pdf->setCellHeightRatio(2);

              $pdf->AddPage();

              $pdf->SetFont('','B',8);
              $pdf->MultiCell(55, 5, 'GOBERNACIÓN DEL ESTADO ZULIA', 0, 'L', 0, 0, '', '', true);
              $pdf->SetFont('','B',8);
              $pdf->setCellHeightRatio(1);
              $pdf->MultiCell(90, 5, 'CRÉDITOS PRESUPUESTARIOS DEL PROYECTO Y/O ACCIÓN CENTRALIZADA A NIVEL DE PROYECTOS Y/O ACCIÓN CENTRALIZADA', 0, 'C', 0, 0, '', '', true);
              $pdf->setCellHeightRatio(2);
              $pdf->ln(8);
              $pdf->SetFont('','B',8);
              $pdf->MultiCell(55, 5, 'PRESUPUESTO '.Session::get("ejercicio"), 0, 'L', 0, 0, '', '', true);
              $pdf->MultiCell(90, 5, '(EN BOLÍVARES)', 0, 'C', 0, 0, '', '', true);
              $pdf->ln(-10);
              $pdf->MultiCell(196, 18, '', 1, 'C', 0, 0, '', '', true);
              $pdf->ln(19);
              $pdf->SetFont('','B',7);
              $pdf->setCellHeightRatio(1.2);

              $pdf->MultiCell(29, 10, chr(10).'SECTOR', 1, 'L', 0, 0, '', '', true);
              $pdf->SetFont('','',6);
              $pdf->MultiCell(91, 10, chr(10).$value_distribucion_tres->co_sector.' - '.mb_strtoupper($value_distribucion_tres->tx_descripcion, 'UTF-8'), 1, 'L', 0, 0, '', '', true);
              $pdf->SetFont('','B',7);
              $pdf->MultiCell(20, 20, chr(10).'UNIDAD EJECUTORA', 1, 'C', 0, 0, '', '', true);
              $pdf->SetFont('','',6);
              $pdf->MultiCell(56, 20, chr(10).$value_distribucion_tres->id_ejecutor.' - '.mb_strtoupper($value_distribucion_tres->tx_ejecutor, 'UTF-8'), 1, 'L', 0, 0, '', '', true);
              $pdf->ln(10);
              $pdf->SetFont('','B',6);
              $pdf->MultiCell(29, 10, chr(10).'PROYECTO Y/O ACCIÓN CENTRALIZADA', 1, 'L', 0, 0, '', '', true);
              $pdf->SetFont('','',6);
              $pdf->MultiCell(91, 10, substr($value_distribucion_tres->nu_original, -2).' - '.mb_strtoupper($value_distribucion_tres->de_nombre, 'UTF-8'), 1, 'L', 0, 0, '', '', true);
              $pdf->ln(10);
              $pdf->MultiCell(196, 220, '', 1, 'C', 0, 0, '', '', true);
              $pdf->ln(0);
              $pdf->SetFont('','B',6);
              $pdf->MultiCell(7, 5, '', 0, 'L', 0, 0, '', '', true);
              $pdf->MultiCell(22, 5, 'SUB - PARTIDA', 1, 'L', 0, 0, '', '', true);
              $pdf->ln(30);
              $pdf->SetFont('','B',6);
              $pdf->StartTransform();
              $pdf->Rotate(90);
              $pdf->MultiCell(30, 7, 'PARTIDA', 1, 'L', 0, 0, '', '', true);
              $pdf->ln(7);
              $pdf->MultiCell(25, 5, 'GENERICA', 1, 'L', 0, 0, '', '', true);
              $pdf->MultiCell(5, 5, '', 0, 'L', 0, 0, '', '', true);
              $pdf->ln(5);
              $pdf->MultiCell(25, 5, 'ESPECIFICA', 1, 'L', 0, 0, '', '', true);
              $pdf->MultiCell(5, 5, '', 0, 'L', 0, 0, '', '', true);
              $pdf->ln(5);
              $pdf->MultiCell(25, 5, 'SUB ESPECIFICA', 1, 'L', 0, 0, '', '', true);
              $pdf->MultiCell(5, 5, '', 0, 'L', 0, 0, '', '', true);
              $pdf->ln(5);
              $pdf->MultiCell(25, 7, 'SUB SUB ESPECIFICA', 1, 'L', 0, 0, '', '', true);
              $pdf->MultiCell(5, 5, '', 0, 'L', 0, 0, '', '', true);
              $pdf->ln(30);
              $pdf->StopTransform();
              $pdf->ln(-82);
              $pdf->SetFont('','B',8);
              $pdf->setCellHeightRatio(10);
              $pdf->MultiCell(29, 30, '', 0, 'C', 0, 0, '', '', true);
              $pdf->MultiCell(91, 30, 'DENOMINACIÓN', 1, 'C', 0, 0, '', '', true);
              $pdf->SetFont('','B',6);
              $pdf->setCellHeightRatio(1.2);
              $pdf->MultiCell(20, 30, chr(10).chr(10).chr(10).'TOTAL PROYECTO Y/O ACCIÓN CENTRALIZADA', 1, 'C', 0, 0, '', '', true);
              $pdf->MultiCell(56, 5, chr(10).'ACCIÓNES ESPECIFICAS', 0, 'C', 0, 0, '', '', true);
              $pdf->ln(0);
              $pdf->MultiCell(140, 30, '', 0, 'C', 0, 0, '', '', true);
              $pdf->ln(5);
              $pdf->MultiCell(140, 30, '', 0, 'C', 0, 0, '', '', true);
              $pdf->MultiCell(20, 25, '', 1, 'C', 0, 0, '', '', true);
              $pdf->MultiCell(20, 25, '', 1, 'C', 0, 0, '', '', true);
              $pdf->MultiCell(16, 25, '', 1, 'C', 0, 0, '', '', true);
              $pdf->ln(25);
              $pdf->setCellHeightRatio(1);
              $pdf->MultiCell(7, 190, '', 1, 'C', 0, 0, '', '', true);
              $pdf->MultiCell(5, 190, '', 1, 'C', 0, 0, '', '', true);
              $pdf->MultiCell(5, 190, '', 1, 'C', 0, 0, '', '', true);
              $pdf->MultiCell(5, 190, '', 1, 'C', 0, 0, '', '', true);
              $pdf->MultiCell(7, 190, '', 1, 'C', 0, 0, '', '', true);
              $pdf->MultiCell(91, 190, '', 1, 'C', 0, 0, '', '', true);
              $pdf->MultiCell(20, 190, '', 1, 'C', 0, 0, '', '', true);
              $pdf->MultiCell(20, 190, '', 1, 'C', 0, 0, '', '', true);
              $pdf->MultiCell(20, 190, '', 1, 'C', 0, 0, '', '', true);
              $pdf->MultiCell(16, 190, '', 1, 'C', 0, 0, '', '', true);
              $pdf->ln(2);
              $pdf->SetFont('','',7);
              $pdf->setCellHeightRatio(1);

            }

            $distribucion_cinco = vista_distribucion_presupuesto::
            join('mantenimiento.tab_partidas as t01', 't01.co_partida', '=', DB::raw('left(public.vista_distribucion_presupuesto.co_partida, 3)'))
            ->join('mantenimiento.tab_partidas as t02', 't02.co_partida', '=', DB::raw('left(public.vista_distribucion_presupuesto.co_partida, 5)'))
            ->select( 't02.co_partida', 't02.tx_nombre',  DB::raw('sum(monto) as mo_partida') )
            ->where('ef_uno', '=', Session::get('ejercicio'))
            ->where('t01.id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
            ->where('t02.id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
            ->where('co_sector', '=', $value_distribucion_uno->co_sector)
            ->where('nu_original', '=', $value_distribucion_dos->nu_original)
            ->where('id_ejecutor', '=', $value_distribucion_tres->id_ejecutor)
            ->where('t01.co_partida', '=', $value_distribucion_cuatro->co_partida)
            ->groupBy('t02.co_partida')
            ->groupBy('t02.tx_nombre')
            ->orderBy('co_partida','ASC')
            ->get();

            foreach ($distribucion_cinco as $key => $value_distribucion_cinco) {

              $pdf->SetFont('','',6);
              $pdf->MultiCell(7, 5, substr(trim($value_distribucion_cinco->co_partida), 0, 3), 0, 'L', 0, 0, '', '', true);
              $pdf->MultiCell(5, 5, substr(substr(trim($value_distribucion_cinco->co_partida), 0, 5), 3), 0, 'L', 0, 0, '', '', true);
              $pdf->MultiCell(5, 5, '', 0, 'C', 0, 0, '', '', true);
              $pdf->MultiCell(5, 5, '', 0, 'C', 0, 0, '', '', true);
              $pdf->MultiCell(7, 5, '', 0, 'C', 0, 0, '', '', true);
              $pdf->MultiCell(91, 5, $value_distribucion_cinco->tx_nombre, 0, 'L', 0, 0, '', '', true);
              $pdf->MultiCell(20, 5, number_format($value_distribucion_cinco->mo_partida, 2, ',', '.'), 0, 'R', 0, 0, '', '', true);
              $pdf->MultiCell(20, 5, '', 0, 'C', 0, 0, '', '', true);
              $pdf->MultiCell(20, 5, '', 0, 'C', 0, 0, '', '', true);
              $pdf->MultiCell(16, 5, '', 0, 'C', 0, 0, '', '', true);
              //$pdf->ln(4);

              $condicionPartida = strlen($value_distribucion_cinco->tx_nombre);
              if ($condicionPartida >= 70) {
                $pdf->ln(8);
              }else {
                $pdf->ln(4);
              }

              $start_y = $pdf->GetY();

              if ($start_y >= 260) {

                // reset font stretching  reset font spacing
                $pdf->setFontStretching(100);
                $pdf->setFontSpacing(0);
                $pdf->SetLineWidth(0.150);
                $pdf->setCellHeightRatio(2);

                $pdf->AddPage();

                $pdf->SetFont('','B',8);
                $pdf->MultiCell(55, 5, 'GOBERNACIÓN DEL ESTADO ZULIA', 0, 'L', 0, 0, '', '', true);
                $pdf->SetFont('','B',8);
                $pdf->setCellHeightRatio(1);
                $pdf->MultiCell(90, 5, 'CRÉDITOS PRESUPUESTARIOS DEL PROYECTO Y/O ACCIÓN CENTRALIZADA A NIVEL DE PROYECTOS Y/O ACCIÓN CENTRALIZADA', 0, 'C', 0, 0, '', '', true);
                $pdf->setCellHeightRatio(2);
                $pdf->ln(8);
                $pdf->SetFont('','B',8);
                $pdf->MultiCell(55, 5, 'PRESUPUESTO '.Session::get("ejercicio"), 0, 'L', 0, 0, '', '', true);
                $pdf->MultiCell(90, 5, '(EN BOLÍVARES)', 0, 'C', 0, 0, '', '', true);
                $pdf->ln(-10);
                $pdf->MultiCell(196, 18, '', 1, 'C', 0, 0, '', '', true);
                $pdf->ln(19);
                $pdf->SetFont('','B',7);
                $pdf->setCellHeightRatio(1.2);

                $pdf->MultiCell(29, 10, chr(10).'SECTOR', 1, 'L', 0, 0, '', '', true);
                $pdf->SetFont('','',6);
                $pdf->MultiCell(91, 10, chr(10).$value_distribucion_tres->co_sector.' - '.mb_strtoupper($value_distribucion_tres->tx_descripcion, 'UTF-8'), 1, 'L', 0, 0, '', '', true);
                $pdf->SetFont('','B',7);
                $pdf->MultiCell(20, 20, chr(10).'UNIDAD EJECUTORA', 1, 'C', 0, 0, '', '', true);
                $pdf->SetFont('','',6);
                $pdf->MultiCell(56, 20, chr(10).$value_distribucion_tres->id_ejecutor.' - '.mb_strtoupper($value_distribucion_tres->tx_ejecutor, 'UTF-8'), 1, 'L', 0, 0, '', '', true);
                $pdf->ln(10);
                $pdf->SetFont('','B',6);
                $pdf->MultiCell(29, 10, chr(10).'PROYECTO Y/O ACCIÓN CENTRALIZADA', 1, 'L', 0, 0, '', '', true);
                $pdf->SetFont('','',6);
                $pdf->MultiCell(91, 10, substr($value_distribucion_tres->nu_original, -2).' - '.mb_strtoupper($value_distribucion_tres->de_nombre, 'UTF-8'), 1, 'L', 0, 0, '', '', true);
                $pdf->ln(10);
                $pdf->MultiCell(196, 220, '', 1, 'C', 0, 0, '', '', true);
                $pdf->ln(0);
                $pdf->SetFont('','B',6);
                $pdf->MultiCell(7, 5, '', 0, 'L', 0, 0, '', '', true);
                $pdf->MultiCell(22, 5, 'SUB - PARTIDA', 1, 'L', 0, 0, '', '', true);
                $pdf->ln(30);
                $pdf->SetFont('','B',6);
                $pdf->StartTransform();
                $pdf->Rotate(90);
                $pdf->MultiCell(30, 7, 'PARTIDA', 1, 'L', 0, 0, '', '', true);
                $pdf->ln(7);
                $pdf->MultiCell(25, 5, 'GENERICA', 1, 'L', 0, 0, '', '', true);
                $pdf->MultiCell(5, 5, '', 0, 'L', 0, 0, '', '', true);
                $pdf->ln(5);
                $pdf->MultiCell(25, 5, 'ESPECIFICA', 1, 'L', 0, 0, '', '', true);
                $pdf->MultiCell(5, 5, '', 0, 'L', 0, 0, '', '', true);
                $pdf->ln(5);
                $pdf->MultiCell(25, 5, 'SUB ESPECIFICA', 1, 'L', 0, 0, '', '', true);
                $pdf->MultiCell(5, 5, '', 0, 'L', 0, 0, '', '', true);
                $pdf->ln(5);
                $pdf->MultiCell(25, 7, 'SUB SUB ESPECIFICA', 1, 'L', 0, 0, '', '', true);
                $pdf->MultiCell(5, 5, '', 0, 'L', 0, 0, '', '', true);
                $pdf->ln(30);
                $pdf->StopTransform();
                $pdf->ln(-82);
                $pdf->SetFont('','B',8);
                $pdf->setCellHeightRatio(10);
                $pdf->MultiCell(29, 30, '', 0, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(91, 30, 'DENOMINACIÓN', 1, 'C', 0, 0, '', '', true);
                $pdf->SetFont('','B',6);
                $pdf->setCellHeightRatio(1.2);
                $pdf->MultiCell(20, 30, chr(10).chr(10).chr(10).'TOTAL PROYECTO Y/O ACCIÓN CENTRALIZADA', 1, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(56, 5, chr(10).'ACCIÓNES ESPECIFICAS', 0, 'C', 0, 0, '', '', true);
                $pdf->ln(0);
                $pdf->MultiCell(140, 30, '', 0, 'C', 0, 0, '', '', true);
                $pdf->ln(5);
                $pdf->MultiCell(140, 30, '', 0, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(20, 25, '', 1, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(20, 25, '', 1, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(16, 25, '', 1, 'C', 0, 0, '', '', true);
                $pdf->ln(25);
                $pdf->setCellHeightRatio(1);
                $pdf->MultiCell(7, 190, '', 1, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(5, 190, '', 1, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(5, 190, '', 1, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(5, 190, '', 1, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(7, 190, '', 1, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(91, 190, '', 1, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(20, 190, '', 1, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(20, 190, '', 1, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(20, 190, '', 1, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(16, 190, '', 1, 'C', 0, 0, '', '', true);
                $pdf->ln(2);
                $pdf->SetFont('','',7);
                $pdf->setCellHeightRatio(1);

              }

              $distribucion_seis = vista_distribucion_presupuesto::
              join('mantenimiento.tab_partidas as t01', 't01.co_partida', '=', DB::raw('left(public.vista_distribucion_presupuesto.co_partida, 3)'))
              ->join('mantenimiento.tab_partidas as t02', 't02.co_partida', '=', DB::raw('left(public.vista_distribucion_presupuesto.co_partida, 5)'))
              ->join('mantenimiento.tab_partidas as t03', 't03.co_partida', '=', DB::raw('left(public.vista_distribucion_presupuesto.co_partida, 7)'))
              ->select( 't03.co_partida', 't03.tx_nombre',  DB::raw('sum(monto) as mo_partida') )
              ->where('ef_uno', '=', Session::get('ejercicio'))
              ->where('t01.id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
              ->where('t02.id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
              ->where('t03.id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
              ->where('co_sector', '=', $value_distribucion_uno->co_sector)
              ->where('nu_original', '=', $value_distribucion_dos->nu_original)
              ->where('id_ejecutor', '=', $value_distribucion_tres->id_ejecutor)
              ->where('t01.co_partida', '=', $value_distribucion_cuatro->co_partida)
              ->where('t02.co_partida', '=', $value_distribucion_cinco->co_partida)
              ->groupBy('t03.co_partida')
              ->groupBy('t03.tx_nombre')
              ->orderBy('co_partida','ASC')
              ->get();

              foreach ($distribucion_seis as $key => $value_distribucion_seis) {

                $pdf->SetFont('','',6);
                $pdf->MultiCell(7, 5, substr(trim($value_distribucion_seis->co_partida), 0, 3), 0, 'L', 0, 0, '', '', true);
                $pdf->MultiCell(5, 5, substr(substr(trim($value_distribucion_seis->co_partida), 0, 5), 3), 0, 'L', 0, 0, '', '', true);
                $pdf->MultiCell(5, 5, substr(substr(trim($value_distribucion_seis->co_partida), 0, 7), 5), 0, 'L', 0, 0, '', '', true);
                $pdf->MultiCell(5, 5, '', 0, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(7, 5, '', 0, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(91, 5, $value_distribucion_seis->tx_nombre, 0, 'L', 0, 0, '', '', true);
                $pdf->MultiCell(20, 5, number_format($value_distribucion_seis->mo_partida, 2, ',', '.'), 0, 'R', 0, 0, '', '', true);
                $pdf->MultiCell(20, 5, '', 0, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(20, 5, '', 0, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(16, 5, '', 0, 'C', 0, 0, '', '', true);
                //$pdf->ln(4);

                $condicionPartida = strlen($value_distribucion_seis->tx_nombre);
                if ($condicionPartida >= 70) {
                  $pdf->ln(8);
                }else {
                  $pdf->ln(4);
                }

                $start_y = $pdf->GetY();

                if ($start_y >= 260) {

                  // reset font stretching  reset font spacing
                  $pdf->setFontStretching(100);
                  $pdf->setFontSpacing(0);
                  $pdf->SetLineWidth(0.150);
                  $pdf->setCellHeightRatio(2);

                  $pdf->AddPage();

                  $pdf->SetFont('','B',8);
                  $pdf->MultiCell(55, 5, 'GOBERNACIÓN DEL ESTADO ZULIA', 0, 'L', 0, 0, '', '', true);
                  $pdf->SetFont('','B',8);
                  $pdf->setCellHeightRatio(1);
                  $pdf->MultiCell(90, 5, 'CRÉDITOS PRESUPUESTARIOS DEL PROYECTO Y/O ACCIÓN CENTRALIZADA A NIVEL DE PROYECTOS Y/O ACCIÓN CENTRALIZADA', 0, 'C', 0, 0, '', '', true);
                  $pdf->setCellHeightRatio(2);
                  $pdf->ln(8);
                  $pdf->SetFont('','B',8);
                  $pdf->MultiCell(55, 5, 'PRESUPUESTO '.Session::get("ejercicio"), 0, 'L', 0, 0, '', '', true);
                  $pdf->MultiCell(90, 5, '(EN BOLÍVARES)', 0, 'C', 0, 0, '', '', true);
                  $pdf->ln(-10);
                  $pdf->MultiCell(196, 18, '', 1, 'C', 0, 0, '', '', true);
                  $pdf->ln(19);
                  $pdf->SetFont('','B',7);
                  $pdf->setCellHeightRatio(1.2);

                  $pdf->MultiCell(29, 10, chr(10).'SECTOR', 1, 'L', 0, 0, '', '', true);
                  $pdf->SetFont('','',6);
                  $pdf->MultiCell(91, 10, chr(10).$value_distribucion_tres->co_sector.' - '.mb_strtoupper($value_distribucion_tres->tx_descripcion, 'UTF-8'), 1, 'L', 0, 0, '', '', true);
                  $pdf->SetFont('','B',7);
                  $pdf->MultiCell(20, 20, chr(10).'UNIDAD EJECUTORA', 1, 'C', 0, 0, '', '', true);
                  $pdf->SetFont('','',6);
                  $pdf->MultiCell(56, 20, chr(10).$value_distribucion_tres->id_ejecutor.' - '.mb_strtoupper($value_distribucion_tres->tx_ejecutor, 'UTF-8'), 1, 'L', 0, 0, '', '', true);
                  $pdf->ln(10);
                  $pdf->SetFont('','B',6);
                  $pdf->MultiCell(29, 10, chr(10).'PROYECTO Y/O ACCIÓN CENTRALIZADA', 1, 'L', 0, 0, '', '', true);
                  $pdf->SetFont('','',6);
                  $pdf->MultiCell(91, 10, substr($value_distribucion_tres->nu_original, -2).' - '.mb_strtoupper($value_distribucion_tres->de_nombre, 'UTF-8'), 1, 'L', 0, 0, '', '', true);
                  $pdf->ln(10);
                  $pdf->MultiCell(196, 220, '', 1, 'C', 0, 0, '', '', true);
                  $pdf->ln(0);
                  $pdf->SetFont('','B',6);
                  $pdf->MultiCell(7, 5, '', 0, 'L', 0, 0, '', '', true);
                  $pdf->MultiCell(22, 5, 'SUB - PARTIDA', 1, 'L', 0, 0, '', '', true);
                  $pdf->ln(30);
                  $pdf->SetFont('','B',6);
                  $pdf->StartTransform();
                  $pdf->Rotate(90);
                  $pdf->MultiCell(30, 7, 'PARTIDA', 1, 'L', 0, 0, '', '', true);
                  $pdf->ln(7);
                  $pdf->MultiCell(25, 5, 'GENERICA', 1, 'L', 0, 0, '', '', true);
                  $pdf->MultiCell(5, 5, '', 0, 'L', 0, 0, '', '', true);
                  $pdf->ln(5);
                  $pdf->MultiCell(25, 5, 'ESPECIFICA', 1, 'L', 0, 0, '', '', true);
                  $pdf->MultiCell(5, 5, '', 0, 'L', 0, 0, '', '', true);
                  $pdf->ln(5);
                  $pdf->MultiCell(25, 5, 'SUB ESPECIFICA', 1, 'L', 0, 0, '', '', true);
                  $pdf->MultiCell(5, 5, '', 0, 'L', 0, 0, '', '', true);
                  $pdf->ln(5);
                  $pdf->MultiCell(25, 7, 'SUB SUB ESPECIFICA', 1, 'L', 0, 0, '', '', true);
                  $pdf->MultiCell(5, 5, '', 0, 'L', 0, 0, '', '', true);
                  $pdf->ln(30);
                  $pdf->StopTransform();
                  $pdf->ln(-82);
                  $pdf->SetFont('','B',8);
                  $pdf->setCellHeightRatio(10);
                  $pdf->MultiCell(29, 30, '', 0, 'C', 0, 0, '', '', true);
                  $pdf->MultiCell(91, 30, 'DENOMINACIÓN', 1, 'C', 0, 0, '', '', true);
                  $pdf->SetFont('','B',6);
                  $pdf->setCellHeightRatio(1.2);
                  $pdf->MultiCell(20, 30, chr(10).chr(10).chr(10).'TOTAL PROYECTO Y/O ACCIÓN CENTRALIZADA', 1, 'C', 0, 0, '', '', true);
                  $pdf->MultiCell(56, 5, chr(10).'ACCIÓNES ESPECIFICAS', 0, 'C', 0, 0, '', '', true);
                  $pdf->ln(0);
                  $pdf->MultiCell(140, 30, '', 0, 'C', 0, 0, '', '', true);
                  $pdf->ln(5);
                  $pdf->MultiCell(140, 30, '', 0, 'C', 0, 0, '', '', true);
                  $pdf->MultiCell(20, 25, '', 1, 'C', 0, 0, '', '', true);
                  $pdf->MultiCell(20, 25, '', 1, 'C', 0, 0, '', '', true);
                  $pdf->MultiCell(16, 25, '', 1, 'C', 0, 0, '', '', true);
                  $pdf->ln(25);
                  $pdf->setCellHeightRatio(1);
                  $pdf->MultiCell(7, 190, '', 1, 'C', 0, 0, '', '', true);
                  $pdf->MultiCell(5, 190, '', 1, 'C', 0, 0, '', '', true);
                  $pdf->MultiCell(5, 190, '', 1, 'C', 0, 0, '', '', true);
                  $pdf->MultiCell(5, 190, '', 1, 'C', 0, 0, '', '', true);
                  $pdf->MultiCell(7, 190, '', 1, 'C', 0, 0, '', '', true);
                  $pdf->MultiCell(91, 190, '', 1, 'C', 0, 0, '', '', true);
                  $pdf->MultiCell(20, 190, '', 1, 'C', 0, 0, '', '', true);
                  $pdf->MultiCell(20, 190, '', 1, 'C', 0, 0, '', '', true);
                  $pdf->MultiCell(20, 190, '', 1, 'C', 0, 0, '', '', true);
                  $pdf->MultiCell(16, 190, '', 1, 'C', 0, 0, '', '', true);
                  $pdf->ln(2);
                  $pdf->SetFont('','',7);
                  $pdf->setCellHeightRatio(1);

                }

                $distribucion_siete = vista_distribucion_presupuesto::
                join('mantenimiento.tab_partidas as t01', 't01.co_partida', '=', DB::raw('left(public.vista_distribucion_presupuesto.co_partida, 3)'))
                ->join('mantenimiento.tab_partidas as t02', 't02.co_partida', '=', DB::raw('left(public.vista_distribucion_presupuesto.co_partida, 5)'))
                ->join('mantenimiento.tab_partidas as t03', 't03.co_partida', '=', DB::raw('left(public.vista_distribucion_presupuesto.co_partida, 7)'))
                ->join('mantenimiento.tab_partidas as t04', 't04.co_partida', '=', DB::raw('left(public.vista_distribucion_presupuesto.co_partida, 9)'))
                ->select( 't04.co_partida', 't04.tx_nombre',  DB::raw('sum(monto) as mo_partida') )
                ->where('ef_uno', '=', Session::get('ejercicio'))
                ->where('t01.id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
                ->where('t02.id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
                ->where('t03.id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
                ->where('t04.id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
                ->where('co_sector', '=', $value_distribucion_uno->co_sector)
                ->where('nu_original', '=', $value_distribucion_dos->nu_original)
                ->where('id_ejecutor', '=', $value_distribucion_tres->id_ejecutor)
                ->where('t01.co_partida', '=', $value_distribucion_cuatro->co_partida)
                ->where('t02.co_partida', '=', $value_distribucion_cinco->co_partida)
                ->where('t03.co_partida', '=', $value_distribucion_seis->co_partida)
                ->groupBy('t04.co_partida')
                ->groupBy('t04.tx_nombre')
                ->orderBy('co_partida','ASC')
                ->get();

                foreach ($distribucion_siete as $key => $value_distribucion_siete) {

                  $pdf->SetFont('','',6);
                  $pdf->MultiCell(7, 5, substr(trim($value_distribucion_siete->co_partida), 0, 3), 0, 'L', 0, 0, '', '', true);
                  $pdf->MultiCell(5, 5, substr(substr(trim($value_distribucion_siete->co_partida), 0, 5), 3), 0, 'L', 0, 0, '', '', true);
                  $pdf->MultiCell(5, 5, substr(substr(trim($value_distribucion_siete->co_partida), 0, 7), 5), 0, 'L', 0, 0, '', '', true);
                  $pdf->MultiCell(5, 5, substr(substr(trim($value_distribucion_siete->co_partida), 0, 9), 7), 0, 'L', 0, 0, '', '', true);
                  $pdf->MultiCell(7, 5, '', 0, 'C', 0, 0, '', '', true);
                  $pdf->MultiCell(91, 5, $value_distribucion_siete->tx_nombre, 0, 'L', 0, 0, '', '', true);
                  $pdf->MultiCell(20, 5, number_format($value_distribucion_siete->mo_partida, 2, ',', '.'), 0, 'R', 0, 0, '', '', true);
                  $pdf->MultiCell(20, 5, '', 0, 'C', 0, 0, '', '', true);
                  $pdf->MultiCell(20, 5, '', 0, 'C', 0, 0, '', '', true);
                  $pdf->MultiCell(16, 5, '', 0, 'C', 0, 0, '', '', true);
                  //$pdf->ln(4);

                  $condicionPartida = strlen($value_distribucion_siete->tx_nombre);
                  if ($condicionPartida >= 70) {
                    $pdf->ln(8);
                  }else {
                    $pdf->ln(4);
                  }

                  $start_y = $pdf->GetY();

                  if ($start_y >= 260) {

                    // reset font stretching  reset font spacing
                    $pdf->setFontStretching(100);
                    $pdf->setFontSpacing(0);
                    $pdf->SetLineWidth(0.150);
                    $pdf->setCellHeightRatio(2);

                    $pdf->AddPage();

                    $pdf->SetFont('','B',8);
                    $pdf->MultiCell(55, 5, 'GOBERNACIÓN DEL ESTADO ZULIA', 0, 'L', 0, 0, '', '', true);
                    $pdf->SetFont('','B',8);
                    $pdf->setCellHeightRatio(1);
                    $pdf->MultiCell(90, 5, 'CRÉDITOS PRESUPUESTARIOS DEL PROYECTO Y/O ACCIÓN CENTRALIZADA A NIVEL DE PROYECTOS Y/O ACCIÓN CENTRALIZADA', 0, 'C', 0, 0, '', '', true);
                    $pdf->setCellHeightRatio(2);
                    $pdf->ln(8);
                    $pdf->SetFont('','B',8);
                    $pdf->MultiCell(55, 5, 'PRESUPUESTO '.Session::get("ejercicio"), 0, 'L', 0, 0, '', '', true);
                    $pdf->MultiCell(90, 5, '(EN BOLÍVARES)', 0, 'C', 0, 0, '', '', true);
                    $pdf->ln(-10);
                    $pdf->MultiCell(196, 18, '', 1, 'C', 0, 0, '', '', true);
                    $pdf->ln(19);
                    $pdf->SetFont('','B',7);
                    $pdf->setCellHeightRatio(1.2);

                    $pdf->MultiCell(29, 10, chr(10).'SECTOR', 1, 'L', 0, 0, '', '', true);
                    $pdf->SetFont('','',6);
                    $pdf->MultiCell(91, 10, chr(10).$value_distribucion_tres->co_sector.' - '.mb_strtoupper($value_distribucion_tres->tx_descripcion, 'UTF-8'), 1, 'L', 0, 0, '', '', true);
                    $pdf->SetFont('','B',7);
                    $pdf->MultiCell(20, 20, chr(10).'UNIDAD EJECUTORA', 1, 'C', 0, 0, '', '', true);
                    $pdf->SetFont('','',6);
                    $pdf->MultiCell(56, 20, chr(10).$value_distribucion_tres->id_ejecutor.' - '.mb_strtoupper($value_distribucion_tres->tx_ejecutor, 'UTF-8'), 1, 'L', 0, 0, '', '', true);
                    $pdf->ln(10);
                    $pdf->SetFont('','B',6);
                    $pdf->MultiCell(29, 10, chr(10).'PROYECTO Y/O ACCIÓN CENTRALIZADA', 1, 'L', 0, 0, '', '', true);
                    $pdf->SetFont('','',6);
                    $pdf->MultiCell(91, 10, substr($value_distribucion_tres->nu_original, -2).' - '.mb_strtoupper($value_distribucion_tres->de_nombre, 'UTF-8'), 1, 'L', 0, 0, '', '', true);
                    $pdf->ln(10);
                    $pdf->MultiCell(196, 220, '', 1, 'C', 0, 0, '', '', true);
                    $pdf->ln(0);
                    $pdf->SetFont('','B',6);
                    $pdf->MultiCell(7, 5, '', 0, 'L', 0, 0, '', '', true);
                    $pdf->MultiCell(22, 5, 'SUB - PARTIDA', 1, 'L', 0, 0, '', '', true);
                    $pdf->ln(30);
                    $pdf->SetFont('','B',6);
                    $pdf->StartTransform();
                    $pdf->Rotate(90);
                    $pdf->MultiCell(30, 7, 'PARTIDA', 1, 'L', 0, 0, '', '', true);
                    $pdf->ln(7);
                    $pdf->MultiCell(25, 5, 'GENERICA', 1, 'L', 0, 0, '', '', true);
                    $pdf->MultiCell(5, 5, '', 0, 'L', 0, 0, '', '', true);
                    $pdf->ln(5);
                    $pdf->MultiCell(25, 5, 'ESPECIFICA', 1, 'L', 0, 0, '', '', true);
                    $pdf->MultiCell(5, 5, '', 0, 'L', 0, 0, '', '', true);
                    $pdf->ln(5);
                    $pdf->MultiCell(25, 5, 'SUB ESPECIFICA', 1, 'L', 0, 0, '', '', true);
                    $pdf->MultiCell(5, 5, '', 0, 'L', 0, 0, '', '', true);
                    $pdf->ln(5);
                    $pdf->MultiCell(25, 7, 'SUB SUB ESPECIFICA', 1, 'L', 0, 0, '', '', true);
                    $pdf->MultiCell(5, 5, '', 0, 'L', 0, 0, '', '', true);
                    $pdf->ln(30);
                    $pdf->StopTransform();
                    $pdf->ln(-82);
                    $pdf->SetFont('','B',8);
                    $pdf->setCellHeightRatio(10);
                    $pdf->MultiCell(29, 30, '', 0, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(91, 30, 'DENOMINACIÓN', 1, 'C', 0, 0, '', '', true);
                    $pdf->SetFont('','B',6);
                    $pdf->setCellHeightRatio(1.2);
                    $pdf->MultiCell(20, 30, chr(10).chr(10).chr(10).'TOTAL PROYECTO Y/O ACCIÓN CENTRALIZADA', 1, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(56, 5, chr(10).'ACCIÓNES ESPECIFICAS', 0, 'C', 0, 0, '', '', true);
                    $pdf->ln(0);
                    $pdf->MultiCell(140, 30, '', 0, 'C', 0, 0, '', '', true);
                    $pdf->ln(5);
                    $pdf->MultiCell(140, 30, '', 0, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(20, 25, '', 1, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(20, 25, '', 1, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(16, 25, '', 1, 'C', 0, 0, '', '', true);
                    $pdf->ln(25);
                    $pdf->setCellHeightRatio(1);
                    $pdf->MultiCell(7, 190, '', 1, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(5, 190, '', 1, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(5, 190, '', 1, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(5, 190, '', 1, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(7, 190, '', 1, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(91, 190, '', 1, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(20, 190, '', 1, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(20, 190, '', 1, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(20, 190, '', 1, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(16, 190, '', 1, 'C', 0, 0, '', '', true);
                    $pdf->ln(2);
                    $pdf->SetFont('','',7);
                    $pdf->setCellHeightRatio(1);

                  }

                }

              }

            }

          }

          // reset font stretching  reset font spacing
          $pdf->setFontStretching(100);
          $pdf->setFontSpacing(0);
          $pdf->SetLineWidth(0.150);
          $pdf->setCellHeightRatio(2);

        }

      }

    }

    //Cierre de Reporte
    $pdf->lastPage();
    $pdf->output('DISTRIBUCION_DE_PRESUPUESTO_'.Session::get("ejercicio").'_'.date("H:i:s").'.pdf', 'D');

  }
}
