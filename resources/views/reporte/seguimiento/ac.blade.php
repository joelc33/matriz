<script type="text/javascript">
Ext.ns('parametroSeguimientoAC');
parametroSeguimientoAC.main = {
init: function(){

//<Stores de fk>
this.storeCO_PERIODO = this.getStoreCO_PERIODO();
//<Stores de fk>

this.multiselecPeriodo = new Ext.ux.Multiselect({
		fieldLabel: 'Periodo',
		name: 'periodo',
		id: 'periodo',
		valueField: 'id',
		displayField: 'rango',
		//iconCls:'icon-multiSel',
		width: 300,
		height: 150,
		allowBlank:false,
		store: this.storeCO_PERIODO,
		tbar:[{
				text: 'Limpiar Selección',
				iconCls:'icon-limpiar',
				handler: function(){
								parametroSeguimientoAC.main.formpanel.getForm().findField('periodo').reset();
						}
		}],
		ddReorder: true
});

this.storeCO_PERIODO.load();


this.fieldset1 = new Ext.form.FieldSet({
    title: 'Seleccione Parametros',
    items:[
      this.multiselecPeriodo
		]
});

this.GrupoBotones = Ext.extend(Ext.Panel, {
		autoWidth:true,
		autoHeight:true,
		style: 'margin-top:5px',
		bodyStyle: 'padding:5px',
		autoScroll: true
});

this.botones = new this.GrupoBotones({
				//title: 'Opciones',
				items:[
					this.fieldset1
				],
				bbar: [{
						xtype: 'buttongroup',
						title: 'Formatos',
						columns: 6,
						defaults: {
								scale: 'medium',
								iconAlign:'top'
						},
						items: [
							@if( in_array( array( 'de_privilegio' => 'ubicacion.ac.municipio', 'in_habilitado' => true), Session::get('credencial') ))
								{
									text:'REPORTE',  // Generar la impresión en pdf
									iconCls:'icon-pdf',
									handler: this.onImprimir
								},
							@endif
							{
								text:'Limpiar',  // Limpiar campos del formulario
								iconCls:'icon-limpiar',
								handler: this.onLimpiar
							}
						]
				}]
});

this.formpanel = new Ext.form.FormPanel({
	bodyStyle: 'padding:5px',
	autoWidth:true,
	autoHeight:true,
	border:false,
	id: 'forma',
	labelWidth: 120,
	iconCls:'icon-reporteest',
	title: 'SEGUIMIENTO - ACCION CENTRALIZADA',
	items:[
		this.botones
	]
});

this.formpanel.render('parametroSeguimientoAC');
},
onImprimir : function() {
if(!parametroSeguimientoAC.main.formpanel.getForm().isValid()){
    Ext.Msg.alert("Alerta","Debe ingresar los campos en rojo");
    return false;
}
	bajar.load({
		url: '{{ URL::to('reporte/poa/ac/ubicacion') }}?'+parametroSeguimientoAC.main.formpanel.getForm().getValues(true)
	});
},
onImprimir1 : function() {
	bajar.load({
		url: '{{ URL::to('reporte/poa/ac/ubicacion/todo') }}'
	});
},
onExportar1 : function() {
if(!parametroSeguimientoAC.main.formpanel.getForm().isValid()){
    Ext.Msg.alert("Alerta","Debe ingresar los campos en rojo");
    return false;
}
	bajar.load({
		url: '{{ URL::to('reporte/poa/ac/ubicacion/exportar') }}?'+parametroSeguimientoAC.main.formpanel.getForm().getValues(true)
	});
},
onExportar2 : function() {
	bajar.load({
		url: '{{ URL::to('reporte/poa/ac/ubicacion/todo/exportar') }}'
	});
},
onLimpiar: function(){
    parametroSeguimientoAC.main.formpanel.getForm().reset();
},
getStoreCO_PERIODO:function(){
    this.store = new Ext.data.JsonStore({
        url:'{{ URL::to('auxiliar/lapso') }}',
        root:'data',
        fields:[
          {name: 'id'},
          {name: 'id_tab_ejercicio_fiscal'},
          {name: 'id_tab_periodo'},
          {name: 'fe_inicio'},
          {name: 'fe_fin'},
          {name: 'de_lapso'},
          {
              name: 'rango',
              convert: function(v, r) {
                  return r.de_lapso + ' ( ' + r.fe_inicio + ' - ' + r.fe_fin + ' )';
              }
          }
          ],
            filter: function(filters, value) {
                Ext.data.Store.prototype.filter.apply(this, [
                    filters,
                    value ? new RegExp(String.escape(value), 'i') : value
                ]);
            },
            listeners : {
                exception : function(proxy, response, operation) {
                    Ext.Msg.alert("Aviso", 'Error al obtener respuesta del servidor intente de nuevo!');
                }
            }
    });
    return this.store;
}
};
Ext.onReady(parametroSeguimientoAC.main.init, parametroSeguimientoAC.main);
</script>
<div id="parametroSeguimientoAC"></div>
