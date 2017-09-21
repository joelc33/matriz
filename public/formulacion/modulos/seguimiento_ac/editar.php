<?php
require_once '../../comun.php';

$id_accion = null;
if (array_key_exists( 'codigo', $_POST ) ) {
    $id_accion = $_POST['codigo'];
}

$accion = null;
$local = $usuario->co_rol > 2; //es planificador local?

if ($id_accion!=''||$id_accion!=null) {
	$params = array();
	$sql = <<<EOT
SELECT t46.id, id_tab_ejercicio_fiscal as id_ejercicio, t46.id_tab_ejecutores as id_ejecutor, t24.tx_ejecutor as ejecutor,
nu_codigo,
co_new_etapa as co_sistema, t46.id_tab_ac_predefinida as id_accion, de_ac as descripcion,
id_tab_estatus, id_tab_sectores as id_subsector, id_tab_situacion_presupuestaria as co_situacion_presupuestaria,
mo_ac as monto, t18.co_sector, fe_inicio as fecha_inicio, fe_fin as fecha_fin, t52.de_nombre, 
inst_mision, inst_vision, inst_objetivos, nu_po_beneficiar, nu_em_previsto, tx_re_esperado, 
(t46.id_tab_estatus = 3) as bloqueado
FROM ac_seguimiento.tab_ac as t46
JOIN mantenimiento.tab_sectores as t18 on t46.id_tab_sectores = t18.id
JOIN mantenimiento.tab_ejecutores as t24 on t46.id_tab_ejecutores = t24.id_ejecutor
JOIN mantenimiento.tab_ac_predefinida as t52 on t52.id = t46.id_tab_ac_predefinida
EOT;
	$where = ' WHERE t46.nu_codigo = ?';
	$params[] = $id_accion;

	if ( $local ) { //planificador local sólo ve los de su ejecutor
		$params[] = $usuario->id_ejecutor;
		$where .= ' AND t46.id_tab_ejecutores = ?';
	}

	$res = $comunes->ObtenerFilasBySqlSelect( $sql.$where, $params );
	if ( count( $res ) > 0 ) {
		$accion = $res[0];
		$id_ejercicio = $res['id_ejercicio'];
		$contenedor = "ac_{$id_accion}";
		$accion['es_local'] = $local;
		$accion['bloqueado'] = $accion['bloqueado'] === 't';
	}
}

if ( is_null( $accion ) ) {
	$ejercicio = $comunes->ObtenerFilasBySqlSelect( <<<EOT
SELECT co_ejercicio_fiscal FROM t25_ejercicio_fiscal
WHERE edo_reg is true LIMIT 1;
EOT
	)[0];
	$id_ejercicio = $ejercicio['co_ejercicio_fiscal'];
	$contenedor = "ac_nueva";

	$accion = array(
		'id' => null,
		'codigo' => null,
		'id_ejercicio' => $id_ejercicio,
		'id_ejecutor' => $usuario->id_ejecutor,
		'es_local' => $local,
		'bloqueado' => false,
		'id_accion' => null,
		'co_sistema' => null,
		'descripcion' => null,
		'sit_presupuesto' => null,
		'monto' => null,
		'co_sector' => null,
		'id_subsector' => null,
		'fecha_inicio' => "01-01-{$id_ejercicio}",
		'fecha_fin' => "31-12-{$id_ejercicio}"
	);
}

?>
<!DOCTYPE html>
<html>
<head>
</head>
<body>
<div id="<?php echo $contenedor; ?>"></div>

<script type="text/javascript">
<?php
	echo file_get_contents('../../js/async.js');
	echo file_get_contents('../../js/overrides.js');
	echo file_get_contents('../../js/Reingsys.js');
	echo file_get_contents('ac_editar.js');
	echo file_get_contents('ac_ae.js');
?>
Ext.onReady( function() {
	var ac = <?php echo json_encode( $accion ); ?>;
	var panel = Ext.create({
		xtype: 'accion_centralizada',
		frm: {
			ac: ac,
			extra: {
				fecha_ini: '01-01-' + ac.id_tab_ejercicio_fiscal,
				fecha_fin: '31-12-' + ac.id_tab_ejercicio_fiscal,
			}
		}
	});
	panel.render( '<?php echo $contenedor; ?>' );
});
</script>
</body>
</html>
