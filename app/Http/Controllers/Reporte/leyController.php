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
use matriz\Models\Proyecto\vista_relacion_transferencia;
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

      $filtro = array();
      $total = 0;

      foreach ($tab_presupuesto_ingreso as $key => $value) {

        $data = DB::select( DB::raw("
            WITH partidas (co_partida) AS (
                    SELECT co_partida, tx_nombre, ace_mov, id_tab_ejercicio_fiscal, 1 as nivel
                  	FROM mantenimiento.tab_partidas
                  	WHERE co_partida = left(:partida, 3)
                  	UNION ALL
                  	SELECT co_partida, tx_nombre, ace_mov, id_tab_ejercicio_fiscal, 2 as nivel
                  	FROM mantenimiento.tab_partidas
                  	WHERE co_partida = left(:partida, 5)
                  	UNION ALL
                  	SELECT co_partida, tx_nombre, ace_mov, id_tab_ejercicio_fiscal, 3 as nivel
                  	FROM mantenimiento.tab_partidas
                  	WHERE co_partida = left(:partida, 7)
                  	UNION ALL
                  	SELECT co_partida, tx_nombre, ace_mov, id_tab_ejercicio_fiscal, 4 as nivel
                  	FROM mantenimiento.tab_partidas
                  	WHERE co_partida = left(:partida, 12)
                 )
            SELECT co_partida, tx_nombre, ace_mov, id_tab_ejercicio_fiscal, nivel
            FROM partidas
            WHERE
            id_tab_ejercicio_fiscal = :ejercicio;
  				"), array( 'partida' => $value->nu_partida , 'ejercicio' => Session::get('ejercicio')));

          foreach ($data as $key => $values) {

          if (in_array($values->co_partida, $filtro)) {

          }else{

            $filtro[] = $values->co_partida;


            $tabla_presupuesto_ingreso.='
            <tr>
              <td style="text-align: center;width:5%">'.substr($values->co_partida, 0, 3).'</td>
              <td style="text-align: center;width:5%">'.substr(substr($values->co_partida, 0, 5), 3).'</td>
              <td style="text-align: center;width:5%">'.substr(substr($values->co_partida, 0, 7), 5).'</td>
              <td style="text-align: center;width:5%">'.substr(substr($values->co_partida, 0, 9), 7).'</td>
              <td style="text-align: left;width:60%">'.$values->tx_nombre.'</td>';

              if ($values->nivel==4) {
                $tabla_presupuesto_ingreso.='
                  <td style="text-align: rigth;width:20%"><strong>'.number_format($value->mo_partida, 2, ',', '.').'</strong></td>';
              }elseif($values->nivel==1){
                $tabla_presupuesto_ingreso.='
                  <td style="text-align: rigth;width:20%"></td>';
              }elseif($values->nivel==2){
                $tabla_presupuesto_ingreso.='
                  <td style="text-align: rigth;width:20%"></td>';
              }elseif($values->nivel==3){
                $tabla_presupuesto_ingreso.='
                  <td style="text-align: rigth;width:20%"></td>';
              }

            $tabla_presupuesto_ingreso.='</tr>';

          }

          $total = $total + $value->mo_partida;

        }

      }

      $tabla_presupuesto_ingreso.='
      <tr>
      <td style="text-align: left;width:80%" colspan="5"><b>TOTAL</b></td>
      <td style="text-align: rigth;width:20%"><b>'.number_format($total, 2, ',', '.').'</b></td>
      </tr>
      </tbody>
      </table>';

      $pdf->writeHTML(Helper::htmlComprimir($tabla_presupuesto_ingreso), true, false, false, false, '');

      // reset font stretching  reset font spacing
      $pdf->setFontStretching(100);
      $pdf->setFontSpacing(0);


      $pdf->AddPage();

      /******Portada Titulo III*********/
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
      $pdf->writeHTML('<b><u>TITULO III<u/></b>', true, false, true, false, 'R');
      $pdf->ln(0);
      $pdf->MultiCell(195, 5, 'PRESUPUESTO DE GASTOS', 0, 'R', 0, 0, '', '', true);
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

      $objetivos = tab_objetivo_sectorial::join('mantenimiento.tab_sectores as t01', 't01.id', '=', 'mantenimiento.tab_objetivo_sectorial.id_tab_sectores')
  		->select( 'mantenimiento.tab_objetivo_sectorial.id', 'id_tab_ejercicio_fiscal',
      'id_tab_sectores', 'de_objetivo_sectorial', 'tx_codigo', 'tx_descripcion' )
  		->where('id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
  		->orderBy('tx_codigo','ASC')
  		->get();

      foreach ($objetivos as $key => $value) {

        $pdf->AddPage();

        /******Portada Titulo Sectores*********/
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
        $pdf->writeHTML('<b><u>SECTOR: '.$value->tx_codigo.'<u/></b>', true, false, true, false, 'R');
        $pdf->ln(0);
        $pdf->MultiCell(195, 5, mb_strtoupper($value->tx_descripcion, 'UTF-8'), 0, 'R', 0, 0, '', '', true);
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

        $pdf->AddPage();

        $pdf->SetFont('','B',8);
        $pdf->MultiCell(55, 5, 'GOBERNACIÓN DEL ESTADO ZULIA', 0, 'L', 0, 0, '', '', true);
        $pdf->SetFont('','B',11);
        $pdf->MultiCell(90, 5, 'OBJETIVOS SECTORIALES', 0, 'C', 0, 0, '', '', true);
        $pdf->ln(8);
        $pdf->SetFont('','B',8);
        $pdf->MultiCell(55, 5, 'PRESUPUESTO '.Session::get("ejercicio"), 0, 'L', 0, 0, '', '', true);
        $pdf->ln(-10);
        $pdf->MultiCell(196, 18, '', 1, 'C', 0, 0, '', '', true);
        $pdf->ln(19);
        $pdf->SetFont('','',8);

        $tabla_objetivo_sectorial = '
        <table border="0.5" style="width:100%" cellspacing="0" cellpadding="4">
        <thead>
        <tr style="font-size: 8px;">
          <th style="text-align: center;width:20%" rowspan="2"><strong><br>SECTOR</strong></th>
          <th style="text-align: center;width:20%"><strong>CODIGO</strong></th>
          <th style="text-align: left;width:60%"><strong>DENOMINACION</strong></th>
        </tr>
        <tr style="font-size: 8px;">
          <th style="text-align: center;width:20%">'.$value->tx_codigo.'</th>
          <th style="text-align: left;width:60%">'.mb_strtoupper($value->tx_descripcion, 'UTF-8').'</th>
        </tr>
        </thead>
        <tbody>
        <tr style="font-size: 9px;">
          <td colspan="3" style="text-align: center;width:100%"><strong>DESCRIPCIÓN</strong></td>
        </tr>
        <tr style="font-size: 7px;">
          <td colspan="3" style="text-align: justify; width:100%; padding: 10px; line-height: 200%;">'.nl2br($value->de_objetivo_sectorial).'</td>
        </tr>
        </tbody>
        </table>';

        $pdf->writeHTML(Helper::htmlComprimir($tabla_objetivo_sectorial), true, false, false, false, '');

        /*Listado de Proyectos*/
        // reset font stretching  reset font spacing
        $pdf->setFontStretching(100);
        $pdf->setFontSpacing(0);

        $pr_lista = tab_proyecto::join('mantenimiento.tab_sectores as t01', 't01.tx_codigo', '=', 'public.t26_proyectos.clase_sector')
        ->join('mantenimiento.tab_ejecutores as t02', 't02.id_ejecutor', '=', 'public.t26_proyectos.id_ejecutor')
        ->select( 'id_proyecto', 'nombre', 'clase_sector', 'tx_descripcion',
        'public.t26_proyectos.id_ejecutor', 'tx_ejecutor' ,'public.t26_proyectos.descripcion')
        ->where('id_ejercicio', '=', Session::get('ejercicio'))
        ->where('clase_sector', '=', $value->tx_codigo)
        ->where('edo_reg', '=', TRUE)
        ->orderBy('id_proyecto','ASC')
        ->get();

        foreach ($pr_lista as $key => $value_pr) {

          $pdf->AddPage();
          /******Portada Titulo Sectores*********/
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
          $pdf->ln(200);
          $pdf->SetFont('','B',12);
          //$pdf->MultiCell(190, 5, 'TITULO I', 0, 'R', 0, 0, '', '', true);
          $pdf->writeHTML('<b><u>SECTOR: '.$value_pr->clase_sector.'<u/></b>', true, false, true, false, 'L');
          $pdf->ln(0);
          $pdf->writeHTML('<b><u>PROYECTO Y/O ACCIÓN CENTRALIZADA: '.substr($value_pr->id_proyecto, -3).'<u/></b>', true, false, true, false, 'L');
          $pdf->ln(0);
          $pdf->MultiCell(195, 5, mb_strtoupper($value_pr->nombre, 'UTF-8'), 0, 'L', 0, 0, '', '', true);
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

          $pdf->AddPage();

          $pdf->SetFont('','B',8);
          $pdf->MultiCell(55, 5, 'GOBERNACIÓN DEL ESTADO ZULIA', 0, 'L', 0, 0, '', '', true);
          $pdf->SetFont('','B',10);
          $pdf->setCellHeightRatio(1);
          $pdf->MultiCell(70, 5, 'DESCRIPCIÓN DEL PROYECTO Y/O ACCIÓN CENTRALIZADA', 0, 'C', 0, 0, '', '', true);
          $pdf->setCellHeightRatio(2);
          $pdf->ln(8);
          $pdf->SetFont('','B',8);
          $pdf->MultiCell(55, 5, 'PRESUPUESTO '.Session::get("ejercicio"), 0, 'L', 0, 0, '', '', true);
          //$pdf->MultiCell(90, 5, '', 0, 'C', 0, 0, '', '', true);
          $pdf->ln(-10);
          $pdf->MultiCell(196, 18, '', 1, 'C', 0, 0, '', '', true);
          $pdf->ln(19);
          $pdf->SetFont('','',8);

          $tabla_objetivo_proyecto = '
          <table border="0.5" style="width:100%" cellspacing="0" cellpadding="4">
          <thead>
          <tr style="font-size: 8px;">
            <th style="text-align: center;width:20%"></th>
            <th style="text-align: center;width:20%"><strong>CODIGO</strong></th>
            <th style="text-align: left;width:60%"><strong>DENOMINACION</strong></th>
          </tr>
          <tr style="font-size: 8px;">
            <th style="text-align: left;width:20%"><strong>SECTOR:</strong></th>
            <th style="text-align: center;width:20%">'.$value_pr->clase_sector.'</th>
            <th style="text-align: left;width:60%">'.mb_strtoupper($value_pr->tx_descripcion, 'UTF-8').'</th>
          </tr>
          <tr style="font-size: 8px;">
            <th style="text-align: left;width:20%"><strong>PROYECTO Y/O ACCIÓN CENTRALIZADA:</strong></th>
            <th style="text-align: center;width:20%">'.substr($value_pr->id_proyecto, -3).'</th>
            <th style="text-align: left;width:60%">'.mb_strtoupper($value_pr->nombre, 'UTF-8').'</th>
          </tr>
          <tr style="font-size: 8px;">
            <th style="text-align: left;width:20%"><strong>UNIDAD EJECUTORA:</strong></th>
            <th style="text-align: center;width:20%">'.$value_pr->id_ejecutor.'</th>
            <th style="text-align: left;width:60%">'.mb_strtoupper($value_pr->tx_ejecutor, 'UTF-8').'</th>
          </tr>
          </thead>
          <tbody>
          <tr style="font-size: 9px;">
            <td colspan="3" style="text-align: center;width:100%"><strong>DESCRIPCIÓN</strong></td>
          </tr>
          <tr style="font-size: 7px;">
            <td colspan="3" style="text-align: justify; width:100%; padding: 10px; line-height: 200%;">'.nl2br($value_pr->descripcion).'</td>
          </tr>
          </tbody>
          </table>';

          $pdf->writeHTML(Helper::htmlComprimir($tabla_objetivo_proyecto), true, false, false, false, '');

          $pdf->AddPage();

          $pdf->SetFont('','B',8);
          $pdf->MultiCell(55, 5, 'GOBERNACIÓN DEL ESTADO ZULIA', 0, 'L', 0, 0, '', '', true);
          $pdf->SetFont('','B',10);
          $pdf->setCellHeightRatio(1);
          $pdf->MultiCell(90, 5, 'CREDITOS PRESUPUESTARIOS DEL PROYECTO Y/O ACCIÓN CENTRALIZADA', 0, 'C', 0, 0, '', '', true);
          $pdf->setCellHeightRatio(2);
          $pdf->ln(8);
          $pdf->SetFont('','B',8);
          $pdf->MultiCell(55, 5, 'PRESUPUESTO '.Session::get("ejercicio"), 0, 'L', 0, 0, '', '', true);
          $pdf->MultiCell(90, 5, '(EN BOLÍVARES) ', 0, 'C', 0, 0, '', '', true);
          $pdf->ln(-10);
          $pdf->MultiCell(196, 18, '', 1, 'C', 0, 0, '', '', true);
          $pdf->ln(19);
          $pdf->SetFont('','',8);
          $start_y = 0;
          $movimiento = 0;

          $tabla_pr_lista = '
          <table border="0.5" style="width:100%" cellspacing="0" cellpadding="4">
          <thead>
          <tr style="font-size: 8px;">
            <th style="text-align: center;width:20%" rowspan="2"><strong><br>SECTOR / PROYECTO Y/O A. CENTRALIZADA</strong></th>
            <th style="text-align: center;width:20%"><strong>CODIGO</strong></th>
            <th style="text-align: left;width:60%"><strong>DENOMINACION</strong></th>
          </tr>
          <tr style="font-size: 8px;">
            <th style="text-align: center;width:20%">'.$value_pr->clase_sector.'.'.substr($value_pr->id_proyecto, -3).'</th>
            <th style="text-align: left;width:60%">'.mb_strtoupper($value_pr->nombre, 'UTF-8').'</th>
          </tr>
          <tr>
            <th colspan="2" style="text-align: center;width:80%"><strong>PARTIDA</strong></th>
            <th rowspan="2" style="text-align: center;width:20%"><strong>ASIGNACION PRESUPUESTARIA</strong></th>
          </tr>
          <tr>
            <th style="text-align: center;width:20%"><strong>CODIGO</strong></th>
            <th style="text-align: center;width:60%"><strong>DENOMINACION</strong></th>
          </tr>
          </thead>
          <tbody>';

          $pr_lista_partida = tab_proyecto_ae_partida::join('public.t39_proyecto_acc_espec as t01', 't01.co_proyecto_acc_espec', '=', 'public.t42_proyecto_acc_espec_partida.co_proyecto_acc_espec')
          ->join('mantenimiento.tab_partidas as t02', 't02.co_partida', '=', DB::raw('left(public.t42_proyecto_acc_espec_partida.co_partida, 3)'))
          ->select( DB::raw('t02.co_partida as partida'), 'tx_nombre',
          DB::raw('sum(public.t42_proyecto_acc_espec_partida.nu_monto) as mo_partida'))
          ->where('t02.id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
          ->where('t01.id_proyecto', '=', $value_pr->id_proyecto)
          ->where('public.t42_proyecto_acc_espec_partida.edo_reg', '=', TRUE)
          ->where('t01.edo_reg', '=', TRUE)
          ->groupBy('partida')
          ->groupBy('tx_nombre')
          ->orderBy('partida','ASC')
          ->get();

          $total_partida_pr = 0;

          foreach ($pr_lista_partida as $key => $value_pr_partida) {

            $tabla_pr_lista.='
            <tr>
              <td style="text-align: center;width:20%">'.$value_pr_partida->partida.'</td>
              <td style="text-align: left;width:60%">'.mb_strtoupper($value_pr_partida->tx_nombre, 'UTF-8').'</td>
              <td style="text-align: right;width:20%">'.number_format($value_pr_partida->mo_partida, 2, ',', '.').'</td>
            </tr>';

            $total_partida_pr = $total_partida_pr + $value_pr_partida->mo_partida;

          }

          $tabla_pr_lista.='
          <tr>
            <td style="text-align: rigth;width:80%" colspan="5"><b>TOTAL</b></td>
            <td style="text-align: rigth;width:20%"><b>'.number_format($total_partida_pr, 2, ',', '.').'</b></td>
          </tr>
          </tbody>
          </table>';

          $pdf->writeHTML(Helper::htmlComprimir($tabla_pr_lista), true, false, false, false, '');

        }

        // reset font stretching  reset font spacing
        $pdf->setFontStretching(100);
        $pdf->setFontSpacing(0);

        $ac_lista = tab_ac::join('mantenimiento.tab_sectores as t01', 't01.id', '=', 'public.t46_acciones_centralizadas.id_subsector')
        ->join('mantenimiento.tab_ac_predefinida as t02', 't02.id', '=', 'public.t46_acciones_centralizadas.id_accion')
        ->select( 'id_accion', 'de_nombre', 'nu_original', 'co_sector' )
        ->where('id_ejercicio', '=', Session::get('ejercicio'))
        ->where('co_sector', '=', $value->tx_codigo)
        ->groupBy('id_accion')
        ->groupBy('de_nombre')
        ->groupBy('nu_original')
        ->groupBy('co_sector')
        ->orderBy('co_sector','ASC')
        ->orderBy('id_accion','ASC')
        ->get();

        foreach ($ac_lista as $key => $value_ac) {

          $pdf->AddPage();
          /******Portada Titulo Sectores*********/
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
          $pdf->writeHTML('<b><u>SECTOR: '.$value_ac->co_sector.'<u/></b>', true, false, true, false, 'L');
          $pdf->ln(0);
          $pdf->writeHTML('<b><u>PROYECTO Y/O ACCIÓN CENTRALIZADA: '.$value_ac->nu_original.'<u/></b>', true, false, true, false, 'L');
          $pdf->ln(0);
          $pdf->MultiCell(195, 5, mb_strtoupper($value_ac->de_nombre, 'UTF-8'), 0, 'L', 0, 0, '', '', true);
          $pdf->ln(20);
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
          $pdf->SetFont('','B',10);
          $pdf->setCellHeightRatio(1);
          $pdf->MultiCell(90, 5, 'CREDITOS PRESUPUESTARIOS DEL PROYECTO Y/O ACCIÓN CENTRALIZADA', 0, 'C', 0, 0, '', '', true);
          $pdf->setCellHeightRatio(2);
          $pdf->ln(8);
          $pdf->SetFont('','B',8);
          $pdf->MultiCell(55, 5, 'PRESUPUESTO '.Session::get("ejercicio"), 0, 'L', 0, 0, '', '', true);
          $pdf->MultiCell(90, 5, '(EN BOLÍVARES) ', 0, 'C', 0, 0, '', '', true);
          $pdf->ln(-10);
          $pdf->MultiCell(196, 18, '', 1, 'C', 0, 0, '', '', true);
          $pdf->ln(19);
          $pdf->SetFont('','',8);

          $tabla_ac_lista = '
          <table border="0.5" style="width:100%" cellspacing="0" cellpadding="4">
          <thead>
          <tr style="font-size: 8px;">
            <th style="text-align: center;width:20%" rowspan="2"><strong><br>SECTOR / PROYECTO Y/O A. CENTRALIZADA</strong></th>
            <th style="text-align: center;width:20%"><strong>CODIGO</strong></th>
            <th style="text-align: left;width:60%"><strong>DENOMINACION</strong></th>
          </tr>
          <tr style="font-size: 8px;">
            <th style="text-align: center;width:20%">'.$value_ac->co_sector.'.'.$value_ac->nu_original.'</th>
            <th style="text-align: left;width:60%">'.mb_strtoupper($value_ac->de_nombre, 'UTF-8').'</th>
          </tr>
          <tr>
            <th colspan="2" style="text-align: center;width:80%"><strong>PARTIDA</strong></th>
            <th rowspan="2" style="text-align: center;width:20%"><strong>ASIGNACION PRESUPUESTARIA</strong></th>
          </tr>
          <tr>
            <th style="text-align: center;width:20%"><strong>CODIGO</strong></th>
            <th style="text-align: center;width:60%"><strong>DENOMINACION</strong></th>
          </tr>
          </thead>
          <tbody>';

          $ac_lista_partida = tab_ac_ae_partida::join('public.t46_acciones_centralizadas as t01', 't01.id', '=', 'public.t54_ac_ae_partidas.id_accion_centralizada')
          ->join('mantenimiento.tab_sectores as t02', 't02.id', '=', 't01.id_subsector')
          ->join('mantenimiento.tab_partidas as t03', 't03.co_partida', '=', DB::raw('left(public.t54_ac_ae_partidas.co_partida, 3)'))
          //->join(DB::raw('inner join mantenimiento.tab_partidas as t03 on left(public.t54_ac_ae_partidas.co_partida, 3) = t03.co_partida'))
          ->select( 'co_sector', 't01.id_accion', DB::raw('left(public.t54_ac_ae_partidas.co_partida, 3) as partida'), 'tx_nombre', DB::raw('sum(t01.monto) as mo_partida') )
          ->where('t01.id_ejercicio', '=', Session::get('ejercicio'))
          ->where('t01.id_accion', '=', $value_ac->id_accion)
          ->where('t02.co_sector', '=', $value_ac->co_sector)
          ->groupBy('co_sector')
          ->groupBy('t01.id_accion')
          ->groupBy('partida')
          ->groupBy('tx_nombre')
          ->orderBy('partida','ASC')
          ->get();

          $total_partida = 0;

          foreach ($ac_lista_partida as $key => $value_ac_partida) {

            $tabla_ac_lista.='
            <tr>
              <td style="text-align: center;width:20%">'.$value_ac_partida->partida.'</td>
              <td style="text-align: left;width:60%">'.mb_strtoupper($value_ac_partida->tx_nombre, 'UTF-8').'</td>
              <td style="text-align: right;width:20%">'.number_format($value_ac_partida->mo_partida, 2, ',', '.').'</td>
            </tr>';

            $total_partida = $total_partida + $value_ac_partida->mo_partida;

          }

          $tabla_ac_lista.='
          <tr>
            <td style="text-align: rigth;width:80%" colspan="5"><b>TOTAL</b></td>
            <td style="text-align: rigth;width:20%"><b>'.number_format($total_partida, 2, ',', '.').'</b></td>
          </tr>
          </tbody>
          </table>';

          $pdf->writeHTML(Helper::htmlComprimir($tabla_ac_lista), true, false, false, false, '');

        }

      }

      // reset font stretching  reset font spacing
      $pdf->setFontStretching(100);
      $pdf->setFontSpacing(0);

      $pdf->AddPage();
      /******Portada Titulo Sectores*********/
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
      $pdf->ln(220);
      $pdf->SetFont('','B',12);
      //$pdf->MultiCell(190, 5, 'TITULO I', 0, 'R', 0, 0, '', '', true);
      $pdf->writeHTML('<b><u>ANEXOS<u/></b>', true, false, true, false, 'R');
      $pdf->ln(0);
      $pdf->writeHTML('<b>RELACIÓN DE OBRAS</b>', true, false, true, false, 'R');
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

      $pdf->AddPage();

      $pdf->SetFont('','B',8);
      $pdf->MultiCell(55, 5, 'GOBERNACIÓN DEL ESTADO ZULIA', 0, 'L', 0, 0, '', '', true);
      $pdf->SetFont('','B',10);
      $pdf->setCellHeightRatio(1);
      $pdf->MultiCell(90, 5, 'RELACIÓN DE OBRAS', 0, 'C', 0, 0, '', '', true);
      $pdf->setCellHeightRatio(2);
      $pdf->ln(8);
      $pdf->SetFont('','B',8);
      $pdf->MultiCell(55, 5, 'PRESUPUESTO '.Session::get("ejercicio"), 0, 'L', 0, 0, '', '', true);
      $pdf->MultiCell(90, 5, '(EN BOLÍVARES)', 0, 'C', 0, 0, '', '', true);
      $pdf->ln(-10);
      $pdf->MultiCell(196, 18, '', 1, 'C', 0, 0, '', '', true);
      $pdf->ln(19);
      $pdf->SetFont('','',8);

      $pdf->MultiCell(196, 230, '', 1, 'C', 0, 0, '', '', true);
      $pdf->ln(0);
      $pdf->SetFont('','B',7);
      $pdf->MultiCell(40, 2, 'CODIGO', 1, 'C', 0, 0, '', '', true);
      $pdf->SetFont('','B',10);
      $pdf->MultiCell(116, 15, 'DENOMINACION', 1, 'C', 0, 0, '', '', true);
      $pdf->MultiCell(40, 15, 'MONTO', 1, 'C', 0, 0, '', '', true);
      $pdf->ln(5);
      $pdf->SetFont('','B',7);
      $pdf->MultiCell(20, 10, 'SECTOR', 1, 'C', 0, 0, '', '', true);
      $pdf->MultiCell(20, 2, 'PROY. Y/O A. CENTRAL', 1, 'C', 0, 0, '', '', true);

      /*$tabla_obra_lista = '
      <table border="0.5" style="width:100%" cellspacing="0" cellpadding="4">
      <thead>
      <tr style="font-size: 8px;" nobr="true">
        <th style="text-align: center;width:20%" colspan="2"><strong>CODIGO</strong></th>
        <th style="text-align: center;width:60%" rowspan="2"><strong><br>DENOMINACION</strong></th>
        <th style="text-align: center;width:20%" rowspan="2"><strong><br>MONTO</strong></th>
      </tr>
      <tr style="font-size: 8px;" nobr="true">
        <th style="text-align: center;width:10%"><strong>SECTOR</strong></th>
        <th style="text-align: center;width:10%"><strong>PROY. Y/O A.CENTRAL</strong></th>
      </tr>
      </thead>
      <tbody>';

      $tabla_obra_lista.='
      <tr nobr="true">
        <td style="text-align: rigth;width:80%" colspan="3"><b>TOTAL GENERAL</b></td>
        <td style="text-align: rigth;width:20%"><b>'.number_format($total_partida, 2, ',', '.').'</b></td>
      </tr>
      </tbody>
      </table>';

      $pdf->writeHTML(Helper::htmlComprimir($tabla_obra_lista), true, false, false, false, '');*/

      //$pdf->AddPage();

      $pdf->AddPage();
      /******Portada Titulo Sectores*********/
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
      $pdf->ln(220);
      $pdf->SetFont('','B',12);
      //$pdf->MultiCell(190, 5, 'TITULO I', 0, 'R', 0, 0, '', '', true);
      $pdf->writeHTML('<b><u>ANEXOS<u/></b>', true, false, true, false, 'R');
      $pdf->ln(0);
      $pdf->writeHTML('<b>FONDO DE COMPENSACION INTERTERRITORIAL (FCI)</b>', true, false, true, false, 'R');
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

      $pdf->AddPage();

      $pdf->SetFont('','B',8);
      $pdf->MultiCell(55, 5, 'GOBERNACIÓN DEL ESTADO ZULIA', 0, 'L', 0, 0, '', '', true);
      $pdf->SetFont('','B',10);
      $pdf->setCellHeightRatio(1);
      $pdf->MultiCell(90, 5, 'RELACIÓN DE PROYECTOS DE INVERSIÓN A SER FINANCIADOS A TRAVÉS DEL FONDO DE COMPENSACION INTERTERRITORIAL', 0, 'C', 0, 0, '', '', true);
      $pdf->setCellHeightRatio(2);
      $pdf->ln(8);
      $pdf->SetFont('','B',8);
      $pdf->MultiCell(55, 5, 'PRESUPUESTO '.Session::get("ejercicio"), 0, 'L', 0, 0, '', '', true);
      $pdf->MultiCell(90, 5, '', 0, 'C', 0, 0, '', '', true);
      $pdf->ln(-10);
      $pdf->MultiCell(196, 18, '', 1, 'C', 0, 0, '', '', true);
      $pdf->ln(19);
      $pdf->SetFont('','',8);

      $pdf->MultiCell(196, 230, '', 1, 'C', 0, 0, '', '', true);
      $pdf->ln(0);
      $pdf->SetFont('','B',7);
      $pdf->MultiCell(40, 2, 'CODIGO', 1, 'C', 0, 0, '', '', true);
      $pdf->SetFont('','B',10);
      $pdf->MultiCell(116, 15, 'DENOMINACION', 1, 'C', 0, 0, '', '', true);
      $pdf->MultiCell(40, 15, 'MONTO', 1, 'C', 0, 0, '', '', true);
      $pdf->ln(5);
      $pdf->SetFont('','B',7);
      $pdf->MultiCell(20, 10, 'SECTOR', 1, 'C', 0, 0, '', '', true);
      $pdf->MultiCell(20, 2, 'PROY. Y/O A. CENTRAL', 1, 'C', 0, 0, '', '', true);


      // reset font stretching  reset font spacing
      $pdf->setFontStretching(100);
      $pdf->setFontSpacing(1);

      $pdf->AddPage();
      /******Portada Titulo Sectores*********/
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
      $pdf->ln(220);
      $pdf->SetFont('','B',12);
      //$pdf->MultiCell(190, 5, 'TITULO I', 0, 'R', 0, 0, '', '', true);
      $pdf->writeHTML('<b><u>ANEXOS<u/></b>', true, false, true, false, 'R');
      $pdf->ln(0);
      $pdf->writeHTML('<b>DISTRIBUCIÓN DE SITUADOS</b>', true, false, true, false, 'R');
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

      $pdf->AddPage();

      $pdf->SetFont('','B',8);
      $pdf->MultiCell(55, 5, 'GOBERNACIÓN DEL ESTADO ZULIA', 0, 'L', 0, 0, '', '', true);
      $pdf->SetFont('','B',10);
      $pdf->setCellHeightRatio(1);
      $pdf->MultiCell(90, 5, 'DISTRIBUCIÓN DE SITUADOS A NIVEL DE MUNICIPIOS', 0, 'C', 0, 0, '', '', true);
      $pdf->setCellHeightRatio(2);
      $pdf->ln(8);
      $pdf->SetFont('','B',8);
      $pdf->MultiCell(55, 5, 'PRESUPUESTO '.Session::get("ejercicio"), 0, 'L', 0, 0, '', '', true);
      $pdf->MultiCell(90, 5, '(EN BOLÍVARES)', 0, 'C', 0, 0, '', '', true);
      $pdf->ln(-10);
      $pdf->MultiCell(196, 18, '', 1, 'C', 0, 0, '', '', true);
      $pdf->ln(19);
      $pdf->SetFont('','',8);

      $pdf->MultiCell(196, 230, '', 1, 'C', 0, 0, '', '', true);
      $pdf->ln(0);
      $pdf->SetFont('','B',7);
      $pdf->MultiCell(40, 2, 'CODIGO', 0, 'C', 0, 0, '', '', true);
      $pdf->SetFont('','B',10);
      $pdf->MultiCell(116, 15, 'DENOMINACION', 1, 'C', 0, 0, '', '', true);
      $pdf->MultiCell(40, 15, 'MONTO', 1, 'C', 0, 0, '', '', true);
      $pdf->ln(5);
      $pdf->SetFont('','B',7);
      $pdf->MultiCell(20, 10, 'SECTOR', 1, 'C', 0, 0, '', '', true);
      $pdf->MultiCell(20, 2, 'PROY. Y/O A. CENTRAL', 1, 'C', 0, 0, '', '', true);


      // reset font stretching  reset font spacing
      $pdf->setFontStretching(100);
      $pdf->setFontSpacing(1);

      $pdf->AddPage();
      /******Portada Titulo Sectores*********/
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
      $pdf->ln(220);
      $pdf->SetFont('','B',12);
      //$pdf->MultiCell(190, 5, 'TITULO I', 0, 'R', 0, 0, '', '', true);
      $pdf->writeHTML('<b><u>ANEXOS<u/></b>', true, false, true, false, 'R');
      $pdf->ln(0);
      $pdf->writeHTML('<b>RELACIÓN DE TRANSFERENCIAS</b>', true, false, true, false, 'R');
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

      $pdf->AddPage();

      $pdf->SetFont('','B',8);
      $pdf->MultiCell(55, 5, 'GOBERNACIÓN DEL ESTADO ZULIA', 0, 'L', 0, 0, '', '', true);
      $pdf->SetFont('','B',10);
      $pdf->setCellHeightRatio(1);
      $pdf->MultiCell(90, 5, 'RELACIÓN DE TRANSFERENCIAS', 0, 'C', 0, 0, '', '', true);
      $pdf->setCellHeightRatio(2);
      $pdf->ln(8);
      $pdf->SetFont('','B',8);
      $pdf->MultiCell(55, 5, 'PRESUPUESTO '.Session::get("ejercicio"), 0, 'L', 0, 0, '', '', true);
      $pdf->MultiCell(90, 5, '(EN BOLÍVARES)', 0, 'C', 0, 0, '', '', true);
      $pdf->ln(-10);
      $pdf->MultiCell(196, 18, '', 1, 'C', 0, 0, '', '', true);
      $pdf->ln(19);
      $pdf->SetFont('','',8);

      $pdf->MultiCell(196, 240, '', 1, 'C', 0, 0, '', '', true);
      $pdf->ln(0);
      $pdf->SetFont('','B',7);
      $pdf->ln(30);
      $pdf->StartTransform();
      $pdf->Rotate(90);
      $pdf->MultiCell(30, 10, 'SECTOR', 1, 'L', 0, 0, '', '', true);
      $pdf->ln(10);
      $pdf->MultiCell(30, 10, 'PROY. Y/O ACCIÓN CENTRALIZADA', 1, 'L', 0, 0, '', '', true);
      $pdf->ln(10);
      $pdf->MultiCell(30, 10, 'PARTIDA', 1, 'L', 0, 0, '', '', true);
      $pdf->ln(10);
      $pdf->MultiCell(30, 10, 'SUB - PARTIDA GENERICA', 1, 'L', 0, 0, '', '', true);
      $pdf->ln(10);
      $pdf->MultiCell(30, 10, 'SUB - PARTIDA ESPECIFICA', 1, 'L', 0, 0, '', '', true);
      $pdf->ln(10);
      $pdf->StopTransform();
      $pdf->ln(-80);
      $pdf->SetFont('','B',8);
      $pdf->setCellHeightRatio(10);
      $pdf->MultiCell(50, 30, '', 0, 'C', 0, 0, '', '', true);
      $pdf->MultiCell(71, 30, 'DENOMINACIÓN', 1, 'C', 0, 0, '', '', true);
      $pdf->MultiCell(25, 30, 'CORRIENTES', 1, 'C', 0, 0, '', '', true);
      $pdf->MultiCell(25, 30, 'CAPITAL', 1, 'C', 0, 0, '', '', true);
      $pdf->MultiCell(25, 30, 'MONTO TOTAL', 1, 'C', 0, 0, '', '', true);
      $pdf->ln(30);
      $pdf->setCellHeightRatio(1);
      $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
      $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
      $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
      $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
      $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
      $pdf->MultiCell(71, 205, '', 1, 'C', 0, 0, '', '', true);
      $pdf->MultiCell(25, 205, '', 1, 'C', 0, 0, '', '', true);
      $pdf->MultiCell(25, 205, '', 1, 'C', 0, 0, '', '', true);
      $pdf->MultiCell(25, 205, '', 1, 'C', 0, 0, '', '', true);
      $pdf->ln(2);
      $pdf->SetFont('','',7);
      $pdf->setCellHeightRatio(0.8);

      /*$ac_transferencia_uno = tab_ac_ae_partida::
      join('public.t46_acciones_centralizadas as t01', 't01.id', '=', 'public.t54_ac_ae_partidas.id_accion_centralizada')
      ->join('mantenimiento.tab_sectores as t02', 't02.id', '=', 't01.id_subsector')
      ->join('mantenimiento.tab_ac_predefinida as t03', 't03.id', '=', 't01.id_accion')
      ->join('mantenimiento.tab_sectores as t04', 't04.tx_codigo', '=', 't02.co_sector')
      ->join('mantenimiento.tab_partidas as t05', 't05.co_partida', '=', DB::raw('left(public.t54_ac_ae_partidas.co_partida, 3)'))
      ->join('mantenimiento.tab_partidas as t06', 't06.co_partida', '=', DB::raw('left(public.t54_ac_ae_partidas.co_partida, 5)'))
      ->join('mantenimiento.tab_partidas as t07', 't07.co_partida', '=', DB::raw('left(public.t54_ac_ae_partidas.co_partida, 7)'))
      ->select( 't02.co_sector', 't04.tx_descripcion', DB::raw('sum(public.t54_ac_ae_partidas.monto) as mo_partida') )
      ->where('t01.id_ejercicio', '=', Session::get('ejercicio'))
      ->where('t05.id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
      ->where('t06.id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
      ->where('t07.id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
      ->where(DB::raw('left(public.t54_ac_ae_partidas.co_partida, 3)'), '=', '407')
      ->groupBy('t02.co_sector')
      ->groupBy('t04.tx_descripcion')
      ->orderBy('t02.co_sector','ASC')
      ->get();*/

      $ac_transferencia_uno = vista_relacion_transferencia::
      select( 'co_sector', 'tx_descripcion', DB::raw('sum(monto) as mo_partida') )
      ->where('ef_uno', '=', Session::get('ejercicio'))
      ->where('ef_dos', '=', Session::get('ejercicio'))
      ->where('ef_tres', '=', Session::get('ejercicio'))
      ->where('ef_cuatro', '=', Session::get('ejercicio'))
      ->groupBy('co_sector')
      ->groupBy('tx_descripcion')
      ->orderBy('co_sector','ASC')
      ->get();

      foreach ($ac_transferencia_uno as $key => $value_transferencia) {

        $pdf->SetFont('','B',7);

        $pdf->MultiCell(10, 5, $value_transferencia->co_sector, 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(71, 5, mb_strtoupper($value_transferencia->tx_descripcion, 'UTF-8'), 0, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(25, 5, number_format($value_transferencia->mo_partida, 2, ',', '.'), 0, 'R', 0, 0, '', '', true);
        $pdf->MultiCell(25, 5, '', 0, 'R', 0, 0, '', '', true);
        $pdf->MultiCell(25, 5, number_format($value_transferencia->mo_partida, 2, ',', '.'), 0, 'R', 0, 0, '', '', true);
        $pdf->ln(5);

        $pdf->SetFont('','',7);

        /*$ac_transferencia_dos = tab_ac_ae_partida::
        join('public.t46_acciones_centralizadas as t01', 't01.id', '=', 'public.t54_ac_ae_partidas.id_accion_centralizada')
        ->join('mantenimiento.tab_sectores as t02', 't02.id', '=', 't01.id_subsector')
        ->join('mantenimiento.tab_ac_predefinida as t03', 't03.id', '=', 't01.id_accion')
        ->join('mantenimiento.tab_sectores as t04', 't04.tx_codigo', '=', 't02.co_sector')
        ->join('mantenimiento.tab_partidas as t05', 't05.co_partida', '=', DB::raw('left(public.t54_ac_ae_partidas.co_partida, 3)'))
        ->join('mantenimiento.tab_partidas as t06', 't06.co_partida', '=', DB::raw('left(public.t54_ac_ae_partidas.co_partida, 5)'))
        ->join('mantenimiento.tab_partidas as t07', 't07.co_partida', '=', DB::raw('left(public.t54_ac_ae_partidas.co_partida, 7)'))
        ->select( 't03.nu_original', 't03.de_nombre', DB::raw('sum(public.t54_ac_ae_partidas.monto) as mo_partida') )
        ->where('t01.id_ejercicio', '=', Session::get('ejercicio'))
        ->where('t05.id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
        ->where('t06.id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
        ->where('t07.id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
        ->where(DB::raw('left(public.t54_ac_ae_partidas.co_partida, 3)'), '=', '407')
        ->where('t02.co_sector', '=', $value_transferencia->co_sector)
        ->groupBy('t03.nu_original')
        ->groupBy('t03.de_nombre')
        ->orderBy('nu_original','ASC')
        ->get();*/

        $ac_transferencia_dos = vista_relacion_transferencia::
        select( 'nu_original', 'de_nombre', DB::raw('sum(monto) as mo_partida') )
        ->where('ef_uno', '=', Session::get('ejercicio'))
        ->where('ef_dos', '=', Session::get('ejercicio'))
        ->where('ef_tres', '=', Session::get('ejercicio'))
        ->where('ef_cuatro', '=', Session::get('ejercicio'))
        ->where('co_sector', '=', $value_transferencia->co_sector)
        ->groupBy('nu_original')
        ->groupBy('de_nombre')
        ->orderBy('nu_original','ASC')
        ->get();

        foreach ($ac_transferencia_dos as $key => $value_transferencia_dos) {

          $pdf->SetFont('','',7);
          $pdf->setCellHeightRatio(1.2);

          $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
          //$pdf->MultiCell(10, 5, $value_transferencia_dos->nu_original, 0, 'C', 0, 0, '', '', true);
          $pdf->MultiCell(10, 5, substr($value_transferencia_dos->nu_original, -2), 0, 'C', 0, 0, '', '', true);
          $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
          $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
          $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
          $pdf->MultiCell(2, 5, '', 0, 'C', 0, 0, '', '', true);
          $pdf->MultiCell(69, 5, mb_strtoupper($value_transferencia_dos->de_nombre, 'UTF-8'), 0, 'L', 0, 0, '', '', true);
          $pdf->MultiCell(25, 5, number_format($value_transferencia_dos->mo_partida, 2, ',', '.'), 0, 'R', 0, 0, '', '', true);
          $pdf->MultiCell(25, 5, '', 0, 'R', 0, 0, '', '', true);
          $pdf->MultiCell(25, 5, number_format($value_transferencia_dos->mo_partida, 2, ',', '.'), 0, 'R', 0, 0, '', '', true);
          //$pdf->ln(5);

          $condicionAcPr = strlen($value_transferencia_dos->de_nombre);
          if ($condicionAcPr >= 30) {
            $pdf->ln(10);
          }else {
            $pdf->ln(5);
          }
          if ($condicionAcPr >= 100) {
            $pdf->ln(5);
          }

          $pdf->SetFont('','',7);
          $pdf->setCellHeightRatio(0.8);

          $start_y = $pdf->GetY();

          $culminado = false;

          if ($start_y >= 245) {

            $pdf->SetFont('','B',8);
            $pdf->setCellHeightRatio(1.5);
            $pdf->SetY(262);
            $pdf->MultiCell(121, 5, 'TOTAL', 1, 'R', 0, 0, '', '', true);
            $pdf->SetFont('','B',7);
            $pdf->MultiCell(25, 5, number_format($movimiento, 2, ',', '.'), 1, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(25, 5, '', 1, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(25, 5, number_format($movimiento, 2, ',', '.'), 1, 'C', 0, 0, '', '', true);

            $pdf->SetFont('','',7);

            // reset font stretching  reset font spacing
            $pdf->setFontStretching(100);
            $pdf->setFontSpacing(0);
            $pdf->SetLineWidth(0.150);
            $pdf->setCellHeightRatio(2);

            $pdf->AddPage();

            $pdf->SetFont('','B',8);
            $pdf->MultiCell(55, 5, 'GOBERNACIÓN DEL ESTADO ZULIA', 0, 'L', 0, 0, '', '', true);
            $pdf->SetFont('','B',10);
            $pdf->setCellHeightRatio(1);
            $pdf->MultiCell(90, 5, 'RELACIÓN DE TRANSFERENCIAS', 0, 'C', 0, 0, '', '', true);
            $pdf->setCellHeightRatio(2);
            $pdf->ln(8);
            $pdf->SetFont('','B',8);
            $pdf->MultiCell(55, 5, 'PRESUPUESTO '.Session::get("ejercicio"), 0, 'L', 0, 0, '', '', true);
            $pdf->MultiCell(90, 5, '(EN BOLÍVARES)', 0, 'C', 0, 0, '', '', true);
            $pdf->ln(-10);
            $pdf->MultiCell(196, 18, '', 1, 'C', 0, 0, '', '', true);
            $pdf->ln(19);
            $pdf->SetFont('','',8);

            $pdf->MultiCell(196, 240, '', 1, 'C', 0, 0, '', '', true);
            $pdf->ln(0);
            $pdf->SetFont('','B',7);
            $pdf->ln(30);
            $pdf->StartTransform();
            $pdf->Rotate(90);
            $pdf->MultiCell(30, 10, 'SECTOR', 1, 'L', 0, 0, '', '', true);
            $pdf->ln(10);
            $pdf->MultiCell(30, 10, 'PROY. Y/O ACCIÓN CENTRALIZADA', 1, 'L', 0, 0, '', '', true);
            $pdf->ln(10);
            $pdf->MultiCell(30, 10, 'PARTIDA', 1, 'L', 0, 0, '', '', true);
            $pdf->ln(10);
            $pdf->MultiCell(30, 10, 'SUB - PARTIDA GENERICA', 1, 'L', 0, 0, '', '', true);
            $pdf->ln(10);
            $pdf->MultiCell(30, 10, 'SUB - PARTIDA ESPECIFICA', 1, 'L', 0, 0, '', '', true);
            $pdf->ln(10);
            $pdf->StopTransform();
            $pdf->ln(-80);
            $pdf->SetFont('','B',8);
            $pdf->setCellHeightRatio(10);
            $pdf->MultiCell(50, 30, '', 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(71, 30, 'DENOMINACIÓN', 1, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(25, 30, 'CORRIENTES', 1, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(25, 30, 'CAPITAL', 1, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(25, 30, 'MONTO TOTAL', 1, 'C', 0, 0, '', '', true);
            $pdf->ln(30);
            $pdf->setCellHeightRatio(1);
            $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(71, 205, '', 1, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(25, 205, '', 1, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(25, 205, '', 1, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(25, 205, '', 1, 'C', 0, 0, '', '', true);
            $pdf->ln(2);
            $pdf->SetFont('','',7);
            $pdf->setCellHeightRatio(0.8);

          }


          $pdf->SetFont('','',7);

          /*$ac_transferencia_tres = tab_ac_ae_partida::
          join('public.t46_acciones_centralizadas as t01', 't01.id', '=', 'public.t54_ac_ae_partidas.id_accion_centralizada')
          ->join('mantenimiento.tab_sectores as t02', 't02.id', '=', 't01.id_subsector')
          ->join('mantenimiento.tab_ac_predefinida as t03', 't03.id', '=', 't01.id_accion')
          ->join('mantenimiento.tab_sectores as t04', 't04.tx_codigo', '=', 't02.co_sector')
          ->join('mantenimiento.tab_partidas as t05', 't05.co_partida', '=', DB::raw('left(public.t54_ac_ae_partidas.co_partida, 3)'))
          ->join('mantenimiento.tab_partidas as t06', 't06.co_partida', '=', DB::raw('left(public.t54_ac_ae_partidas.co_partida, 5)'))
          ->join('mantenimiento.tab_partidas as t07', 't07.co_partida', '=', DB::raw('left(public.t54_ac_ae_partidas.co_partida, 7)'))
          ->select( 't05.co_partida', 't05.tx_nombre', DB::raw('sum(public.t54_ac_ae_partidas.monto) as mo_partida') )
          ->where('t01.id_ejercicio', '=', Session::get('ejercicio'))
          ->where('t05.id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
          ->where('t06.id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
          ->where('t07.id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
          ->where(DB::raw('left(public.t54_ac_ae_partidas.co_partida, 3)'), '=', '407')
          ->where('t02.co_sector', '=', $value_transferencia->co_sector)
          ->where('t03.nu_original', '=', $value_transferencia_dos->nu_original)
          ->groupBy('t05.co_partida')
          ->groupBy('t05.tx_nombre')
          ->orderBy('t05.co_partida','ASC')
          ->get();*/

          $ac_transferencia_tres = vista_relacion_transferencia::
          join('mantenimiento.tab_partidas as t05', 't05.co_partida', '=', DB::raw('left(public.vista_relacion_transferencia.co_partida, 3)'))
          ->select( 't05.co_partida', 'np_uno as tx_nombre', DB::raw('sum(monto) as mo_partida') )
          ->where('ef_uno', '=', Session::get('ejercicio'))
          ->where('ef_dos', '=', Session::get('ejercicio'))
          ->where('ef_tres', '=', Session::get('ejercicio'))
          ->where('ef_cuatro', '=', Session::get('ejercicio'))
          ->where('t05.id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
          ->where('co_sector', '=', $value_transferencia->co_sector)
          ->where('nu_original', '=', $value_transferencia_dos->nu_original)
          ->groupBy('t05.co_partida')
          ->groupBy('np_uno')
          ->orderBy('co_partida','ASC')
          ->get();

          foreach ($ac_transferencia_tres as $key => $value_transferencia_tres) {

            $pdf->SetFont('','',7);

            $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(10, 5, trim($value_transferencia_tres->co_partida), 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(4, 5, '', 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(67, 5, $value_transferencia_tres->tx_nombre, 0, 'L', 0, 0, '', '', true);
            $pdf->MultiCell(25, 5, number_format($value_transferencia_tres->mo_partida, 2, ',', '.'), 0, 'R', 0, 0, '', '', true);
            $pdf->MultiCell(25, 5, '', 0, 'R', 0, 0, '', '', true);
            $pdf->MultiCell(25, 5, number_format($value_transferencia_tres->mo_partida, 2, ',', '.'), 0, 'R', 0, 0, '', '', true);
            $pdf->ln(5);

            $pdf->SetFont('','',7);

            $start_y = $pdf->GetY();

            $culminado = false;

            if ($start_y >= 245) {

              $pdf->SetFont('','B',8);
              $pdf->setCellHeightRatio(1.5);
              $pdf->SetY(262);
              $pdf->MultiCell(121, 5, 'TOTAL', 1, 'R', 0, 0, '', '', true);
              $pdf->SetFont('','B',7);
              $pdf->MultiCell(25, 5, number_format($movimiento, 2, ',', '.'), 1, 'C', 0, 0, '', '', true);
              $pdf->MultiCell(25, 5, '', 1, 'C', 0, 0, '', '', true);
              $pdf->MultiCell(25, 5, number_format($movimiento, 2, ',', '.'), 1, 'C', 0, 0, '', '', true);

              $pdf->SetFont('','',7);

              // reset font stretching  reset font spacing
              $pdf->setFontStretching(100);
              $pdf->setFontSpacing(0);
              $pdf->SetLineWidth(0.150);
              $pdf->setCellHeightRatio(2);

              $pdf->AddPage();

              $pdf->SetFont('','B',8);
              $pdf->MultiCell(55, 5, 'GOBERNACIÓN DEL ESTADO ZULIA', 0, 'L', 0, 0, '', '', true);
              $pdf->SetFont('','B',10);
              $pdf->setCellHeightRatio(1);
              $pdf->MultiCell(90, 5, 'RELACIÓN DE TRANSFERENCIAS', 0, 'C', 0, 0, '', '', true);
              $pdf->setCellHeightRatio(2);
              $pdf->ln(8);
              $pdf->SetFont('','B',8);
              $pdf->MultiCell(55, 5, 'PRESUPUESTO '.Session::get("ejercicio"), 0, 'L', 0, 0, '', '', true);
              $pdf->MultiCell(90, 5, '(EN BOLÍVARES)', 0, 'C', 0, 0, '', '', true);
              $pdf->ln(-10);
              $pdf->MultiCell(196, 18, '', 1, 'C', 0, 0, '', '', true);
              $pdf->ln(19);
              $pdf->SetFont('','',8);

              $pdf->MultiCell(196, 240, '', 1, 'C', 0, 0, '', '', true);
              $pdf->ln(0);
              $pdf->SetFont('','B',7);
              $pdf->ln(30);
              $pdf->StartTransform();
              $pdf->Rotate(90);
              $pdf->MultiCell(30, 10, 'SECTOR', 1, 'L', 0, 0, '', '', true);
              $pdf->ln(10);
              $pdf->MultiCell(30, 10, 'PROY. Y/O ACCIÓN CENTRALIZADA', 1, 'L', 0, 0, '', '', true);
              $pdf->ln(10);
              $pdf->MultiCell(30, 10, 'PARTIDA', 1, 'L', 0, 0, '', '', true);
              $pdf->ln(10);
              $pdf->MultiCell(30, 10, 'SUB - PARTIDA GENERICA', 1, 'L', 0, 0, '', '', true);
              $pdf->ln(10);
              $pdf->MultiCell(30, 10, 'SUB - PARTIDA ESPECIFICA', 1, 'L', 0, 0, '', '', true);
              $pdf->ln(10);
              $pdf->StopTransform();
              $pdf->ln(-80);
              $pdf->SetFont('','B',8);
              $pdf->setCellHeightRatio(10);
              $pdf->MultiCell(50, 30, '', 0, 'C', 0, 0, '', '', true);
              $pdf->MultiCell(71, 30, 'DENOMINACIÓN', 1, 'C', 0, 0, '', '', true);
              $pdf->MultiCell(25, 30, 'CORRIENTES', 1, 'C', 0, 0, '', '', true);
              $pdf->MultiCell(25, 30, 'CAPITAL', 1, 'C', 0, 0, '', '', true);
              $pdf->MultiCell(25, 30, 'MONTO TOTAL', 1, 'C', 0, 0, '', '', true);
              $pdf->ln(30);
              $pdf->setCellHeightRatio(1);
              $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
              $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
              $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
              $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
              $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
              $pdf->MultiCell(71, 205, '', 1, 'C', 0, 0, '', '', true);
              $pdf->MultiCell(25, 205, '', 1, 'C', 0, 0, '', '', true);
              $pdf->MultiCell(25, 205, '', 1, 'C', 0, 0, '', '', true);
              $pdf->MultiCell(25, 205, '', 1, 'C', 0, 0, '', '', true);
              $pdf->ln(2);
              $pdf->SetFont('','',7);
              $pdf->setCellHeightRatio(0.8);

            }

            /*$ac_transferencia_cuatro = tab_ac_ae_partida::
            join('public.t46_acciones_centralizadas as t01', 't01.id', '=', 'public.t54_ac_ae_partidas.id_accion_centralizada')
            ->join('mantenimiento.tab_sectores as t02', 't02.id', '=', 't01.id_subsector')
            ->join('mantenimiento.tab_ac_predefinida as t03', 't03.id', '=', 't01.id_accion')
            ->join('mantenimiento.tab_sectores as t04', 't04.tx_codigo', '=', 't02.co_sector')
            ->join('mantenimiento.tab_partidas as t05', 't05.co_partida', '=', DB::raw('left(public.t54_ac_ae_partidas.co_partida, 3)'))
            ->join('mantenimiento.tab_partidas as t06', 't06.co_partida', '=', DB::raw('left(public.t54_ac_ae_partidas.co_partida, 5)'))
            ->join('mantenimiento.tab_partidas as t07', 't07.co_partida', '=', DB::raw('left(public.t54_ac_ae_partidas.co_partida, 7)'))
            ->select( 't06.co_partida', 't06.tx_nombre', DB::raw('sum(public.t54_ac_ae_partidas.monto) as mo_partida') )
            ->where('t01.id_ejercicio', '=', Session::get('ejercicio'))
            ->where('t05.id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
            ->where('t06.id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
            ->where('t07.id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
            ->where(DB::raw('left(public.t54_ac_ae_partidas.co_partida, 3)'), '=', '407')
            ->where('t02.co_sector', '=', $value_transferencia->co_sector)
            ->where('t03.nu_original', '=', $value_transferencia_dos->nu_original)
            ->where('t05.co_partida', '=', $value_transferencia_tres->co_partida)
            ->groupBy('t06.co_partida')
            ->groupBy('t06.tx_nombre')
            ->orderBy('t06.co_partida','ASC')
            ->get();*/

            $ac_transferencia_cuatro = vista_relacion_transferencia::
            join('mantenimiento.tab_partidas as t05', 't05.co_partida', '=', DB::raw('left(public.vista_relacion_transferencia.co_partida, 3)'))
            ->join('mantenimiento.tab_partidas as t06', 't06.co_partida', '=', DB::raw('left(public.vista_relacion_transferencia.co_partida, 5)'))
            ->select( 't06.co_partida', 'np_dos as tx_nombre', DB::raw('sum(monto) as mo_partida') )
            ->where('ef_uno', '=', Session::get('ejercicio'))
            ->where('ef_dos', '=', Session::get('ejercicio'))
            ->where('ef_tres', '=', Session::get('ejercicio'))
            ->where('ef_cuatro', '=', Session::get('ejercicio'))
            ->where('t05.id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
            ->where('t06.id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
            ->where('co_sector', '=', $value_transferencia->co_sector)
            ->where('nu_original', '=', $value_transferencia_dos->nu_original)
            ->where('t05.co_partida', '=', $value_transferencia_tres->co_partida)
            ->groupBy('t06.co_partida')
            ->groupBy('np_dos')
            ->orderBy('t06.co_partida','ASC')
            ->get();

            foreach ($ac_transferencia_cuatro as $key => $value_transferencia_cuatro) {

              $pdf->SetFont('','',7);
              $pdf->setCellHeightRatio(1);

              $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
              $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
              $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
              $pdf->MultiCell(10, 5, substr(substr(trim($value_transferencia_cuatro->co_partida), 0, 5), 3), 0, 'C', 0, 0, '', '', true);
              $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
              $pdf->MultiCell(6, 5, '', 0, 'C', 0, 0, '', '', true);
              $pdf->MultiCell(65, 5, $value_transferencia_cuatro->tx_nombre, 0, 'L', 0, 0, '', '', true);
              $pdf->MultiCell(25, 5, number_format($value_transferencia_cuatro->mo_partida, 2, ',', '.'), 0, 'R', 0, 0, '', '', true);
              $pdf->MultiCell(25, 5, '', 0, 'R', 0, 0, '', '', true);
              $pdf->MultiCell(25, 5, number_format($value_transferencia_cuatro->mo_partida, 2, ',', '.'), 0, 'R', 0, 0, '', '', true);

              $condicionPartida = strlen($value_transferencia_cuatro->tx_nombre);
              if ($condicionPartida >= 30) {
                $pdf->ln(10);
              }else {
                $pdf->ln(5);
              }

              $pdf->SetFont('','',7);
              $pdf->setCellHeightRatio(0.8);

              /*$ac_transferencia_cinco = tab_ac_ae_partida::
              join('public.t46_acciones_centralizadas as t01', 't01.id', '=', 'public.t54_ac_ae_partidas.id_accion_centralizada')
              ->join('mantenimiento.tab_sectores as t02', 't02.id', '=', 't01.id_subsector')
              ->join('mantenimiento.tab_ac_predefinida as t03', 't03.id', '=', 't01.id_accion')
              ->join('mantenimiento.tab_sectores as t04', 't04.tx_codigo', '=', 't02.co_sector')
              ->join('mantenimiento.tab_partidas as t05', 't05.co_partida', '=', DB::raw('left(public.t54_ac_ae_partidas.co_partida, 3)'))
              ->join('mantenimiento.tab_partidas as t06', 't06.co_partida', '=', DB::raw('left(public.t54_ac_ae_partidas.co_partida, 5)'))
              ->join('mantenimiento.tab_partidas as t07', 't07.co_partida', '=', DB::raw('left(public.t54_ac_ae_partidas.co_partida, 7)'))
              ->select( 't07.co_partida', 't07.tx_nombre', DB::raw('sum(public.t54_ac_ae_partidas.monto) as mo_partida') )
              ->where('t01.id_ejercicio', '=', Session::get('ejercicio'))
              ->where('t05.id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
              ->where('t06.id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
              ->where('t07.id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
              ->where(DB::raw('left(public.t54_ac_ae_partidas.co_partida, 3)'), '=', '407')
              ->where('t02.co_sector', '=', $value_transferencia->co_sector)
              ->where('t03.nu_original', '=', $value_transferencia_dos->nu_original)
              ->where('t05.co_partida', '=', $value_transferencia_tres->co_partida)
              ->where('t06.co_partida', '=', $value_transferencia_cuatro->co_partida)
              ->groupBy('t07.co_partida')
              ->groupBy('t07.tx_nombre')
              ->orderBy('t07.co_partida','ASC')
              ->get();*/

              $ac_transferencia_cinco = vista_relacion_transferencia::
              join('mantenimiento.tab_partidas as t05', 't05.co_partida', '=', DB::raw('left(public.vista_relacion_transferencia.co_partida, 3)'))
              ->join('mantenimiento.tab_partidas as t06', 't06.co_partida', '=', DB::raw('left(public.vista_relacion_transferencia.co_partida, 5)'))
              ->join('mantenimiento.tab_partidas as t07', 't07.co_partida', '=', DB::raw('left(public.vista_relacion_transferencia.co_partida, 7)'))
              ->select( 't07.co_partida', 'np_tres as tx_nombre', DB::raw('sum(monto) as mo_partida') )
              ->where('ef_uno', '=', Session::get('ejercicio'))
              ->where('ef_dos', '=', Session::get('ejercicio'))
              ->where('ef_tres', '=', Session::get('ejercicio'))
              ->where('ef_cuatro', '=', Session::get('ejercicio'))
              ->where('t05.id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
              ->where('t06.id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
              ->where('t07.id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
              ->where('co_sector', '=', $value_transferencia->co_sector)
              ->where('nu_original', '=', $value_transferencia_dos->nu_original)
              ->where('t05.co_partida', '=', $value_transferencia_tres->co_partida)
              ->where('t06.co_partida', '=', $value_transferencia_cuatro->co_partida)
              ->groupBy('t07.co_partida')
              ->groupBy('np_tres')
              ->orderBy('t07.co_partida','ASC')
              ->get();

              foreach ($ac_transferencia_cinco as $key => $value_transferencia_cinco) {

                $pdf->SetFont('','',7);
                $pdf->setCellHeightRatio(1);

                $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(10, 5, substr(substr(trim($value_transferencia_cinco->co_partida), 0, 7), 5), 0, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(6, 5, '', 0, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(65, 5, $value_transferencia_cinco->tx_nombre, 0, 'L', 0, 0, '', '', true);
                $pdf->MultiCell(25, 5, number_format($value_transferencia_cinco->mo_partida, 2, ',', '.'), 0, 'R', 0, 0, '', '', true);
                $pdf->MultiCell(25, 5, '', 0, 'R', 0, 0, '', '', true);
                $pdf->MultiCell(25, 5, number_format($value_transferencia_cinco->mo_partida, 2, ',', '.'), 0, 'R', 0, 0, '', '', true);

                $condicionPartida = strlen($value_transferencia_cinco->tx_nombre);
                if ($condicionPartida >= 30) {
                  $pdf->ln(10);
                }else {
                  $pdf->ln(5);
                }

                $movimiento = $movimiento + $value_transferencia_cinco->mo_partida;

                $pdf->SetFont('','',7);
                $pdf->setCellHeightRatio(0.8);

                $start_y = $pdf->GetY();

                $culminado = false;

                if ($start_y >= 245) {

                  $pdf->SetFont('','B',8);
                  $pdf->setCellHeightRatio(1.5);
                  $pdf->SetY(262);
                  $pdf->MultiCell(121, 5, 'TOTAL', 1, 'R', 0, 0, '', '', true);
                  $pdf->SetFont('','B',7);
                  $pdf->MultiCell(25, 5, number_format($movimiento, 2, ',', '.'), 1, 'C', 0, 0, '', '', true);
                  $pdf->MultiCell(25, 5, '', 1, 'C', 0, 0, '', '', true);
                  $pdf->MultiCell(25, 5, number_format($movimiento, 2, ',', '.'), 1, 'C', 0, 0, '', '', true);

                  $pdf->SetFont('','',7);

                  // reset font stretching  reset font spacing
                  $pdf->setFontStretching(100);
                  $pdf->setFontSpacing(0);
                  $pdf->SetLineWidth(0.150);
                  $pdf->setCellHeightRatio(2);

                  $pdf->AddPage();

                  $pdf->SetFont('','B',8);
                  $pdf->MultiCell(55, 5, 'GOBERNACIÓN DEL ESTADO ZULIA', 0, 'L', 0, 0, '', '', true);
                  $pdf->SetFont('','B',10);
                  $pdf->setCellHeightRatio(1);
                  $pdf->MultiCell(90, 5, 'RELACIÓN DE TRANSFERENCIAS', 0, 'C', 0, 0, '', '', true);
                  $pdf->setCellHeightRatio(2);
                  $pdf->ln(8);
                  $pdf->SetFont('','B',8);
                  $pdf->MultiCell(55, 5, 'PRESUPUESTO '.Session::get("ejercicio"), 0, 'L', 0, 0, '', '', true);
                  $pdf->MultiCell(90, 5, '(EN BOLÍVARES)', 0, 'C', 0, 0, '', '', true);
                  $pdf->ln(-10);
                  $pdf->MultiCell(196, 18, '', 1, 'C', 0, 0, '', '', true);
                  $pdf->ln(19);
                  $pdf->SetFont('','',8);

                  $pdf->MultiCell(196, 240, '', 1, 'C', 0, 0, '', '', true);
                  $pdf->ln(0);
                  $pdf->SetFont('','B',7);
                  $pdf->ln(30);
                  $pdf->StartTransform();
                  $pdf->Rotate(90);
                  $pdf->MultiCell(30, 10, 'SECTOR', 1, 'L', 0, 0, '', '', true);
                  $pdf->ln(10);
                  $pdf->MultiCell(30, 10, 'PROY. Y/O ACCIÓN CENTRALIZADA', 1, 'L', 0, 0, '', '', true);
                  $pdf->ln(10);
                  $pdf->MultiCell(30, 10, 'PARTIDA', 1, 'L', 0, 0, '', '', true);
                  $pdf->ln(10);
                  $pdf->MultiCell(30, 10, 'SUB - PARTIDA GENERICA', 1, 'L', 0, 0, '', '', true);
                  $pdf->ln(10);
                  $pdf->MultiCell(30, 10, 'SUB - PARTIDA ESPECIFICA', 1, 'L', 0, 0, '', '', true);
                  $pdf->ln(10);
                  $pdf->StopTransform();
                  $pdf->ln(-80);
                  $pdf->SetFont('','B',8);
                  $pdf->setCellHeightRatio(10);
                  $pdf->MultiCell(50, 30, '', 0, 'C', 0, 0, '', '', true);
                  $pdf->MultiCell(71, 30, 'DENOMINACIÓN', 1, 'C', 0, 0, '', '', true);
                  $pdf->MultiCell(25, 30, 'CORRIENTES', 1, 'C', 0, 0, '', '', true);
                  $pdf->MultiCell(25, 30, 'CAPITAL', 1, 'C', 0, 0, '', '', true);
                  $pdf->MultiCell(25, 30, 'MONTO TOTAL', 1, 'C', 0, 0, '', '', true);
                  $pdf->ln(30);
                  $pdf->setCellHeightRatio(1);
                  $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
                  $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
                  $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
                  $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
                  $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
                  $pdf->MultiCell(71, 205, '', 1, 'C', 0, 0, '', '', true);
                  $pdf->MultiCell(25, 205, '', 1, 'C', 0, 0, '', '', true);
                  $pdf->MultiCell(25, 205, '', 1, 'C', 0, 0, '', '', true);
                  $pdf->MultiCell(25, 205, '', 1, 'C', 0, 0, '', '', true);
                  $pdf->ln(2);
                  $pdf->SetFont('','',7);
                  $pdf->setCellHeightRatio(0.8);

                }

              }

            }

          }

        }

      }

      $culminado = true;

      if($culminado ==true){
        $pdf->SetFont('','B',8);
        $pdf->setCellHeightRatio(1.5);
        $pdf->SetY(262);
        $pdf->MultiCell(121, 5, 'TOTAL', 1, 'R', 0, 0, '', '', true);
        $pdf->SetFont('','B',7);
        $pdf->MultiCell(25, 5, number_format($movimiento, 2, ',', '.'), 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(25, 5, '', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(25, 5, number_format($movimiento, 2, ',', '.'), 1, 'C', 0, 0, '', '', true);
      }

      //Cierre de Reporte
      $pdf->lastPage();
      $pdf->output('LEY_DE_PRESUPUESTO_'.Session::get("ejercicio").'_'.date("H:i:s").'.pdf', 'D');
    }
}
