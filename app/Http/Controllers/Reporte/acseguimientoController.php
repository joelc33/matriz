<?php

namespace matriz\Http\Controllers\Reporte;

//*******agregar esta linea******//
use matriz\Models\AcSegto\tab_meta_financiera;
use matriz\Models\AcSegto\tab_forma_001;
use matriz\Models\AcSegto\tab_ac;
use matriz\Models\Ac\tab_meta_fisica;
use matriz\Models\Mantenimiento\tab_lapso;
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
        $pdf->SetFont('', 'B', 11);
        $pdf->MultiCell(190, 5, 'GOBERNACIÓN DEL ESTADO ZULIA', 0, 'L', 0, 0, '', '', true);
        $pdf->setXY(30, 20);
        $pdf->MultiCell(190, 5, 'PLAN OPERATIVO ANUAL '.Session::get("ejercicio"), 0, 'L', 0, 0, '', '', true);
        $pdf->setY(30);
        $pdf->MultiCell(277, 5, 'SISTEMA DE SEGUIMIENTO EVALUACIÓN Y CONTROL DEL PLAN OPERATIVO ANUAL (P.O.A)', 0, 'C', 0, 0, '', '', true);
        $pdf->Ln(5);
        $pdf->MultiCell(277, 5, 'FORMULARIO Nº 1', 0, 'C', 0, 0, '', '', true);
        $pdf->Ln(5);
        $pdf->MultiCell(277, 5, 'MARCO NORMATIVO INSTITUCIONAL', 0, 'C', 0, 0, '', '', true);

        return $pdf;
    }

    public function pie($pdf)
    {
        $pdf->setXY(10, -10);
        $pdf->SetFont('', '', 9);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->writeHTMLCell(250, 0, '', '', 'Palacio de los Cóndores, Plaza Bolívar, Maracaibo, Estado Zulia, Venezuela', 0, 0, 0, true, 'C', true);
        $pdf->SetFont('', '', 7);
        $pdf->writeHTMLCell(15, 0, '', '', $pdf->getAliasNumPage().'/'.$pdf->getAliasNbPages(), 0, 0, 0, true, 'C', true);

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
      public function reporte($id)
      {
          
        $lapso = tab_lapso::where('id', '=', $id)
        ->first();
        $data = json_encode(array("id_ejecutor" => Session::get('ejecutor')));
        return View::make('reporte.seguimiento.ac')->with('data', $data)->with('lapso', $lapso);          
      }

      /**
       * Display a listing of the resource.
       *
       * @return \Illuminate\Http\Response
       */
      public function ficha001($id)
      {

          

            $data = tab_ac::join('mantenimiento.tab_ejecutores as t04', 't04.id_ejecutor', '=', 'ac_seguimiento.tab_ac.id_ejecutor')
            ->join('t46_acciones_centralizadas as t46', function ($join) {
            $join->on('t46.id_ejecutor', '=', 'ac_seguimiento.tab_ac.id_ejecutor')
            ->on('t46.id_ejercicio', '=', 'ac_seguimiento.tab_ac.id_tab_ejercicio_fiscal')
            ->on('t46.id_accion', '=', 'ac_seguimiento.tab_ac.id_tab_ac_predefinida');
            })                
            ->join('t49_ac_planes as t49', 't49.id_accion_centralizada', '=', 't46.id')
            ->join('t45_planes_zulia as t45', 't45.co_area_estrategica', '=', 't49.co_area_estrategica')
            ->select(
            'nu_codigo',
            'id_tab_ejecutores',
            'id_tab_ejercicio_fiscal',
            'tx_ejecutor',
            't45.tx_descripcion as tx_area_estrategica',        
            'id_tab_ac_predefinida',
            'id_tab_sectores',
            'id_tab_estatus',
            'id_tab_situacion_presupuestaria',
            'id_tab_tipo_registro',
            'co_new_etapa',
            'de_ac',
            'mo_ac',
            'mo_calculado',
            'fe_inicio',
            'fe_fin',
            'ac_seguimiento.tab_ac.inst_mision',
            'ac_seguimiento.tab_ac.inst_vision',
            'ac_seguimiento.tab_ac.inst_objetivos',
            'ac_seguimiento.tab_ac.nu_po_beneficiar',
            'ac_seguimiento.tab_ac.nu_em_previsto',
            'ac_seguimiento.tab_ac.tx_re_esperado',
            'id_tab_lapso',
            'in_bloquear_001',
            'de_observacion_001'
        )
        ->where('ac_seguimiento.tab_ac.id', '=', $id)
        ->first();
          
//          var_dump($data->nu_codigo);
//          exit();
          
          /******Objetivos*********/

	$htmlObjetivo = '
<table border="0.1" style="width:100%;text-align: center;" cellpadding="3">
	<tr align="left">
		<td colspan="2"><b>1.2. UNIDAD EJECUTORA RESPONSABLE: </b>'.$data->tx_ejecutor.'</td>
	</tr>
	<tr align="left">
		<td colspan="2"><b>2.5.1. AREA ESTRATEGICA: </b>'.$data->tx_area_estrategica.'</td>
	</tr>
	<tr>
		<td><b>MISIÓN</b></td>
		<td><b>VISIÓN</b></td>
	</tr>
	<tr>
		<td height="100" align="justify">'.$data->inst_mision.'</td>
		<td height="100" align="justify">'.$data->inst_vision.'</td>
	</tr>
<thead>
	<tr>
		<td colspan="2"><b>OBJETIVOS INSTITUCIONALES</b></td>
	</tr>
</thead>
<tbody>
	<tr nobr="true">
		<td colspan="2" height="100" align="justify">'.str_replace(array("\r\n","\r","\n","\\r","\\n","\\r\\n"),"<br/>",$data->inst_objetivos).'</td>
	</tr>
</tbody>
</table>';

        

        

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
          $pdf->AddPage();
          $pdf->SetFont('','',11);
//          $pdf->writeHTML($htmlObjetivo, true, false, false, false, '');
          $pdf->writeHTML(Helper::htmlComprimir($htmlObjetivo), true, false, false, false, '');
          $pdf->lastPage();
          $pdf->output('SEGUIMIENTO_AC_'.Session::get("ejercicio").'_'.date("H:i:s").'.pdf', 'D');
      }
      
      
      public function ficha002($id)
      {

            $data = tab_ac::join('mantenimiento.tab_ejecutores as t04', 't04.id_ejecutor', '=', 'ac_seguimiento.tab_ac.id_ejecutor')
            ->join('t46_acciones_centralizadas as t46', function ($join) {
            $join->on('t46.id_ejecutor', '=', 'ac_seguimiento.tab_ac.id_ejecutor')
            ->on('t46.id_ejercicio', '=', 'ac_seguimiento.tab_ac.id_tab_ejercicio_fiscal')
            ->on('t46.id_accion', '=', 'ac_seguimiento.tab_ac.id_tab_ac_predefinida');
            })
            ->join('t52_ac_predefinidas as t52', 't52.id', '=', 't46.id_accion')
            ->leftjoin('t47_ac_accion_especifica as t47', 't47.id_accion_centralizada', '=', 't46.id')
            ->leftjoin('t49_ac_planes as t49', 't49.id_accion_centralizada', '=', 't46.id')
            ->leftjoin('t53_ac_ae_predefinidas as t53', 't53.id', '=', 't47.id_accion')
            ->leftjoin('t45_planes_zulia as t45', function ($join) {
            $join->on('t49.co_area_estrategica', '=', 't45.co_area_estrategica')
            ->on('t45.nu_nivel', '=', DB::raw('0'));
            })
            ->leftjoin('t45_planes_zulia as t45a', function ($join) {
            $join->on('t49.co_area_estrategica', '=', 't45a.co_area_estrategica')
            ->on('t49.co_ambito_estado', '=', 't45a.co_ambito_zulia')        
            ->on('t45a.nu_nivel', '=', DB::raw('1'));
            })
            ->leftjoin('t45_planes_zulia as t45b', function ($join) {
            $join->on('t49.co_ambito_estado', '=', 't45b.co_ambito_zulia')
            ->on('t49.co_macroproblema', '=', 't45b.co_macroproblema')
            ->on('t45b.edo_reg', '=', DB::raw('true'))        
            ->on('t45b.nu_nivel', '=', DB::raw('3'));
            })
            ->leftjoin('t45_planes_zulia as t45c', function ($join) {
            $join->on('t49.co_ambito_estado', '=', 't45c.co_ambito_zulia')
            ->on(DB::raw('t49.co_nodos::integer'), '=', 't45c.co_nodo')       
            ->on('t45c.edo_reg', '=', DB::raw('true'))        
            ->on('t45c.nu_nivel', '=', DB::raw('4'));
            })            
            ->join('mantenimiento.tab_sectores as t18a', 't46.id_subsector', '=', 't18a.id')
            ->join('mantenimiento.tab_sectores as t18b', function ($join) {
            $join->on('t18a.co_sector', '=', 't18b.co_sector')
            ->on('t18b.nu_nivel', '=', DB::raw('1'));
            })
            ->leftjoin('t20_planes as t20', function ($join) {
            $join->on('t49.co_objetivo_historico', '=', 't20.co_objetivo_historico')
            ->on('t20.nu_nivel', '=', DB::raw('1'));
            }) 
            ->leftjoin('t20_planes as t20a', function ($join) {
            $join->on('t49.co_objetivo_nacional', '=', 't20a.co_objetivo_nacional')
            ->on('t49.co_objetivo_historico', '=', 't20a.co_objetivo_historico')        
            ->on('t20a.nu_nivel', '=', DB::raw('2'));
            })
            ->leftjoin('t20_planes as t20b', function ($join) {
            $join->on('t49.co_objetivo_estrategico', '=', 't20b.co_objetivo_estrategico')
            ->on('t49.co_objetivo_historico', '=', 't20b.co_objetivo_historico')        
            ->on('t49.co_objetivo_nacional', '=', 't20b.co_objetivo_nacional')        
            ->on('t20b.nu_nivel', '=', DB::raw('3'));
            })
            ->leftjoin('t20_planes as t20c', function ($join) {
            $join->on('t49.co_objetivo_general', '=', 't20c.co_objetivo_general')
            ->on('t49.co_objetivo_estrategico', '=', 't20c.co_objetivo_estrategico')
            ->on('t49.co_objetivo_historico', '=', 't20c.co_objetivo_historico')        
            ->on('t49.co_objetivo_nacional', '=', 't20c.co_objetivo_nacional')
            ->on('t20c.edo_reg', '=', DB::raw('true'))        
            ->on('t20c.nu_nivel', '=', DB::raw('4'));
            })            
            ->select(
            't46.id as id_accion_centralizada',
            't46.id_ejecutor',
            'tx_ejecutor',
            't18b.tx_codigo as tx_sector',
            't45.tx_descripcion as tx_area_estrategica',
            't20.tx_descripcion as tx_objetivo_historico',
            't20a.tx_descripcion as tx_objetivo_nacional',
            't20b.tx_descripcion as tx_objetivo_estrategico',
            't20c.tx_descripcion as tx_objetivo_general',
            't45a.tx_descripcion as tx_ambito_estado', 
            't45b.tx_descripcion as tx_macroproblema',
            't45c.tx_descripcion as tx_nodos',
            'objetivo_institucional as tx_objetivo_institucional',
            DB::raw("'AC' || t04.id_ejecutor || id_ejercicio || lpad(t46.id_accion::text, 5, '0') as id_proy_ac"),
            't52.nombre',
            DB::raw('t53.numero::text as tx_codigo_ae'),
            't53.nombre as tx_nombre_ae',
            't47.id_ejecutor as id_ejecutor_ae',
            'tx_pr_objetivo',
            't47.id_accion as co_ae'        
        )
        ->where('ac_seguimiento.tab_ac.id', '=', $id)
        ->first();
          

            $actividad = tab_meta_fisica::select('co_metas', 't69_metas_ac.codigo','nb_meta','tx_prog_anual','fecha_inicio','fecha_fin','nb_responsable','de_unidad_medida as tx_unidades_medida')
            ->join('mantenimiento.tab_unidad_medida as t21', 't69_metas_ac.co_unidades_medida', '=', 't21.id')             
            ->where('id_accion_centralizada', '=', $data->id_accion_centralizada)
            ->where('co_ac_acc_espec', '=', $data->co_ae)
            ->where('t69_metas_ac.edo_reg', '=', true)
            ->orderBy('codigo', 'ASC')
            ->get();

          
$html1 = '
<table border="0.1" style="width:100%" style="font-size:10px" cellpadding="3">
<tbody>
<tr style="font-size:9px">
<td style="width: 50%;"><b>'.$data->id_ejecutor.'</b> - '.$data->tx_ejecutor.'</td>
<td style="width: 15%;"><b>SECTOR:</b> '.$data->tx_sector.'</td>
<td style="width: 35%;"><b>AREA ESTRATEGICA:</b> '.$data->tx_area_estrategica.'</td>
</tr>
<tr style="font-size:9px">
<td rowspan="2" style="width: 30%;" align="justify"><b>OBJETIVO HISTORICO:</b> '.$data->tx_objetivo_historico.'</td>
<td colspan="2" style="width: 70%;" align="justify"><b>OBJETIVO(s) NACIONAL(ES):</b> '.$data->tx_objetivo_nacional.'</td>
</tr>
<tr style="font-size:9px">
<td colspan="2" style="width: 70%;" align="justify"><b>OBJETIVO(S) ESTRATEGICO(S):</b> '.$data->tx_objetivo_estrategico.'</td>
</tr>
<tr style="font-size:9px">
<td colspan="3" align="justify"><b>OBJETIVO GENERAL:</b> '.$data->tx_objetivo_general.'</td>
</tr>
<tr style="font-size:9px">
<td rowspan="2"><b>AMBITO:</b> '.$data->tx_ambito_estado.'</td>
<td colspan="2"><b>PDEZ/NOMBRE DEL PROBLEMA:</b> '.$data->tx_macroproblema.'</td>
</tr>
<tr style="font-size:9px">
<td colspan="2"><b>PDEZ/LÍNEA MATRIZ:</b> '.$data->tx_nodos.'</td>
</tr>
<tr style="font-size:9px">
<td colspan="3"><b>OBJETIVO INSTITUCIONAL POA:</b> '.$data->tx_objetivo_institucional.'</td>
</tr>
<tr style="font-size:9px">
<td colspan="3"><b>ACCION C.:</b> '.$data->id_proy_ac.' - '.$data->nombre.'</td>
</tr>
<tr style="font-size:9px">
<td style="width: 80%;"><b>ACCION E.:</b> '.$data->tx_codigo_ae.' - '.$data->tx_nombre_ae.'</td>
<td style="width: 20%;"><b>COD. EJECUTOR:</b> '.$data->id_ejecutor_ae.' </td>
</tr>
<tr style="font-size:9px">
<td colspan="3" style="width: 100%;" align="justify"><b>PRODUCTO PROGRAMADO ANUAL DEL OBJETIVO INSTITUCIONAL:</b> '.$data->tx_pr_objetivo.'</td>
</tr>
</tbody>
</table>
';

$html23='';
$html23.= '
<table border="0.1" style="width:100%" style="font-size:9px" cellpadding="3">
<thead>
<tr align="center" bgcolor="#BDBDBD">
<th colspan="11" style="width: 100%;"><b>METAS FISICAS</b></th>
</tr>
<tr style="font-size:6px">
<th align="center" bgcolor="#BDBDBD" style="width: 18%;">ACTIVIDAD</th>
<th align="center" bgcolor="#BDBDBD" style="width: 7%;">U. MED</th>
<th align="center" bgcolor="#BDBDBD" style="width: 8%;">PROGRAMADA</th>
<th align="center" bgcolor="#BDBDBD" style="width: 8%;">MODIFICADA</th>
<th align="center" bgcolor="#BDBDBD" style="width: 8%;">ACTUALIZADA</th>
<th align="center" bgcolor="#BDBDBD" style="width: 8%;">INICIO</th>
<th align="center" bgcolor="#BDBDBD" style="width: 8%;">TERMINO</th>
<th align="center" bgcolor="#BDBDBD" style="width: 8%;">OBTENIDO AL CORTE</th>
<th align="center" bgcolor="#BDBDBD" style="width: 8%;">% EJEC. OBTENIDA AL CORTE Vs. EJEC. PROG. ANUAL</th>
<th align="center" bgcolor="#BDBDBD" style="width: 10%;">MUNIC / PARROQ</th>
<th align="center" bgcolor="#BDBDBD" style="width: 9%;">RESPONSABLE</th>
</tr>
</thead>
';        
       
$html23.='
<tbody>';
$cantidad = $actividad->count();

$contar=0;
      foreach($actividad as $item) {
          
//                               var_dump($item->nb_responsable);
//          exit();
$contar=$contar+1;
		$html23.='
		<tr style="font-size:6px" nobr="true">
		<td style="width: 18%;"  nobr="true">'.$item->codigo.' - '.$item->nb_meta.'</td>
		<td style="width: 7%;"  align="center">'.$item->tx_unidades_medida.'</td>
		<td style="width: 8%;"  align="center">'.$item->tx_prog_anual.'</td>
		<td style="width: 8%;"  align="center">'.trim(date_format(date_create($item->fecha_inicio),'d/m/Y')).'</td>
		<td style="width: 8%;" align="center">'.trim(date_format(date_create($item->fecha_fin),'d/m/Y')).'</td>
                <td style="width: 8%;" align="center">'.$item->tx_prog_anual.'</td>
                <td style="width: 8%;" align="center">'.$item->tx_prog_anual.'</td>
                <td style="width: 8%;" align="center">'.$item->tx_prog_anual.'</td>
                <td style="width: 8%;" align="center">'.$item->tx_prog_anual.'</td>
                <td style="width: 10%;"  align="center">'.$item->tx_prog_anual.'</td>
		<td style="width: 9%;" align="center">'.$item->nb_responsable.'</td>';
                $html23.='</tr>';
//                				if($cantidad>$contar){
//					$html23.='</tr>
//					<tr style="font-size:6px" nobr="true">';
//				}else{
//					$html23.='';
//				}
                
          
      }        
//$html23.='</tr>';
$html23.='
</tbody>
</table>';

          $pdf = new PDFseguimientoAC("L", PDF_UNIT, 'Letter', true, 'UTF-8', false);
          $pdf->SetCreator('Sistema POA, Yoser Perez');
          $pdf->SetAuthor('Yoser Perez');
          $pdf->SetTitle('Seguimiento AC');
          $pdf->SetSubject('Seguimiento AC');
          $pdf->SetKeywords('Seguimiento AC, PDF, Zulia, SPE, '.Session::get("ejercicio").'');
          $pdf->SetMargins(10,10,10);
          $pdf->SetTopMargin(50);
          $pdf->SetPrintHeader(false);
          $pdf->SetPrintFooter(true);
          // set auto page breaks
          $pdf->SetAutoPageBreak(true, 10);
          $pdf->AddPage();
            $pdf->Image(public_path().'/images/zulia_escudo.png', 10, 10, 20, 18, 'PNG', '', '', true, 150, '', false, false, 0, false, false, false);
        $pdf->setXY(30, 15);
        $pdf->SetFont('', 'B', 11);
        $pdf->MultiCell(190, 5, 'GOBERNACIÓN DEL ESTADO ZULIA', 0, 'L', 0, 0, '', '', true);
                $pdf->setXY(30, 20);
        $pdf->MultiCell(190, 5, 'PLAN OPERATIVO ANUAL '.Session::get("ejercicio"), 0, 'L', 0, 0, '', '', true);
        $pdf->setY(30);
        $pdf->MultiCell(277, 5, 'SISTEMA DE SEGUIMIENTO EVALUACIÓN Y CONTROL DEL PLAN OPERATIVO ANUAL (P.O.A)', 0, 'C', 0, 0, '', '', true);
        $pdf->Ln(5);        
        $pdf->MultiCell(277, 5, 'FORMULARIO Nº 2', 0, 'C', 0, 0, '', '', true);
        $pdf->Ln(5);
        $pdf->MultiCell(277, 5, 'METAS FÍSICAS', 0, 'C', 0, 0, '', '', true);
        $pdf->Ln(5);
          $pdf->SetFont('','',11);
//          $pdf->writeHTML($htmlObjetivo, true, false, false, false, '');
          $pdf->writeHTML(Helper::htmlComprimir($html1), true, false, false, false, '');
          $pdf->Ln(-3);
          $pdf->writeHTML($html23, true, false, false, false, '');
          $pdf->lastPage();
          $pdf->output('SEGUIMIENTO_AC_'.Session::get("ejercicio").'_'.date("H:i:s").'.pdf', 'D');
      }      

}
