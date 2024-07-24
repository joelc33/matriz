<?php

namespace matriz\Http\Controllers\Reporte;

//*******agregar esta linea******//
use matriz\Models\AcSegto\tab_meta_fisica;
use matriz\Models\AcSegto\tab_meta_financiera;
use matriz\Models\AcSegto\tab_forma_005;
use matriz\Models\AcSegto\tab_ac;
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
class PDFseguimientoAC extends TCPDF
{
    public function encabezado($pdf)
    {

        $pdf->Image(public_path().'/images/zulia_escudo.png', 10, 10, 20, 18, 'PNG', '', '', true, 150, '', false, false, 0, false, false, false);
        $pdf->setXY(30, 15);
//        $pdf->SetFont('', 'B', 11);
//        $pdf->MultiCell(190, 5, 'GOBERNACIÓN DEL ESTADO ZULIA', 0, 'L', 0, 0, '', '', true);
//        $pdf->setXY(30, 20);
//        $pdf->MultiCell(190, 5, 'PLAN OPERATIVO ANUAL '.Session::get("ejercicio"), 0, 'L', 0, 0, '', '', true);
        $pdf->setY(30);
        $pdf->MultiCell(277, 5, 'SISTEMA DE SEGUIMIENTO, EVALUACIÓN Y CONTROL DEL PLAN OPERATIVO ESTADAL', 0, 'C', 0, 0, '', '', true);
        $pdf->Ln(5);        
        $pdf->MultiCell(277, 5, 'REPORTE DE LA EJECUCIÓN DEL PRESUPUESTO DE LOS EGRESOS POR SECTORES Y PARTIDAS', 0, 'C', 0, 0, '', '', true);
        $pdf->Ln(5);
        $pdf->MultiCell(277, 5, 'EJECUCIÓN PRESUPUESTARIA', 0, 'C', 0, 0, '', '', true);
        $pdf->Ln(5);
//        $pdf->MultiCell(275, 5, Session::get("periodo"), 0, 'R', 0, 0, '', '', true);
        $pdf->Ln(5);        

        return $pdf;
    }

    public function pie($pdf)
    {
        $pdf->setXY(10, -10);
        $pdf->SetFont('', '', 9);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->writeHTMLCell(250, 0, '', '', 'Palacio de los Cóndores, Plaza Bolívar, Maracaibo, Estado Zulia, Venezuela', 0, 0, 0, true, 'C', true);
        $pdf->SetFont('', '', 7);
//        $pdf->writeHTMLCell(15, 0, '', '', $pdf->getAliasNumPage().'/'.$pdf->getAliasNbPages(), 0, 0, 0, true, 'C', true);

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

class acseguimientoejecucionController extends Controller
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
	function formatoDinero($numero, $fractional=true){
	    if ($fractional) {
		$numero = sprintf('%.2f', $numero);
	    }
	    while (true) {
		$replaced = preg_replace('/(-?\d+)(\d\d\d)/', '$1,$2', $numero);
		if ($replaced != $numero) {
		    $numero = $replaced;
		} else {
		    break;
		}
	    }
	    return "Bs. ".$numero;
	}
      /**
       * Display a listing of the resource.
       *
       * @return \Illuminate\Http\Response
       */      
      
      public function fichaEjecucion($id_tab_lapso,$id=null)
      {

      
            if($id!=null){
                
            $data =  tab_meta_financiera::select(
                'tx_nombre',
                DB::raw('sum(coalesce(mo_presupuesto,0)) as mo_presupuesto'),
                DB::raw('sum(coalesce(mo_modificado_anual,0)) as mo_modificado_anual'),
                DB::raw('sum(coalesce(mo_presupuesto,0)) + sum(coalesce(mo_modificado_anual,0)) as mo_actualizado_anual'),
                DB::raw('sum(coalesce(mo_comprometido,0)) as mo_comprometido'),
                DB::raw('sum(coalesce(mo_causado,0)) as mo_causado'),
                DB::raw('sum(coalesce(mo_pagado,0)) as mo_pagado'),
                DB::raw('(sum(coalesce(mo_presupuesto,0)) + sum(coalesce(mo_modificado_anual,0))) -  sum(coalesce(mo_pagado,0)) as mo_financiera'),
                DB::raw('(sum(coalesce(mo_presupuesto,0)) + sum(coalesce(mo_modificado_anual,0))) -  sum(coalesce(mo_comprometido,0)) as mo_presupuestaria'),                    
                'ac_seguimiento.tab_meta_financiera.co_partida',
                't03.id_ejecutor',
                'tx_ejecutor',
                't18b.tx_codigo as tx_sector',
                'de_fuente_financiamiento',
                'de_lapso',
                't03.id_tab_ejercicio_fiscal'
            )
            ->join('ac_seguimiento.tab_meta_fisica as t01', 'ac_seguimiento.tab_meta_financiera.id_tab_meta_fisica', '=', 't01.id')
            ->join('ac_seguimiento.tab_ac_ae as t02', 't01.id_tab_ac_ae', '=', 't02.id')
            ->join('ac_seguimiento.tab_ac as t03', 't02.id_tab_ac', '=', 't03.id')
            ->join('mantenimiento.tab_ejecutores as t05', 't05.id_ejecutor', '=', 't03.id_ejecutor')
            ->join('mantenimiento.tab_fuente_financiamiento as t06', 'ac_seguimiento.tab_meta_financiera.id_tab_fuente_financiamiento', '=', 't06.id')  
            ->join('mantenimiento.tab_lapso as t07', 't03.id_tab_lapso', '=', 't07.id')        
            ->join('mantenimiento.tab_partidas as t04', function ($j) {
                $j->on('t04.co_partida', '=', 'ac_seguimiento.tab_meta_financiera.co_partida')
                  ->on('t04.id_tab_ejercicio_fiscal', '=', 't03.id_tab_ejercicio_fiscal');
            })
            ->join('mantenimiento.tab_sectores as t18a', 't03.id_tab_sectores', '=', 't18a.id')
            ->join('mantenimiento.tab_sectores as t18b', function ($join) {
            $join->on('t18a.co_sector', '=', 't18b.co_sector')
            ->on('t18b.nu_nivel', '=', DB::raw('1'));
            })             
            ->where('t03.id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
            ->where('ac_seguimiento.tab_meta_financiera.in_activo', '=', true) 
            ->where('t03.id_ejecutor', '=', $id)
            ->where('t03.id_tab_lapso', '=', $id_tab_lapso)
            ->groupBy('ac_seguimiento.tab_meta_financiera.co_partida')
            ->groupBy('t03.id_ejecutor')
            ->groupBy('tx_ejecutor')
            ->groupBy('tx_sector')
            ->groupBy('tx_nombre')
            ->groupBy('de_lapso')
            ->groupBy('de_fuente_financiamiento')
            ->groupBy('t03.id_tab_ejercicio_fiscal')
            ->orderby('ac_seguimiento.tab_meta_financiera.t18b.tx_codigo', 'ASC')->orderby('ac_seguimiento.tab_meta_financiera.co_partida', 'ASC')->get();                 
            
            }else{
            $data =  tab_meta_financiera::select(
                    'tx_nombre',
                DB::raw('sum(coalesce(mo_presupuesto,0)) as mo_presupuesto'),
                DB::raw('sum(coalesce(mo_modificado_anual,0)) as mo_modificado_anual'),
                DB::raw('sum(coalesce(mo_presupuesto,0)) + sum(coalesce(mo_modificado_anual,0)) as mo_actualizado_anual'),
                DB::raw('sum(coalesce(mo_comprometido,0)) as mo_comprometido'),
                DB::raw('sum(coalesce(mo_causado,0)) as mo_causado'),
                DB::raw('sum(coalesce(mo_pagado,0)) as mo_pagado'),
                DB::raw('(sum(coalesce(mo_presupuesto,0)) + sum(coalesce(mo_modificado_anual,0))) -  sum(coalesce(mo_pagado,0)) as mo_financiera'),
                DB::raw('(sum(coalesce(mo_presupuesto,0)) + sum(coalesce(mo_modificado_anual,0))) -  sum(coalesce(mo_comprometido,0)) as mo_presupuestaria'),                    
                'ac_seguimiento.tab_meta_financiera.co_partida',
                    'de_fuente_financiamiento',
                    't18b.tx_codigo as tx_sector',
                    'de_lapso',
                    't03.id_tab_ejercicio_fiscal'
            )
            ->join('ac_seguimiento.tab_meta_fisica as t01', 'ac_seguimiento.tab_meta_financiera.id_tab_meta_fisica', '=', 't01.id')
            ->join('ac_seguimiento.tab_ac_ae as t02', 't01.id_tab_ac_ae', '=', 't02.id')
            ->join('ac_seguimiento.tab_ac as t03', 't02.id_tab_ac', '=', 't03.id')
            ->join('mantenimiento.tab_fuente_financiamiento as t06', 'ac_seguimiento.tab_meta_financiera.id_tab_fuente_financiamiento', '=', 't06.id')
            ->join('mantenimiento.tab_lapso as t07', 't03.id_tab_lapso', '=', 't07.id')
            ->join('mantenimiento.tab_partidas as t04', function ($j) {
                $j->on('t04.co_partida', '=', 'ac_seguimiento.tab_meta_financiera.co_partida')
                  ->on('t04.id_tab_ejercicio_fiscal', '=', 't03.id_tab_ejercicio_fiscal');
            })
            ->join('mantenimiento.tab_sectores as t18a', 't03.id_tab_sectores', '=', 't18a.id')
            ->join('mantenimiento.tab_sectores as t18b', function ($join) {
            $join->on('t18a.co_sector', '=', 't18b.co_sector')
            ->on('t18b.nu_nivel', '=', DB::raw('1'));
            })      
            ->where('t03.id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
            ->where('ac_seguimiento.tab_meta_financiera.in_activo', '=', true)
            ->where('t03.id_tab_lapso', '=', $id_tab_lapso)
            ->groupBy('ac_seguimiento.tab_meta_financiera.co_partida')
            ->groupBy('tx_sector')
            ->groupBy('de_lapso')
            ->groupBy('tx_nombre')
            ->groupBy('t03.id_tab_ejercicio_fiscal')
            ->groupBy('de_fuente_financiamiento')
            ->orderby('ac_seguimiento.tab_meta_financiera.t18b.tx_codigo', 'ASC')->orderby('ac_seguimiento.tab_meta_financiera.co_partida', 'ASC')->get();                 
            }   
//            $data->orderby('ac_seguimiento.tab_meta_financiera.co_partida', 'ASC')->get();
//        $periodo = 'LAPSO '.$data->fe_inicio.' - '.$data->fe_fin;    
//            
//          Session::put('periodo',$periodo);             

          $pdf = new PDFseguimientoAC("L", PDF_UNIT, 'Letter', true, 'UTF-8', false);
          $pdf->SetCreator('Sistema POA, Yoser Perez');
          $pdf->SetAuthor('Yoser Perez');
          $pdf->SetTitle('Seguimiento AC');
          $pdf->SetSubject('Seguimiento AC');
          $pdf->SetKeywords('Seguimiento AC, PDF, Zulia, SPE, '.Session::get("ejercicio").'');
          $pdf->SetMargins(10,10,10);
          $pdf->SetTopMargin(50);
          $pdf->SetPrintHeader(true);
          $pdf->SetPrintFooter(true);
          // set auto page breaks
          $pdf->SetAutoPageBreak(true, 10);
//          $pdf->AddPage();

          $pdf->SetFont('','',11);

       $tx_sector = '';   
          $pdf->Ln(-3);
          
//          var_dump($data);
//          exit();

                $mo_presupuesto = 0;
                $mo_modificado_anual = 0;
                $mo_actualizado_anual = 0;
                $mo_comprometido = 0;
                $mo_causado = 0;
                $mo_pagado = 0;          
          
foreach($data as $item) {
    
         
                $de_lapso = $item->de_lapso;
                $id_tab_ejercicio_fiscal = $item->id_tab_ejercicio_fiscal;
         
         if($tx_sector!=$item->tx_sector){
             
         
          
if($tx_sector!=''){
    
      


		$html23.='
		<tr style="font-size:7px" >
                <td style="width: 30%;" colspan="2" align="right"> TOTAL EJECUTADO AL '.$de_lapso.' '.$id_tab_ejercicio_fiscal.' </td>
                <td style="width: 10%;" >'.$this->formatoDinero($mo_presupuesto).'</td>
                <td style="width: 10%;" >'.$this->formatoDinero($mo_modificado_anual).'</td>
                <td style="width: 10%;" >'.$this->formatoDinero($mo_actualizado_anual).'</td>
                <td style="width: 10%;" >'.$this->formatoDinero($mo_comprometido).'</td>
                <td style="width: 10%;" >'.$this->formatoDinero($mo_causado).'</td>
                <td style="width: 10%;" >'.$this->formatoDinero($mo_pagado).'</td>';
                $html23.='</tr>';

   $html23.='
</tbody>
</table>';  
   
   $html1 = '
<table border="0.1" style="font-size:9px" cellpadding="3" >
<tbody>
<tr style="font-size:8px">
<td style="width: 35%;" align="center"><b>PLANIFICACIÓN Y CONTROL DE GESTIÓN</b></td>
<td style="width: 35%;" align="center"><b>ADMINISTRACIÓN Y FINANZAS / PRESUPUESTO</b></td>
<td style="width: 30%;" align="center"><b>TITULAR</b></td>
</tr>
<tr style="font-size:8px">
<td style="width: 35%;" align="center"><b>FIRMA Y SELLO</b></td>
<td style="width: 35%;" align="center"><b>FIRMA Y SELLO</b></td>
<td style="width: 30%;" align="center"><b>FIRMA Y SELLO</b></td>
</tr>
<tr style="font-size:8px">
<td style="width: 35%; height: 40px;" align="center"><b></b></td>
<td style="width: 35%; height: 40px;" align="center"><b></b></td>
<td style="width: 30%; height: 40px;" align="center"><b></b></td>
</tr>
<tr style="font-size:8px">
<td style="width: 35%;" align="center"><b>NOMBRE Y APELLIDO</b></td>
<td style="width: 35%;" align="center"><b>NOMBRE Y APELLIDO</b></td>
<td style="width: 30%;" align="center"><b>NOMBRE Y APELLIDO</b></td>
</tr>
<tr style="font-size:8px">
<td style="width: 35%; height: 40px;" align="center"><b></b></td>
<td style="width: 35%; height: 40px;" align="center"><b></b></td>
<td style="width: 30%; height: 40px;" align="center"><b></b></td>
</tr>
<tr style="font-size:8px">
<td style="width: 35%;" align="center"><b>C.I. </b></td>
<td style="width: 35%;" align="center"><b>C.I. </b></td>
<td style="width: 30%;" align="center"><b>C.I. </b></td>
</tr>
</tbody>
</table>
';
   
          $pdf->writeHTML(Helper::htmlComprimir($html23), true, false, false, false, '');
          $pdf->SetX(140);
          $pdf->writeHTML(Helper::htmlComprimir($html1), true, false, false, false, '');   
    
}         
         
             
$pdf->AddPage();

         if($item->tx_sector){
          $tx_sector = $item->tx_sector;   
          $ejecutor = $item->id_ejecutor.' - '.$item->tx_ejecutor;
         }else{
          $tx_sector = 'TODOS'; 
          $ejecutor = 'TODOS';
         }    

$html23='';
$html23.= '
<table border="0.1" style="width:100%" style="font-size:9px" cellpadding="3">
<thead>
<tr style="font-size:9px">
<td style="width: 100%;"><b>'.$ejecutor.'</b> </td>
</tr>
<tr style="font-size:7px">
<td  style="width: 30%;" ><b>SECTOR:</b> '.$tx_sector.'</td>
<td rowspan="3" style="width: 10%;" align="center"><b>PRESUPUESTO INICIAL</b></td>
<td rowspan="3" style="width: 10%;" align="center"><b>PRESUPUESTO MODIFICADO</b></td>
<td rowspan="3" style="width: 10%;" align="center"><b>PRESUPUESTO ACTUALIZADO (TOTAL)</b></td>
<td rowspan="3" style="width: 10%;" align="center"><b>COMPROMETIDO</b></td>
<td rowspan="3" style="width: 10%;" align="center"><b>CAUSADO</b></td>
<td rowspan="3" style="width: 10%;" align="center"><b>PAGADO</b></td>
<td rowspan="3" style="width: 10%;" align="center"><b>FUENTE DE FINANCIAMIENTO</b></td>
</tr>
<tr style="font-size:7px">
<td style="width: 30%;" align="center" colspan="2"><b>PARTIDA PRESUPUESTARIA</b></td>
</tr>
<tr style="font-size:7px">
<td style="width: 10%;" align="center"><b>CÓDIGO</b></td>
<td style="width: 20%;" align="center"><b>DENOMINACIÓN</b></td>
</tr>
</thead>
';  
$html23.='
<tbody>';  

		$html23.='
		<tr style="font-size:7px" >
		<td style="width: 10%;" align="center">'.$item->co_partida.'</td>
                <td style="width: 20%;" >'.$item->tx_nombre.'</td>
                <td style="width: 10%;" >'.$this->formatoDinero($item->mo_presupuesto).'</td>
                <td style="width: 10%;" >'.$this->formatoDinero($item->mo_modificado_anual).'</td>
                <td style="width: 10%;" >'.$this->formatoDinero($item->mo_actualizado_anual).'</td>
                <td style="width: 10%;" >'.$this->formatoDinero($item->mo_comprometido).'</td>
                <td style="width: 10%;" >'.$this->formatoDinero($item->mo_causado).'</td>
                <td style="width: 10%;" >'.$this->formatoDinero($item->mo_pagado).'</td>                                 
                <td style="width: 10%;" >'.$item->de_fuente_financiamiento.'</td>';
                $html23.='</tr>';

                $mo_presupuesto = $mo_presupuesto + $item->mo_presupuesto;
                $mo_modificado_anual = $mo_modificado_anual + $item->mo_modificado_anual;
                $mo_actualizado_anual = $mo_actualizado_anual + $item->mo_actualizado_anual;
                $mo_comprometido = $mo_comprometido + $item->mo_comprometido;
                $mo_causado = $mo_causado + $item->mo_causado;
                $mo_pagado = $mo_pagado + $item->mo_pagado;
                $de_lapso = $item->de_lapso;
                $id_tab_ejercicio_fiscal = $item->id_tab_ejercicio_fiscal;

            $tx_sector = $item->tx_sector;


         }else{
             
 		$html23.='
		<tr style="font-size:7px" >
		<td style="width: 10%;" align="center">'.$item->co_partida.'</td>
                <td style="width: 20%;" >'.$item->tx_nombre.'</td>
                <td style="width: 10%;" >'.$this->formatoDinero($item->mo_presupuesto).'</td>
                <td style="width: 10%;" >'.$this->formatoDinero($item->mo_modificado_anual).'</td>
                <td style="width: 10%;" >'.$this->formatoDinero($item->mo_actualizado_anual).'</td>
                <td style="width: 10%;" >'.$this->formatoDinero($item->mo_comprometido).'</td>
                <td style="width: 10%;" >'.$this->formatoDinero($item->mo_causado).'</td>
                <td style="width: 10%;" >'.$this->formatoDinero($item->mo_pagado).'</td>                                 
                <td style="width: 10%;" >'.$item->de_fuente_financiamiento.'</td>';
                $html23.='</tr>';

                $mo_presupuesto = $mo_presupuesto + $item->mo_presupuesto;
                $mo_modificado_anual = $mo_modificado_anual + $item->mo_modificado_anual;
                $mo_actualizado_anual = $mo_actualizado_anual + $item->mo_actualizado_anual;
                $mo_comprometido = $mo_comprometido + $item->mo_comprometido;
                $mo_causado = $mo_causado + $item->mo_causado;
                $mo_pagado = $mo_pagado + $item->mo_pagado;
                $de_lapso = $item->de_lapso;
                $id_tab_ejercicio_fiscal = $item->id_tab_ejercicio_fiscal;            
         }


     }
                  


		$html23.='
		<tr style="font-size:7px" >
                <td style="width: 30%;" colspan="2" align="right"> TOTAL EJECUTADO AL '.$de_lapso.' '.$id_tab_ejercicio_fiscal.' </td>
                <td style="width: 10%;" >'.$this->formatoDinero($mo_presupuesto).'</td>
                <td style="width: 10%;" >'.$this->formatoDinero($mo_modificado_anual).'</td>
                <td style="width: 10%;" >'.$this->formatoDinero($mo_actualizado_anual).'</td>
                <td style="width: 10%;" >'.$this->formatoDinero($mo_comprometido).'</td>
                <td style="width: 10%;" >'.$this->formatoDinero($mo_causado).'</td>
                <td style="width: 10%;" >'.$this->formatoDinero($mo_pagado).'</td>';
                $html23.='</tr>';

   $html23.='
</tbody>
</table>';  
   
   $html1 = '
<table border="0.1" style="font-size:9px" cellpadding="3" >
<tbody>
<tr style="font-size:8px">
<td style="width: 35%;" align="center"><b>PLANIFICACIÓN Y CONTROL DE GESTIÓN</b></td>
<td style="width: 35%;" align="center"><b>ADMINISTRACIÓN Y FINANZAS / PRESUPUESTO</b></td>
<td style="width: 30%;" align="center"><b>TITULAR</b></td>
</tr>
<tr style="font-size:8px">
<td style="width: 35%;" align="center"><b>FIRMA Y SELLO</b></td>
<td style="width: 35%;" align="center"><b>FIRMA Y SELLO</b></td>
<td style="width: 30%;" align="center"><b>FIRMA Y SELLO</b></td>
</tr>
<tr style="font-size:8px">
<td style="width: 35%; height: 40px;" align="center"><b></b></td>
<td style="width: 35%; height: 40px;" align="center"><b></b></td>
<td style="width: 30%; height: 40px;" align="center"><b></b></td>
</tr>
<tr style="font-size:8px">
<td style="width: 35%;" align="center"><b>NOMBRE Y APELLIDO</b></td>
<td style="width: 35%;" align="center"><b>NOMBRE Y APELLIDO</b></td>
<td style="width: 30%;" align="center"><b>NOMBRE Y APELLIDO</b></td>
</tr>
<tr style="font-size:8px">
<td style="width: 35%; height: 40px;" align="center"><b></b></td>
<td style="width: 35%; height: 40px;" align="center"><b></b></td>
<td style="width: 30%; height: 40px;" align="center"><b></b></td>
</tr>
<tr style="font-size:8px">
<td style="width: 35%;" align="center"><b>C.I. </b></td>
<td style="width: 35%;" align="center"><b>C.I. </b></td>
<td style="width: 30%;" align="center"><b>C.I. </b></td>
</tr>
</tbody>
</table>
';
   
          $pdf->writeHTML(Helper::htmlComprimir($html23), true, false, false, false, '');
          $pdf->SetX(140);
          $pdf->writeHTML(Helper::htmlComprimir($html1), true, false, false, false, ''); 
          $pdf->output('SEGUIMIENTO_AC_'.Session::get("ejercicio").'_'.date("H:i:s").'.pdf', 'D');
      }      

}
