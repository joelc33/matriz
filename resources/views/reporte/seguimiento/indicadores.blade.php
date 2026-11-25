<script type="text/javascript">
Ext.ns('reporteIndicadoresAC');
reporteIndicadoresAC.main = {
init: function(){

this.OBJ = paqueteComunJS.funcion.doJSON({stringData:'{!! $data !!}'});
this.initialLapso = this.OBJ.id_tab_lapso || '';

<?php $rol_planificador = array(3, 8); ?>

//<Stores>
this.storeLAPSO             = this.getStoreLAPSO();
this.storeEJECUTOR          = this.getStoreEJECUTOR();
this.storeTIPO_INDICADOR    = this.getStoreTIPO_INDICADOR();
this.storeSUB_TIPO_INDICADOR= this.getStoreSUB_TIPO_INDICADOR();
this.storeMUNICIPIO         = this.getStoreMUNICIPIO();
this.storePARROQUIA         = this.getStorePARROQUIA();
this.storeCOMUNA            = this.getStoreCOMUNA();
this.store_lista            = this.getStoreLISTA();
//</Stores>

this.id_tab_lapso = new Ext.form.ComboBox({
	fieldLabel: 'PERIODO / LAPSO',
	store: this.storeLAPSO,
	typeAhead: true,
	valueField: 'id',
	displayField: 'rango',
	hiddenName: 'id_tab_lapso',
	forceSelection: true,
	resizable: true,
	triggerAction: 'all',
	emptyText: 'Todos los Periodos del Ejercicio {{ $ejercicio }}',
	selectOnFocus: true,
	mode: 'local',
	width: 340,
	listeners: {
		select: function() {
			reporteIndicadoresAC.main.initialLapso = '';
		}
	}
});
this.storeLAPSO.load();
if (this.OBJ.id_tab_lapso != '') {
	paqueteComunJS.funcion.seleccionarComboByCo({
		objCMB: this.id_tab_lapso,
		value: this.OBJ.id_tab_lapso,
		objStore: this.storeLAPSO
	});
}

this.id_tab_ejecutores = new Ext.form.ComboBox({
	fieldLabel: 'UNIDAD EJECUTORA',
	store: this.storeEJECUTOR,
	typeAhead: true,
	valueField: 'id',
	displayField: 'de_ejecutor',
	hiddenName: 'id_tab_ejecutores',
	@if (in_array(Session::get('rol'), $rol_planificador))
		readOnly: true,
		style: 'background:#c9c9c9;',
	@endif
	forceSelection: true,
	resizable: true,
	triggerAction: 'all',
	emptyText: 'Todas las Unidades Ejecutoras',
	selectOnFocus: true,
	mode: 'local',
	width: 340,
	itemSelector: 'div.search-item',
	tpl: new Ext.XTemplate('<tpl for="."><div class="search-item"><div class="desc">{de_ejecutor}</div></div></tpl>'),
	listeners: {
		keyup: function() {
			this.store.filter('de_ejecutor', this.getRawValue(), true, false);
		},
		beforequery: function(queryEvent) {
			queryEvent.combo.onLoad();
			return false;
		}
	}
});
this.storeEJECUTOR.load();
if (this.OBJ.id_tab_ejecutores != '') {
	paqueteComunJS.funcion.seleccionarComboByCo({
		objCMB: this.id_tab_ejecutores,
		value: this.OBJ.id_tab_ejecutores,
		objStore: this.storeEJECUTOR
	});
}

this.id_tab_tipo_indicador = new Ext.form.ComboBox({
	fieldLabel: 'LÍNEA DE ACCIÓN',
	store: this.storeTIPO_INDICADOR,
	typeAhead: true,
	valueField: 'id',
	displayField: 'de_tipo_indicador',
	hiddenName: 'id_tab_tipo_indicador',
	forceSelection: true,
	resizable: true,
	triggerAction: 'all',
	emptyText: 'Todas las Líneas de Acción',
	selectOnFocus: true,
	mode: 'local',
	width: 340,
	itemSelector: 'div.search-item',
	tpl: new Ext.XTemplate('<tpl for="."><div class="search-item"><div class="desc">{de_tipo_indicador}</div></div></tpl>'),
	listeners: {
		select: function(cmb, record) {
			reporteIndicadoresAC.main.id_tab_sub_tipo_indicador.clearValue();
			reporteIndicadoresAC.main.storeSUB_TIPO_INDICADOR.removeAll();
			reporteIndicadoresAC.main.storeSUB_TIPO_INDICADOR.load({
				params: {
					id_tab_tipo_indicador: record.get('id'),
					_token: '{{ csrf_token() }}'
				}
			});
		}
	}
});
this.storeTIPO_INDICADOR.load();

this.id_tab_sub_tipo_indicador = new Ext.form.ComboBox({
	fieldLabel: 'TIPO INDICADOR',
	store: this.storeSUB_TIPO_INDICADOR,
	typeAhead: true,
	valueField: 'id',
	displayField: 'de_sub_tipo_indicador',
	hiddenName: 'id_tab_sub_tipo_indicador',
	forceSelection: true,
	resizable: true,
	triggerAction: 'all',
	emptyText: 'Todos los Tipos de Indicador',
	selectOnFocus: true,
	mode: 'local',
	width: 340,
	itemSelector: 'div.search-item',
	tpl: new Ext.XTemplate('<tpl for="."><div class="search-item"><div class="desc">{de_sub_tipo_indicador}</div></div></tpl>')
});

this.id_tab_municipio_detalle = new Ext.form.ComboBox({
	fieldLabel: 'MUNICIPIO',
	store: this.storeMUNICIPIO,
	typeAhead: true,
	valueField: 'id',
	displayField: 'de_municipio',
	hiddenName: 'id_tab_municipio_detalle',
	forceSelection: true,
	resizable: true,
	triggerAction: 'all',
	emptyText: 'Todos los Municipios',
	selectOnFocus: true,
	mode: 'local',
	width: 340,
	itemSelector: 'div.search-item',
	tpl: new Ext.XTemplate('<tpl for="."><div class="search-item"><div class="desc">{de_municipio}</div></div></tpl>'),
	listeners: {
		select: function(cmb, record) {
			reporteIndicadoresAC.main.id_tab_parroquia_detalle.clearValue();
			reporteIndicadoresAC.main.id_tab_comuna.clearValue();
			reporteIndicadoresAC.main.storePARROQUIA.removeAll();
			reporteIndicadoresAC.main.storeCOMUNA.removeAll();
			reporteIndicadoresAC.main.storePARROQUIA.load({
				params: {
					id_tab_municipio: record.get('id'),
					_token: '{{ csrf_token() }}'
				}
			});
		}
	}
});
this.storeMUNICIPIO.load();

this.id_tab_parroquia_detalle = new Ext.form.ComboBox({
	fieldLabel: 'PARROQUIA',
	store: this.storePARROQUIA,
	typeAhead: true,
	valueField: 'id',
	displayField: 'de_parroquia',
	hiddenName: 'id_tab_parroquia_detalle',
	forceSelection: true,
	resizable: true,
	triggerAction: 'all',
	emptyText: 'Todas las Parroquias',
	selectOnFocus: true,
	mode: 'local',
	width: 340,
	listeners: {
		select: function(cmb, record) {
			reporteIndicadoresAC.main.id_tab_comuna.clearValue();
			reporteIndicadoresAC.main.storeCOMUNA.removeAll();
			reporteIndicadoresAC.main.storeCOMUNA.load({
				params: {
					id_tab_parroquia_detalle: record.get('id'),
					_token: '{{ csrf_token() }}'
				}
			});
		}
	}
});

this.id_tab_comuna = new Ext.form.ComboBox({
	fieldLabel: 'COMUNA',
	store: this.storeCOMUNA,
	typeAhead: true,
	valueField: 'id',
	displayField: 'de_comuna',
	hiddenName: 'id_tab_comuna',
	forceSelection: true,
	resizable: true,
	triggerAction: 'all',
	emptyText: 'Todas las Comunas',
	selectOnFocus: true,
	mode: 'local',
	width: 340
});

this.texto = new Ext.form.TextField({
	fieldLabel: 'BUSCAR TEXTO',
	name: 'texto',
	emptyText: 'Descripción, ejecutor o línea de acción...',
	width: 340,
	enableKeyEvents: true,
	listeners: {
		specialkey: function(f, e) {
			if (e.getKey() == e.ENTER) {
				reporteIndicadoresAC.main.onConsultar();
			}
		}
	}
});

this.fieldsetFiltros = new Ext.form.FieldSet({
	title: 'Parámetros de Consulta - Ejercicio Fiscal {{ $ejercicio }}',
	layout: 'column',
	autoHeight: true,
	items: [
		{
			columnWidth: 0.5,
			layout: 'form',
			border: false,
			labelWidth: 135,
			items: [
				this.id_tab_lapso,
				this.id_tab_tipo_indicador,
				this.id_tab_sub_tipo_indicador,
				this.texto
			]
		},
		{
			columnWidth: 0.5,
			layout: 'form',
			border: false,
			labelWidth: 135,
			items: [
				this.id_tab_ejecutores,
				this.id_tab_municipio_detalle,
				this.id_tab_parroquia_detalle,
				this.id_tab_comuna
			]
		}
	]
});

this.formpanel = new Ext.form.FormPanel({
	bodyStyle: 'padding:5px',
	autoWidth: true,
	autoHeight: true,
	border: false,
	id: 'formaReporteIndicadoresAC',
	iconCls: 'icon-reporteest',
	title: 'REPORTE DE GESTIÓN DE INDICADORES POR LÍNEA DE ACCIÓN / EJECUTOR',
	items: [
		this.fieldsetFiltros
	],
	bbar: [
		{
			text: 'Consultar',
			iconCls: 'icon-buscar',
			handler: this.onConsultar
		},
		'-',
		{
			text: 'Limpiar Filtros',
			iconCls: 'icon-limpiar',
			handler: this.onLimpiar
		},
		'->',
		{
			text: 'PDF por Línea de Acción',
			iconCls: 'icon-pdf',
			handler: this.onExportarPdfLinea
		},
		'-',
		{
			text: 'PDF por Ejecutor',
			iconCls: 'icon-pdf',
			handler: this.onExportarPdfEjecutor
		},
		'-',
		{
			text: 'Excel por Línea de Acción',
			iconCls: 'icon-excel',
			handler: this.onExportarLinea
		},
		'-',
		{
			text: 'Excel por Ejecutor',
			iconCls: 'icon-excel',
			handler: this.onExportarEjecutor
		}
	]
});

this.gridPanel_ = new Ext.grid.GridPanel({
	title: 'Vista Previa de Indicadores de Gestión',
	iconCls: 'icon-libro',
	store: this.store_lista,
	border: true,
	loadMask: true,
	autoWidth: true,
	height: 360,
	stripeRows: true,
	autoScroll: true,
	columns: [
		new Ext.grid.RowNumberer({width: 35}),
		{header: 'Código Ejecutor',       width: 95,  sortable: true, dataIndex: 'codigo_ejecutor'},
		{header: 'Ejecutor',              width: 220, sortable: true, renderer: textoLargo, dataIndex: 'ejecutor'},
		{header: 'Línea de Acción',       width: 200, sortable: true, renderer: textoLargo, dataIndex: 'linea_accion'},
		{header: 'Tipo Indicador',        width: 230, sortable: true, renderer: textoLargo, dataIndex: 'tipo_indicador'},
		{header: 'Periodo',               width: 130, sortable: true, dataIndex: 'periodo'},
		{header: 'Unidad de Medida',      width: 130, sortable: true, renderer: textoLargo, dataIndex: 'unidad_medida'},
		{header: 'Cantidad',              width: 85,  sortable: true, align: 'right', dataIndex: 'cantidad'},
		{header: 'Descripción / Detalle', width: 280, sortable: true, renderer: textoLargo, dataIndex: 'descripcion'},
		{header: 'Municipios',            width: 130, sortable: true, renderer: textoLargo, dataIndex: 'municipio'},
		{header: 'Parroquias',            width: 150, sortable: true, renderer: textoLargo, dataIndex: 'parroquia'},
		{header: 'Comunas',               width: 180, sortable: true, renderer: textoLargo, dataIndex: 'comuna'}
	],
	bbar: new Ext.PagingToolbar({
		pageSize: 25,
		store: this.store_lista,
		displayInfo: true,
		displayMsg: '<span style="color:black">Registros: {0} - {1} de {2}</span>',
		emptyMsg: '<span style="color:black">No se encontraron indicadores para los filtros seleccionados</span>'
	})
});

this.panelContenedor = new Ext.Panel({
	border: false,
	autoWidth: true,
	autoScroll: true,
	items: [
		this.formpanel,
		this.gridPanel_
	]
});

this.panelContenedor.render('contenedorReporteIndicadoresAC');

// Carga inicial del grid
this.onConsultar();
},

obtenerParametros: function() {
	var me = reporteIndicadoresAC.main;
	var lapsoVal = me.id_tab_lapso.getValue();
	if (!lapsoVal && me.initialLapso) {
		lapsoVal = me.initialLapso;
	}
	return {
		_token: '{{ csrf_token() }}',
		id_tab_lapso: lapsoVal,
		id_tab_ejecutores: me.id_tab_ejecutores.getValue(),
		id_tab_tipo_indicador: me.id_tab_tipo_indicador.getValue(),
		id_tab_sub_tipo_indicador: me.id_tab_sub_tipo_indicador.getValue(),
		id_tab_municipio_detalle: me.id_tab_municipio_detalle.getValue(),
		id_tab_parroquia_detalle: me.id_tab_parroquia_detalle.getValue(),
		id_tab_comuna: me.id_tab_comuna.getValue(),
		texto: me.texto.getValue()
	};
},

construirQueryString: function() {
	var p = reporteIndicadoresAC.main.obtenerParametros();
	var qs = [];
	if (p.id_tab_lapso)              qs.push('id_tab_lapso=' + encodeURIComponent(p.id_tab_lapso));
	if (p.id_tab_ejecutores)         qs.push('id_tab_ejecutores=' + encodeURIComponent(p.id_tab_ejecutores));
	if (p.id_tab_tipo_indicador)     qs.push('id_tab_tipo_indicador=' + encodeURIComponent(p.id_tab_tipo_indicador));
	if (p.id_tab_sub_tipo_indicador) qs.push('id_tab_sub_tipo_indicador=' + encodeURIComponent(p.id_tab_sub_tipo_indicador));
	if (p.id_tab_municipio_detalle)  qs.push('id_tab_municipio_detalle=' + encodeURIComponent(p.id_tab_municipio_detalle));
	if (p.id_tab_parroquia_detalle)  qs.push('id_tab_parroquia_detalle=' + encodeURIComponent(p.id_tab_parroquia_detalle));
	if (p.id_tab_comuna)             qs.push('id_tab_comuna=' + encodeURIComponent(p.id_tab_comuna));
	if (p.texto)                     qs.push('texto=' + encodeURIComponent(p.texto));
	return qs.length > 0 ? '?' + qs.join('&') : '';
},

onConsultar: function() {
	var me = reporteIndicadoresAC.main;
	me.store_lista.baseParams = me.obtenerParametros();
	me.store_lista.load({params: {start: 0, limit: 25}});
},

onLimpiar: function() {
	var me = reporteIndicadoresAC.main;
	me.initialLapso = '';
	me.id_tab_lapso.clearValue();
	me.id_tab_tipo_indicador.clearValue();
	me.id_tab_sub_tipo_indicador.clearValue();
	me.storeSUB_TIPO_INDICADOR.removeAll();
	me.id_tab_municipio_detalle.clearValue();
	me.id_tab_parroquia_detalle.clearValue();
	me.storePARROQUIA.removeAll();
	me.id_tab_comuna.clearValue();
	me.storeCOMUNA.removeAll();
	me.texto.setValue('');
	@if (!in_array(Session::get('rol'), array(3, 8)))
		me.id_tab_ejecutores.clearValue();
		me.storeEJECUTOR.clearFilter();
	@endif
	me.onConsultar();
},

onExportarPdfLinea: function() {
	var qs = reporteIndicadoresAC.main.construirQueryString();
	bajar.load({
		url: '{{ URL::to('reporte/ac/indicadores/pdf/linea') }}' + qs
	});
},

onExportarPdfEjecutor: function() {
	var qs = reporteIndicadoresAC.main.construirQueryString();
	bajar.load({
		url: '{{ URL::to('reporte/ac/indicadores/pdf/ejecutor') }}' + qs
	});
},

onExportarLinea: function() {
	var qs = reporteIndicadoresAC.main.construirQueryString();
	bajar.load({
		url: '{{ URL::to('reporte/ac/indicadores/exportar/linea') }}' + qs
	});
},

onExportarEjecutor: function() {
	var qs = reporteIndicadoresAC.main.construirQueryString();
	bajar.load({
		url: '{{ URL::to('reporte/ac/indicadores/exportar/ejecutor') }}' + qs
	});
},

getStoreLISTA: function() {
	return new Ext.data.JsonStore({
		url: '{{ URL::to('reporte/ac/indicadores/storeLista') }}',
		root: 'data',
		totalProperty: 'total',
		fields: [
			{name: 'id'},
			{name: 'linea_accion'},
			{name: 'tipo_indicador'},
			{name: 'periodo'},
			{name: 'unidad_medida'},
			{name: 'cantidad'},
			{name: 'descripcion'},
			{name: 'municipio'},
			{name: 'parroquia'},
			{name: 'comuna'},
			{name: 'codigo_ejecutor'},
			{name: 'ejecutor'}
		]
	});
},

getStoreLAPSO: function() {
	return new Ext.data.JsonStore({
		url: '{{ URL::to('auxiliar/lapso') }}',
		root: 'data',
		fields: [
			{name: 'id'},
			{name: 'id_tab_ejercicio_fiscal'},
			{name: 'de_lapso'},
			{name: 'fe_inicio'},
			{name: 'fe_fin'},
			{
				name: 'rango',
				convert: function(v, r) {
					return r.de_lapso + ' ' + r.id_tab_ejercicio_fiscal + ' (' + r.fe_inicio + ' - ' + r.fe_fin + ')';
				}
			}
		]
	});
},

getStoreEJECUTOR: function() {
	return new Ext.data.JsonStore({
		url: '{{ URL::to('auxiliar/ejecutor/activo') }}',
		root: 'data',
		fields: [
			{name: 'id'},
			{name: 'id_ejecutor'},
			{name: 'tx_ejecutor'},
			{
				name: 'de_ejecutor',
				convert: function(v, r) {
					return r.id_ejecutor + ' - ' + r.tx_ejecutor;
				}
			}
		],
		filter: function(filters, value) {
			Ext.data.Store.prototype.filter.apply(this, [
				filters,
				value ? new RegExp(String.escape(value), 'i') : value
			]);
		}
	});
},

getStoreTIPO_INDICADOR: function() {
	return new Ext.data.JsonStore({
		url: '{{ URL::to('auxiliar/tipoindicador') }}',
		root: 'data',
		fields: [
			{name: 'id'},
			{name: 'de_tipo_indicador'},
			{name: 'in_territorial'}
		]
	});
},

getStoreSUB_TIPO_INDICADOR: function() {
	return new Ext.data.JsonStore({
		url: '{{ URL::to('auxiliar/subtipoindicador') }}',
		root: 'data',
		fields: [
			{name: 'id'},
			{name: 'de_sub_tipo_indicador'},
			{name: 'in_territorial'}
		]
	});
},

getStoreMUNICIPIO: function() {
	return new Ext.data.JsonStore({
		url: '{{ URL::to('auxiliar/municipio/todo') }}',
		root: 'data',
		fields: [
			{name: 'id'},
			{name: 'de_municipio'}
		]
	});
},

getStorePARROQUIA: function() {
	return new Ext.data.JsonStore({
		url: '{{ URL::to('auxiliar/parroquia/todo') }}',
		root: 'data',
		fields: [
			{name: 'id'},
			{name: 'de_parroquia'}
		]
	});
},

getStoreCOMUNA: function() {
	return new Ext.data.JsonStore({
		url: '{{ URL::to('auxiliar/comuna/todo') }}',
		root: 'data',
		fields: [
			{name: 'id'},
			{name: 'de_comuna'}
		]
	});
}
};
Ext.onReady(reporteIndicadoresAC.main.init, reporteIndicadoresAC.main);
</script>
<div id="contenedorReporteIndicadoresAC"></div>
