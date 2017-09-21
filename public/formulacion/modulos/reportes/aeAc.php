<?php
session_start(); 
if($_SESSION['estatus']!='OK'){
	header('Location: ../../');
}
include("../../configuracion/ConexionComun.php");
define('FPDF_FONTPATH','font/');
require_once('../../plugins/tcpdf/examples/lang/spa.php');
require_once('../../plugins/tcpdf/tcpdf.php');

$original_mem = ini_get('memory_limit');
ini_set('memory_limit','1024M');
ini_set('max_execution_time', 600);

class MYPDF extends TCPDF {
	public $conexion;
//=========================================== Datos del Reporte ====================================================/	

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

	function getRegistro($id_ejecutor, $id_proy_ae){

		$condicionPR='';
		$condicionAC='';
		if($id_ejecutor!= '')
		{
			$condicionPR.= " t26.id_ejecutor = '".$id_ejecutor."' AND ";
			$condicionAC.= " t47.id_ejecutor = '".$id_ejecutor."' AND ";	    
		}

		if($id_proy_ae!= '')
		{
			$condicionPR.= " t26.id_proyecto = '".$id_proy_ae."' AND ";
			$condicionAC.= " ('AC' || t24.id_ejecutor || t46.id_ejercicio || lpad(t47.id_accion::text, 5, '0')) = '".$id_proy_ae."' AND ";	    
		}

		$comunes = new ConexionComun();

		$sql = "select 'AC' || t24.id_ejecutor || id_ejercicio || lpad(t46.id_accion::text, 5, '0') as id_proy_ac, t52.nombre, tx_ejecutor, t46.fecha_inicio, t46.fecha_fin, t46.monto,
	coalesce(t46.monto_calc, 0) as monto_calc, '2' as co_tipo, t46.id_ejecutor, t18b.tx_codigo as tx_sector, t46.id_ejercicio::integer as nu_anio, t45.tx_descripcion as tx_area_estrategica, 
        t20.tx_descripcion as tx_objetivo_historico, t20a.tx_descripcion as tx_objetivo_nacional, t20b.tx_descripcion as tx_objetivo_estrategico, t20c.tx_descripcion as tx_objetivo_general, 
        t53.numero::text as tx_codigo_ae, t53.nombre as tx_nombre_ae, t47.id_accion as co_ae, t46.id as id_accion_centralizada, t46.monto as subtotal_actividades, mo_total_ejecutor( t46.id_ejecutor, t46.id_ejercicio::int) as mo_proyecto_ac, 
        objetivo_institucional as tx_objetivo_institucional, t45a.tx_descripcion as tx_ambito_estado, t45b.tx_descripcion as tx_macroproblema,t49.co_nodos as tx_nodos, t47.id_ejecutor as id_ejecutor_ae, 
        tx_categoria_ac (t47.id_accion_centralizada::integer, t53.numero, t46.id_ejercicio::integer) as tx_categoria_ac
		from t46_acciones_centralizadas as t46
		join t52_ac_predefinidas as t52 on t52.id = t46.id_accion
		join mantenimiento.tab_ejecutores as t24 on t24.id_ejecutor = t46.id_ejecutor
		inner join t18_sectores as t18a on t46.id_subsector=t18a.co_sectores
		inner join t18_sectores as t18b on t18a.co_sector = t18b.co_sector and t18b.nu_nivel = 1
		inner join t49_ac_planes as t49 on t46.id=t49.id_accion_centralizada
		inner join t45_planes_zulia as t45 on t49.co_area_estrategica=t45.co_area_estrategica and t45.nu_nivel = 0
		inner join t45_planes_zulia as t45a on t49.co_area_estrategica=t45a.co_area_estrategica and t49.co_ambito_estado=t45a.co_ambito_zulia and t45a.nu_nivel = 1
		inner join t45_planes_zulia as t45b on t49.co_ambito_estado=t45b.co_ambito_zulia and t49.co_macroproblema=t45b.co_macroproblema and t45b.nu_nivel = 3
		inner join t20_planes as t20 on t49.co_objetivo_historico=t20.co_objetivo_historico and t20.nu_nivel = 1
		inner join t20_planes as t20a on t49.co_objetivo_nacional=t20a.co_objetivo_nacional and t49.co_objetivo_historico=t20a.co_objetivo_historico and t20a.nu_nivel = 2
		inner join t20_planes as t20b on t49.co_objetivo_estrategico=t20b.co_objetivo_estrategico and t49.co_objetivo_historico=t20b.co_objetivo_historico and t49.co_objetivo_nacional=t20b.co_objetivo_nacional and t20b.nu_nivel = 3
		inner join t20_planes as t20c on t49.co_objetivo_general=t20c.co_objetivo_general and t49.co_objetivo_estrategico=t20c.co_objetivo_estrategico and t49.co_objetivo_historico=t20c.co_objetivo_historico and t49.co_objetivo_nacional=t20c.co_objetivo_nacional and t20c.nu_nivel = 4 and t20c.edo_reg is true
		inner join t47_ac_accion_especifica as t47 on t46.id = t47.id_accion_centralizada
		inner join t53_ac_ae_predefinidas as t53 on t53.id = t47.id_accion
		inner join vista_cn_actividad_ac as v1 on v1.id_accion_centralizada=t47.id_accion_centralizada and v1.co_ac_acc_espec=t47.id_accion
	where t46.edo_reg is true and ".$condicionAC." t47.edo_reg is true AND t46.id_ejercicio = ".$_SESSION['ejercicio_fiscal']." order by 9, 8, 1, 17 ASC";

		$this->datos = $comunes->ObtenerFilasBySqlSelect($sql);
		$this->cantidadTotal = $comunes->getFilas($sql);
	}

	public function Footer()	
	{
		/*$this->getRegistro('PR130120150002','');
		foreach($this->datos as $key => $campo){
			$tipo = $campo["co_tipo"];
		}*/
		pie($this,'h',2);
		//$this->Cell(0, 10, 'Pagina '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');  
	}
	public function setHeader()	
	{
		encabezado($this,'h',1);
	}
        public function cuerpo()
        {
		
		if($_GET['id_ejecutor']!= '')
		{
			$id_ejecutor = decode($_GET['id_ejecutor']);	    
		}

		if($_GET['id_proy_ae']!= '')
		{
			$id_proy_ae = decode($_GET['id_proy_ae']);	    
		}

	$this->getRegistro($id_ejecutor, $id_proy_ae);
       	$comunes = new ConexionComun();
	$this->SetFont('','',11);
	//$this->Ln(-20);
	$contador=0;
	$acumulador_ac_a=0;
	$ejecutor_ant = '';
	foreach($this->datos as $key => $campo){
		if($campo["co_tipo"]==1){
			$datosEnunciado='PROYECTO';
			$datosEnunciadoSUBTOTAL='PROYECTO';
			$fieldDatos='Datos del Proyecto';
			$co_proy_ac='Codigo de Proyecto';
			$sqlActividad = "SELECT co_metas, t67.codigo, nb_meta, tx_prog_anual, fecha_inicio, fecha_fin, nb_responsable, de_unidad_medida as tx_unidades_medida FROM t67_metas as t67
			inner join mantenimiento.tab_unidad_medida as t21 on t67.co_unidades_medida=t21.id
			WHERE co_proyecto_acc_espec='".$campo['co_ae']."' and t67.edo_reg is true order by codigo ASC";

			$sqlDetalleMonto= "SELECT SUM(mo_presupuesto) as subtotal_ac FROM t68_metas_detalle as t68
			inner join t67_metas as t67 on t68.co_metas=t67.co_metas
			WHERE co_proyecto_acc_espec='".$campo['co_ae']."' AND t68.edo_reg is true";   
                        
                        $sqlAlcance= "SELECT (benef_femeninos+benef_masculinos) as nu_beneficiarios, (emp_dir_feme+emp_dir_mascu+emp_new_feme+emp_new_mascu+emp_sos_feme+emp_sos_mascu) as nu_empleos FROM t38_proyecto_alcance 
			WHERE id_proyecto='".$campo['id_proy_ac']."' AND edo_reg is true";
                        
		}elseif($campo["co_tipo"]==2){
			$datosEnunciado='ACCION C.';
			$datosEnunciadoSUBTOTAL='ACCION CENTRALIZADA';
			$fieldDatos='Datos de la Accion Centralizada';
			$co_proy_ac='Codigo de la Accion Centralizada';
			$sqlActividad = "SELECT co_metas, t69.codigo, nb_meta, tx_prog_anual, fecha_inicio, fecha_fin, nb_responsable, de_unidad_medida as tx_unidades_medida FROM t69_metas_ac as t69
			inner join mantenimiento.tab_unidad_medida as t21 on t69.co_unidades_medida=t21.id
			WHERE id_accion_centralizada='".$campo['id_accion_centralizada']."' and co_ac_acc_espec='".$campo['co_ae']."' and t69.edo_reg is true order by codigo ASC";

			$sqlDetalleMonto= "SELECT SUM(mo_presupuesto) as subtotal_ac FROM t70_metas_ac_detalle as t70
			inner join t69_metas_ac as t69 on t70.co_metas=t69.co_metas
			WHERE  id_accion_centralizada='".$campo['id_accion_centralizada']."' and co_ac_acc_espec='".$campo['co_ae']."' AND t69.edo_reg is true AND t70.edo_reg is true"; 
                        
                        $sqlAlcance= "SELECT '' as nu_beneficiarios, '' as nu_empleos FROM t47_ac_accion_especifica 
			WHERE id_accion_centralizada='".$campo['id_accion_centralizada']."' and id_accion='".$campo['co_ae']."' AND edo_reg is true";
		}

$html1 = '
<table border="0.1" style="width:100%" style="font-size:10px" cellpadding="3">
<tbody>
<tr align="center" bgcolor="#BDBDBD">
<td colspan="3"><b>PLAN OPERATIVO INSTITUCIONAL - PRESUPUESTO EJERCICIO FISCAL '.$campo['nu_anio'].'</b></td>
</tr>
<tr style="font-size:9px">
<td style="width: 50%;">'.$campo['id_ejecutor'].' - '.$campo['tx_ejecutor'].'</td>
<td style="width: 15%;">SECTOR: '.$campo['tx_sector'].'</td>
<td style="width: 35%;">AREA ESTRATEGICA: '.$campo['tx_area_estrategica'].'</td>
</tr>
<tr style="font-size:9px">
<td colspan="3">'.$datosEnunciado.': '.$campo['id_proy_ac'].' - '.$campo['nombre'].'</td>
</tr>
<tr style="font-size:9px">
<td rowspan="2" style="width: 30%;">OBJETIVO HISTORICO: '.$campo['tx_objetivo_historico'].'</td>
<td colspan="2" style="width: 70%;">OBJETIVO(s) NACIONAL(ES): '.$campo['tx_objetivo_nacional'].'</td>
</tr>
<tr style="font-size:9px">
<td colspan="2" style="width: 70%;">OBJETIVO(S) ESTRATEGICO(S): '.$campo['tx_objetivo_estrategico'].'</td>
</tr>
<tr style="font-size:9px">
<td colspan="3">OBJETIVO GENERAL: '.$campo['tx_objetivo_general'].'</td>
</tr>
<tr style="font-size:9px">
<td rowspan="2">AMBITO: '.$campo['tx_ambito_estado'].'</td>
<td colspan="2">PDEZ/NOMBRE DEL PROBLEMA: '.$campo['tx_macroproblema'].'</td>
</tr>
<tr style="font-size:9px">
<td colspan="2">PDEZ/NUDO CRITICO: '.$campo['tx_nodos'].'</td>
</tr>
<tr style="font-size:9px">
<td colspan="3">OBJETIVO INSTITUCIONAL POA: '.$campo['tx_objetivo_institucional'].'</td>
</tr>
<tr style="font-size:9px">
<td style="width: 80%;">ACCION E.: '.$campo['tx_codigo_ae'].' - '.$campo['tx_nombre_ae'].'</td>
<td style="width: 20%;">COD. EJECUTOR: '.$campo['id_ejecutor_ae'].' </td>
</tr>
</tbody>
</table>
';					    
		$this->writeHTML($html1, true, false, false, false, '');	
		$this->Ln(-3);
$html23=''; 
$html23.= '
<!-- Tabla 2 -->
<table border="0.1" style="width:100%" style="font-size:9px" cellpadding="3">
<thead>
<tr align="center" bgcolor="#BDBDBD">
<th colspan="5" style="width: 48%;"><b>METAS FISICAS</b></th>
<th colspan="6" style="width: 52%;"><b>METAS FINANCIERAS</b></th>
</tr>
<tr style="font-size:6px">
<th align="center" bgcolor="#BDBDBD" style="width: 17%;">Actividad</th>
<th align="center" bgcolor="#BDBDBD" style="width: 7%;">U. Med</th>
<th align="center" bgcolor="#BDBDBD" style="width: 8%;">Programado</th>
<th align="center" bgcolor="#BDBDBD" style="width: 8%;">Inicio</th>
<th align="center" bgcolor="#BDBDBD" style="width: 8%;">Termino</th>
<th align="center" bgcolor="#BDBDBD" style="width: 9%;">REPONSABLE</th>
<th align="center" bgcolor="#BDBDBD" style="width: 10%;">Munic / Parroq</th>
<th align="center" bgcolor="#BDBDBD" style="width: 8%;">PRESUPUESTO</th>
<th align="center" bgcolor="#BDBDBD" style="width: 8%;">CATEGORIA</th>
<th align="center" bgcolor="#BDBDBD" style="width: 7%;">PARTIDA</th>
<th align="center" bgcolor="#BDBDBD" style="width: 10%;">FUENTE FIN.</th>
</tr>
</thead>
';
 
$html23.='
<tbody>';
$this->datos_actividad = $comunes->ObtenerFilasBySqlSelect($sqlActividad);
foreach($this->datos_actividad as $key => $campo2){
	if($campo["co_tipo"]==1){
		$sqlDetalle= "SELECT mo_presupuesto, co_partida, de_municipio as tx_municipio, de_parroquia as tx_parroquia, de_fuente_financiamiento as tx_fuente FROM t68_metas_detalle as t68
		left join mantenimiento.tab_municipio_detalle as t64 on t68.co_municipio=t64.id
		left join mantenimiento.tab_parroquia_detalle as t65 on t68.co_parroquia=t65.id
		inner join mantenimiento.tab_fuente_financiamiento as t66 on t68.co_fuente=t66.id
		WHERE co_metas='".$campo2['co_metas']."' AND t68.edo_reg is true order by tx_municipio, tx_fuente ASC";
	}elseif($campo["co_tipo"]==2){
		$sqlDetalle = "SELECT mo_presupuesto, co_partida, de_municipio as tx_municipio, de_parroquia as tx_parroquia, de_fuente_financiamiento as tx_fuente FROM t70_metas_ac_detalle as t70
		left join mantenimiento.tab_municipio_detalle as t64 on t70.co_municipio=t64.id
		left join mantenimiento.tab_parroquia_detalle as t65 on t70.co_parroquia=t65.id
		inner join mantenimiento.tab_fuente_financiamiento as t66 on t70.co_fuente=t66.id
		WHERE co_metas='".$campo2['co_metas']."' AND t70.edo_reg is true order by tx_municipio, tx_fuente ASC";
	}
	$cantidadDetalle = $comunes->getFilas($sqlDetalle);

	if($cantidadDetalle>1){
		$html23.='
		<tr style="font-size:6px" nobr="true">
		<td style="width: 17%;" colspan="1" rowspan="'.$cantidadDetalle.'" nobr="true">'.$campo2['codigo'].' - '.$campo2['nb_meta'].'</td>
		<td style="width: 7%;" colspan="1" rowspan="'.$cantidadDetalle.'">'.$campo2['tx_unidades_medida'].'</td>
		<td style="width: 8%;" colspan="1" rowspan="'.$cantidadDetalle.'">'.$campo2['tx_prog_anual'].'</td>
		<td style="width: 8%;" colspan="1" rowspan="'.$cantidadDetalle.'">'.trim(date_format(date_create($campo2["fecha_inicio"]),'d/m/Y')).'</td>
		<td style="width: 8%;" colspan="1" rowspan="'.$cantidadDetalle.'">'.trim(date_format(date_create($campo2["fecha_fin"]),'d/m/Y')).'</td>
		<td style="width: 9%;" colspan="1" rowspan="'.$cantidadDetalle.'">'.$campo2['nb_responsable'].'</td>';
		        $this->datos_detalle = $comunes->ObtenerFilasBySqlSelect($sqlDetalle);
			$contar=0;
			foreach($this->datos_detalle as $key => $campo3){
			$contar=$contar+1;
			$html23.='
				<td style="width: 10%;" colspan="1" rowspan="1">'.$campo3['tx_municipio'].' / '.$campo3['tx_parroquia'].'</td>
				<td style="width: 8%;" colspan="1" rowspan="1">'.$this->formatoDinero($campo3['mo_presupuesto']).'</td>
				<td style="width: 8%;" colspan="1" rowspan="1" align="center">'.$campo['tx_categoria_ac'].'</td>
				<td style="width: 7%;" colspan="1" rowspan="1" align="center">'.$campo3['co_partida'].'</td>
				<td style="width: 10%;" colspan="1" rowspan="1" align="center">'.$campo3['tx_fuente'].'</td>
				';

				if($cantidadDetalle>$contar){
					$html23.='</tr>
					<tr style="font-size:6px" nobr="true">';
				}else{
					$html23.='';
				}
			}
		$html23.='</tr>';
	}elseif($cantidadDetalle==1){
	$this->datos_detalle = $comunes->ObtenerFilasBySqlSelect($sqlDetalle);
	foreach($this->datos_detalle as $key => $campo3){
		$html23.='
		<tr style="font-size:6px" nobr="true">
		<td style="width: 17%;">'.$campo2['codigo'].' - '.$campo2['nb_meta'].'</td>
		<td style="width: 7%;">'.$campo2['tx_unidades_medida'].'</td>
		<td style="width: 8%;">'.$campo2['tx_prog_anual'].'</td>
		<td style="width: 8%;" >'.trim(date_format(date_create($campo2["fecha_inicio"]),'d/m/Y')).'</td>
		<td style="width: 8%;" >'.trim(date_format(date_create($campo2["fecha_fin"]),'d/m/Y')).'</td>
		<td style="width: 9%;" >'.$campo2['nb_responsable'].'</td>
		<td style="width: 10%;" >'.$campo3['tx_municipio'].' / '.$campo3['tx_parroquia'].'</td>
		<td style="width: 8%;" >'.$this->formatoDinero($campo3['mo_presupuesto']).'</td>
		<td style="width: 8%;" align="center">'.$campo['tx_categoria_ac'].'</td>
		<td style="width: 7%;" align="center">'.$campo3['co_partida'].'</td>
		<td style="width: 10%;" align="center">'.$campo3['tx_fuente'].'</td>
		</tr>';
	}
	}elseif($cantidadDetalle==0){
		$html23.='
		<tr style="font-size:6px" nobr="true">
		<td style="width: 17%;">'.$campo2['codigo'].' - '.$campo2['nb_meta'].'</td>
		<td style="width: 7%;">'.$campo2['tx_unidades_medida'].'</td>
		<td style="width: 8%;">'.$campo2['tx_prog_anual'].'</td>
		<td style="width: 8%;" >'.trim(date_format(date_create($campo2["fecha_inicio"]),'d/m/Y')).'</td>
		<td style="width: 8%;" >'.trim(date_format(date_create($campo2["fecha_fin"]),'d/m/Y')).'</td>
		<td style="width: 9%;" >'.$campo2['nb_responsable'].'</td>
		<td style="width: 10%;" align="center" cellpadding="6">N/A</td>
		<td style="width: 8%;" align="center" cellpadding="6">N/A</td>
		<td style="width: 8%;" align="center" cellpadding="6">N/A</td>
		<td style="width: 7%;" align="center" cellpadding="6">N/A</td>
		<td style="width: 10%;" align="center" cellpadding="6">N/A</td>
		</tr>';
	}
}
$html23.='
</tbody>
</table>';
/*echo $html23;
exit();*/
		/*$this->writeHTML($html2, true, false, false, false, '');
		$this->Ln(-7);*/

		$this->writeHTML($html23, true, false, false, false, '');
		$this->Ln(-3);
$this->actividad_monto = $comunes->ObtenerFilasBySqlSelect($sqlDetalleMonto);
$html3 = '
<!-- Tabla 3 -->
<table border="0.1" style="width:100%" style="font-size:7px" cellpadding="3">
<tbody>
<tr nobr="true">
<td colspan="6" align="right"><b>SUBTOTAL ACTIVIDADES</b></td>
<td colspan="5" align="left"><b>'.$this->formatoDinero($this->actividad_monto[0]['subtotal_ac']).'</b></td>
</tr>
</tbody>
</table>
';
		$this->writeHTML($html3, true, false, false, false, '');
		$this->Ln(-3);	

		if($campo["id_ejecutor"]!=$ejecutor_ant){ $acumulador_ac_a = 0; }

		$acumulador_ac_a = $acumulador_ac_a+$this->actividad_monto[0]['subtotal_ac'];

		$ejecutor_ant = $campo["id_ejecutor"];
                
$this->monto_alcance = $comunes->ObtenerFilasBySqlSelect($sqlAlcance);
$html4 = '
<!-- Tabla 4 -->
<table border="0.1" style="width:100%" style="font-size:7px" cellpadding="3">
<tbody>
<tr nobr="true">
<td rowspan="2" colspan="6" align="right"><b>SUBTOTAL '.$datosEnunciadoSUBTOTAL.'</b></td>
<td rowspan="2" colspan="2" align="left"><b>'.$this->formatoDinero($acumulador_ac_a).'</b></td>
<td colspan="3" align="left" style="font-size:6px">POBLACION A BENEFICIAR: '.$this->monto_alcance[0]['nu_beneficiarios'].'</td>
</tr>
<tr nobr="true">
<td colspan="3" align="left" style="font-size:6px">EMPLEOS PREVISTOS: '.$this->monto_alcance[0]['nu_empleos'].'</td>
</tr>
</tbody>
</table>
';
		$this->writeHTML($html4, true, false, false, false, '');
		$this->Ln(-3);	
$html5 = '
<!-- Tabla 5 -->
<table border="0.1" style="width:100%" style="font-size:7px" cellpadding="3">
<tbody>
<tr nobr="true">
<td colspan="6" align="right"><b>TOTAL EJECUTOR</b></td>
<td colspan="5" align="left"><b>'.$this->formatoDinero($campo["mo_proyecto_ac"]).'</b></td>
</tr>
</tbody>
</table>
';
		$this->writeHTML($html5, true, false, false, false, '');
		$this->Ln(-3);	
$html6 = '
<!-- Tabla 6 -->
<table border="0.1" style="width:100%" style="font-size:7px" cellpadding="3">
<tbody>
<tr nobr="true">
<td colspan="11" align="left"><b>RESULTADOS ESPERADOS:</b></td>
</tr>
</tbody>
</table>
';
		$this->writeHTML($html6, true, false, false, false, '');
		$contador=$contador+1;
		if($this->cantidadTotal>$contador){
			$this->AddPage();
			//$this->Ln(-20);
		}
		}
        }
}

//Crear new PDF documento
$pdf = new MYPDF("L", PDF_UNIT, 'Letter', true, 'UTF-8', false);
$pdf->SetCreator('Yoser Perez');
$pdf->SetAuthor('Secretaria de Planificacion y Estadistica');
$pdf->SetTitle('ACCIONES ESPECIFICAS');
$pdf->SetSubject('MI DOCUMENTO');
$pdf->SetKeywords('Planilla, PDF, Registro');
$pdf->SetMargins(10,20,10);
$pdf->SetTopMargin(23);
$pdf->setPrintHeader(false);
$pdf->SetPrintFooter(true);
$pdf->AddPage();
$pdf->cuerpo();
$pdf->Output('POA_AC_'.$_SESSION['ejercicio_fiscal'].'_'.date("H:i:s").'.pdf', 'D');
