<script type="text/javascript">
Ext.ns('parametroUbicacionPR');
parametroUbicacionPR.main = {
init: function(){

this.OBJ = paqueteComunJS.funcion.doJSON({stringData:'{!! $data !!}'});

//<Stores de fk>
this.storeCO_MUNICIPIO = this.getStoreCO_MUNICIPIO();
//<Stores de fk>
//<Stores de fk>
this.storeCO_PARROQUIA = this.getStoreCO_PARROQUIA();
//<Stores de fk>

<?php $rol_planificador = array( 3, 8); ?>

this.co_municipio = new Ext.form.ComboBox({
	fieldLabel:'MUNICIPIO',
	store: this.storeCO_MUNICIPIO,
	typeAhead: true,
	valueField: 'id',
	displayField:'de_municipio',
	hiddenName:'id_tab_municipio',
	//readOnly:(this.OBJ.co_municipio!='')?true:false,
	//style:(this.OBJ.co_municipio!='')?'background:#c9c9c9;':'',
	forceSelection:true,
	resizable:true,
	triggerAction: 'all',
	emptyText:'Seleccione Municipio',
	selectOnFocus: true,
	mode: 'local',
	width:300,
	resizable:true,
	allowBlank:false,
	listeners:{
            change: function(){
                parametroUbicacionPR.main.storeCO_PARROQUIA.load({
                    params: {id_tab_municipio:this.getValue(), _token: '{{ csrf_token() }}'}
                })
            }
        }
});

this.storeCO_MUNICIPIO.load();
	paqueteComunJS.funcion.seleccionarComboByCo({
	objCMB: this.co_municipio,
	value:  this.OBJ.co_municipio,
	objStore: this.storeCO_MUNICIPIO
});

this.co_municipio.on('beforeselect',function(cmb,record,index){
        	this.co_parroquia.clearValue();
},this);

this.co_parroquia = new Ext.form.ComboBox({
	fieldLabel:'PARROQUIA',
	store: this.storeCO_PARROQUIA,
	typeAhead: true,
	valueField: 'id',
	displayField:'de_parroquia',
	hiddenName:'id_tab_parroquia',
	//readOnly:(this.OBJ.co_parroquia!='')?true:false,
	//style:(this.OBJ.co_parroquia!='')?'background:#c9c9c9;':'',
	forceSelection:true,
	resizable:true,
	triggerAction: 'all',
	emptyText:'Seleccione Parroquia',
	selectOnFocus: true,
	mode: 'local',
	width:300,
	resizable:true,
	allowBlank:false
});

this.fieldset1 = new Ext.form.FieldSet({
    title: 'Seleccione Parametros',
    items:[
			this.co_municipio
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
							@if( in_array( array( 'de_privilegio' => 'ubicacion.proyecto.municipio', 'in_habilitado' => true), Session::get('credencial') ))
								{
									text:'REPORTE por Municipio',  // Generar la impresión en pdf
									iconCls:'icon-pdf',
									handler: this.onImprimir
								},
							@endif
							@if( in_array( array( 'de_privilegio' => 'ubicacion.proyecto.todos', 'in_habilitado' => true), Session::get('credencial') ))
								{
									text:'REPORTE Todos',  // Generar la impresión en pdf
									iconCls:'icon-pdf',
									handler: this.onImprimir1
								},
							@endif
							@if( in_array( array( 'de_privilegio' => 'ubicacion.proyecto.exportar.municipio', 'in_habilitado' => true), Session::get('credencial') ))
								{
									text:'Exportar por Municipio',  // Generar la impresión en pdf
									iconCls:'icon-excel',
									handler: this.onExportar1
								},
							@endif
							@if( in_array( array( 'de_privilegio' => 'ubicacion.proyecto.exportar.todo', 'in_habilitado' => true), Session::get('credencial') ))
								{
									text:'Exportar Todos',  // Generar la impresión en pdf
									iconCls:'icon-excel',
									handler: this.onExportar2
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
	labelWidth: 160,
	iconCls:'icon-reporteest',
	title: 'UBICACION - PROYECTOS',
	items:[
		this.botones
	]
});

this.formpanel.render('parametroUbicacionPR');
},
onImprimir : function() {
if(!parametroUbicacionPR.main.formpanel.getForm().isValid()){
    Ext.Msg.alert("Alerta","Debe ingresar los campos en rojo");
    return false;
}
   //window.open('formulacion/modulos/reportes/proyResumen.php?'+parametroUbicacionPR.main.formpanel.getForm().getValues(true));
	bajar.load({
		url: 'formulacion/modulos/reportes/ormPDF.php/reporte/ubicacion?'+parametroUbicacionPR.main.formpanel.getForm().getValues(true)
	});
},
onImprimir1 : function() {
   //window.open('formulacion/modulos/reportes/proyResumen.php');
	bajar.load({
		url: 'formulacion/modulos/reportes/ormPDF.php/reporte/ubicacion/todo'
	});
},
onExportar1 : function() {
if(!parametroUbicacionPR.main.formpanel.getForm().isValid()){
    Ext.Msg.alert("Alerta","Debe ingresar los campos en rojo");
    return false;
}
	bajar.load({
		url: 'formulacion/modulos/reportes/orm.php/exportar/ubicacion?'+parametroUbicacionPR.main.formpanel.getForm().getValues(true)
	});
},
onExportar2 : function() {
   //window.open('formulacion/modulos/reportes/proyResumen.php');
	bajar.load({
		url: 'formulacion/modulos/reportes/orm.php/exportar/ubicacion/todo'
	});
},
onLimpiar: function(){
    parametroUbicacionPR.main.formpanel.getForm().reset();
},
getStoreCO_MUNICIPIO:function(){
    this.store = new Ext.data.JsonStore({
        url:'{{ URL::to('auxiliar/municipio/todo') }}',
        root:'data',
        fields:[
						{name: 'id'},{name: 'de_municipio'}
            ],
            listeners : {
                exception : function(proxy, response, operation) {
                    Ext.Msg.alert("Aviso", 'Error al obtener respuesta del servidor intente de nuevo!');
                }
            }
    });
    return this.store;
},
getStoreCO_PARROQUIA:function(){
    this.store = new Ext.data.JsonStore({
        url:'{{ URL::to('auxiliar/parroquia/todo') }}',
        root:'data',
        fields:[
						{name: 'id'},{name: 'de_parroquia'}
            ],
            listeners : {
                exception : function(proxy, response, operation) {
                    Ext.Msg.alert("Aviso", 'Error al obtener respuesta del servidor intente de nuevo!');
                }
            }
    });
    return this.store;
}
};
Ext.onReady(parametroUbicacionPR.main.init, parametroUbicacionPR.main);
</script>
<div id="parametroUbicacionPR"></div>
