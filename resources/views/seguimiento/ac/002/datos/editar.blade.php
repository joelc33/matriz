<script type="text/javascript">
Ext.ns("forma002Editar");
forma002Editar.main = {
init:function(){

this.OBJ = paqueteComunJS.funcion.doJSON({stringData:'{!! $data !!}'});

//<Stores de fk>
this.storeCO_UNIDADES_MEDIDA = this.getStoreCO_UNIDADES_MEDIDA();
//<Stores de fk>

//<token>
this._token = new Ext.form.Hidden({
	name:'_token',
	value:'{{ csrf_token() }}'
});
//</token>

this.nb_actividad = new Ext.form.TextField({
	fieldLabel:'NOMBRE DE LA ACTIVIDAD',
	name:'nb_actividad',
	value:this.OBJ.nb_meta,
	width:400,
	maxLength: 250,
	allowBlank:false,
        listeners:{
            change: function(){
                this.setValue(String(this.getValue()).toUpperCase());
            }
        }
});

this.co_unidades_medida = new Ext.form.ComboBox({
	fieldLabel:'UNIDAD DE MEDIDA',
	store: this.storeCO_UNIDADES_MEDIDA,
	typeAhead: true,
	valueField: 'co_unidades_medida',
	displayField:'tx_unidades_medida',
	hiddenName:'co_unidades_medida',
	//readOnly:(this.OBJ.co_unidades_medida!='')?true:false,
	//style:(this.OBJ.co_unidades_medida!='')?'background:#c9c9c9;':'',
	forceSelection:true,
	resizable:true,
	triggerAction: 'all',
	emptyText:'Seleccione Unidades',
	selectOnFocus: true,
	mode: 'local',
	width:400,
	resizable:true,
	allowBlank:false
});

this.storeCO_UNIDADES_MEDIDA.load();
	paqueteComunJS.funcion.seleccionarComboByCo({
	objCMB: this.co_unidades_medida,
	value:  this.OBJ.co_unidades_medida,
	objStore: this.storeCO_UNIDADES_MEDIDA
});

this.pr_anual = new Ext.form.NumberField({
	fieldLabel:'META PROGRAMADA POA',
	name:'pr_anual',
	value:this.OBJ.pr_anual,
	allowBlank:false,
	width:200,
	maxLength: 8,
	emptyText: '0',
	decimalPrecision: 0,
 	minValue : 0,
 	maxValue : 99999999,
	msgTarget : 'Rango Entre 0 y 9',
	autoCreate: {tag: "input", type: "numeric", autocomplete: "off", maxlength: 8},
	allowDecimals: false,
	allowNegative: false
});

this.fecha_inicio = new Ext.form.DateField({
	fieldLabel:'FECHA DE INICIO',
	name:'fecha_inicio',
	value:this.OBJ.fecha_inicio,
	allowBlank:false,
	width:100,
});

this.fecha_culminacion = new Ext.form.DateField({
	fieldLabel:'FECHA DE CULMINACIÓN',
	name:'fecha_culminacion',
	value:this.OBJ.fecha_culminacion,
	allowBlank:false,
	width:100,
});

this.meta_modificada = new Ext.form.NumberField({
	fieldLabel:'META MODIFICADA',
	name:'pr_anual',
	value:this.OBJ.pr_anual,
	allowBlank:false,
	width:200,
	maxLength: 8,
	emptyText: '0',
	decimalPrecision: 0,
 	minValue : 0,
 	maxValue : 99999999,
	msgTarget : 'Rango Entre 0 y 9',
	autoCreate: {tag: "input", type: "numeric", autocomplete: "off", maxlength: 8},
	allowDecimals: false,
	allowNegative: false
});

this.meta_actualizada = new Ext.form.NumberField({
	fieldLabel:'META ACTUALIZADA',
	name:'pr_anual',
	value:this.OBJ.pr_anual,
	allowBlank:false,
	width:200,
	maxLength: 8,
	emptyText: '0',
	decimalPrecision: 0,
 	minValue : 0,
 	maxValue : 99999999,
	msgTarget : 'Rango Entre 0 y 9',
	autoCreate: {tag: "input", type: "numeric", autocomplete: "off", maxlength: 8},
	allowDecimals: false,
	allowNegative: false
});

this.obtenido_corte = new Ext.form.NumberField({
	fieldLabel:'OBTENIDO AL CORTE',
	name:'pr_anual',
	value:this.OBJ.pr_anual,
	allowBlank:false,
	width:200,
	maxLength: 8,
	emptyText: '0',
	decimalPrecision: 0,
 	minValue : 0,
 	maxValue : 99999999,
	msgTarget : 'Rango Entre 0 y 9',
	autoCreate: {tag: "input", type: "numeric", autocomplete: "off", maxlength: 8},
	allowDecimals: false,
	allowNegative: false
});

this.obtenido = new Ext.form.NumberField({
	fieldLabel:'% EJEC. OBTENIDA AL CORTE Vs. EJEC. PROG. ANUAL',
	name:'pr_anual',
	value:this.OBJ.pr_anual,
	allowBlank:false,
	width:200,
	maxLength: 8,
	emptyText: '0',
	decimalPrecision: 0,
 	minValue : 0,
 	maxValue : 99999999,
	msgTarget : 'Rango Entre 0 y 9',
	autoCreate: {tag: "input", type: "numeric", autocomplete: "off", maxlength: 8},
	allowDecimals: false,
	allowNegative: false
});

this.comFechaInCul = new Ext.form.CompositeField({
fieldLabel: 'FECHA DE INICIO',
items: [
	this.fecha_inicio,
             {
                   xtype: 'displayfield',
                   value: '&nbsp;&nbsp;&nbsp; FECHA DE CULMINACIÓN:',
                   width: 190
             },
	this.fecha_culminacion
	]
});

this.localizacion = new Ext.form.TextField({
	fieldLabel:'LOCALIZACIÓN',
	name:'nb_responsable',
	value:this.OBJ.nb_responsables,
	width:400,
	maxLength: 250,
	allowBlank:false,
        listeners:{
            change: function(){
                this.setValue(String(this.getValue()).toUpperCase());
            }
        }
});

this.nb_responsable = new Ext.form.TextField({
	fieldLabel:'RESPONSABLE',
	name:'nb_responsable',
	value:this.OBJ.nb_responsable,
	width:400,
	maxLength: 250,
	allowBlank:false,
        listeners:{
            change: function(){
                this.setValue(String(this.getValue()).toUpperCase());
            }
        }
});

this.guardar = new Ext.Button({
    text:'Guardar',
    iconCls: 'icon-guardar',
    handler:function(){

        if(!forma002Editar.main.formPanel_.getForm().isValid()){
            Ext.Msg.alert("Alerta","Debe ingresar los campos en rojo");
            return false;
        }
        forma002Editar.main.formPanel_.getForm().submit({
		method:'POST',
	@if(empty($data->id))
		url:'{{ URL::to('mantenimiento/aplicacion/guardar') }}',
	@else
		url:'{{ URL::to('mantenimiento/aplicacion/guardar') }}/{!! $data->id !!}',
	@endif
		waitMsg: 'Enviando datos, por favor espere..',
		waitTitle:'Enviando',
            failure: function(form, action) {
		var errores = '';
		for(datos in action.result.msg){
			errores += action.result.msg[datos] + '<br>';
		}
                Ext.MessageBox.alert('Error en transacción', errores);
            },
            success: function(form, action) {
                 if(action.result.success){
                     Ext.MessageBox.show({
                         title: 'Mensaje',
                         msg: action.result.msg,
                         closable: false,
                         icon: Ext.MessageBox.INFO,
                         resizable: false,
			 animEl: document.body,
                         buttons: Ext.MessageBox.OK
                     });
                 }
                 forma001Lista.main.store_lista.load();
                 forma002Editar.main.winformPanel_.close();
             }
        });


    }
});

this.salir = new Ext.Button({
    text:'Salir',
//    iconCls: 'icon-cancelar',
    handler:function(){
        forma002Editar.main.winformPanel_.close();
    }
});

this.formPanel_ = new Ext.form.FormPanel({
	//frame:true,
	width:600,
	labelWidth: 120,
	border:false,
	autoHeight:true,
	autoScroll:true,
	bodyStyle:'padding:10px;',
	items:[
		this._token,
		this.nb_actividad,
		this.co_unidades_medida,
		this.pr_anual,
		this.comFechaInCul,
		this.meta_modificada,
		this.meta_actualizada,
		this.obtenido_corte,
		this.obtenido,
		this.localizacion,
		this.nb_responsable
	]
});

this.winformPanel_ = new Ext.Window({
    title:'Formulario: METAS FÍSICAS',
    modal:true,
    constrain:true,
width:614,
    frame:true,
    closabled:true,
    autoHeight:true,
    items:[
        this.formPanel_
    ],
    buttons:[
			@if( in_array( array( 'de_privilegio' => 'aplicacion.guardar', 'in_habilitado' => true), Session::get('credencial') ))
				this.guardar,
			@endif
        this.salir
    ],
    buttonAlign:'center'
});
this.winformPanel_.show();
forma001Lista.main.mascara.hide();
},
getStoreCO_UNIDADES_MEDIDA:function(){
    this.store = new Ext.data.JsonStore({
        url:'formulacion/modulos/proyecto/funcion.php?op=16',
        root:'data',
        fields:[
            {name: 'co_unidades_medida'},{name: 'tx_unidades_medida'}
            ]
    });
    return this.store;
}
};
Ext.onReady(forma002Editar.main.init, forma002Editar.main);
</script>
