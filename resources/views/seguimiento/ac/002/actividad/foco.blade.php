<script type="text/javascript">
Ext.ns("forma002ActividadEditar");
forma002ActividadEditar.main = {
init:function(){

//<Stores de fk>
this.storeCO_FOCO_ACCION = this.getStoreCO_FOCO_ACCION();
//<Stores de fk>
//<Stores de fk>

this.OBJ = paqueteComunJS.funcion.doJSON({stringData:'{!! $data !!}'});

this.id_tab_meta_fisica = new Ext.form.Hidden({
	name:'id_tab_meta_fisica',
	value:this.OBJ.id
});

//<token>
this._token = new Ext.form.Hidden({
	name:'_token',
	value:'{{ csrf_token() }}'
});
//</token>

this.datos1 = '<p class="registro_detalle"><b>Código: </b>'+this.OBJ.cod+'</p>';
this.datos1 +='<p class="registro_detalle"><b>Actividad: </b>'+this.OBJ.nb_meta+'</p>';
this.datos1 +='<p class="registro_detalle"><b>Fecha Programada: </b>'+this.OBJ.fecha_inicio+' - '+this.OBJ.fecha_fin+'</p>';
this.datos1 +='<p class="registro_detalle"><b>Programado: </b>'+this.OBJ.tx_prog_anual+' '+this.OBJ.de_unidad_medida+'</p>';

this.fieldset1 = new Ext.form.FieldSet({
	title: 'Datos de la Actividad',
	html: this.datos1
});




this.tx_foco_accion = new Ext.form.ComboBox({
	fieldLabel:'FOCO DE ACCION',
	store: this.storeCO_FOCO_ACCION,
	typeAhead: true,
	valueField: 'tx_foco_accion',
	displayField:'tx_foco_accion',
	hiddenName:'foco_accion',
	resizable:true,
	triggerAction: 'all',
	emptyText:'Seleccione ...',
	selectOnFocus: true,
	mode: 'local',
	width:400,
	itemSelector: 'div.search-item',  
	tpl: new Ext.XTemplate('<tpl for="."><div class="search-item"><div class="desc">{tx_foco_accion}</div></div></tpl>'),
	resizable:true,
	allowBlank:false
});

this.storeCO_FOCO_ACCION.load({
params: {id_tab_ac:this.OBJ.id_tab_ac, _token:'{{ csrf_token() }}'}
        });

paqueteComunJS.funcion.seleccionarComboByCo({
	objCMB: this.tx_foco_accion,
	value:  this.OBJ.tx_foco_accion,
	objStore: this.storeCO_FOCO_ACCION
});


this.fieldset2 = new Ext.form.FieldSet({
	title: 'Datos',
	items:[
		this.tx_foco_accion
	]
});

this.enviar = new Ext.Button({
    text:'Guardar',
    iconCls: 'icon-guardar',
    handler:function(){

        if(!forma002ActividadEditar.main.formPanel_.getForm().isValid()){
            Ext.Msg.alert("Alerta","Debe ingresar los campos en rojo");
            return false;
        }

				Ext.MessageBox.confirm('Confirmación', '¿Realmente desea guardar los cambios?', function(boton){
				if(boton=="yes"){

        forma002ActividadEditar.main.formPanel_.getForm().submit({
		method:'POST',
	@if(empty($data->codigo))
		url:'{{ URL::to('ac/seguimiento/002/actividad/guardarFoco') }}',
	@else
		url:'{{ URL::to('ac/seguimiento/002/actividad/guardarFoco') }}/{!! $data->codigo !!}',
	@endif
		waitMsg: 'Enviando datos, por favor espere..',
		waitTitle:'Enviando',
            failure: function(form, action) {
		var errores = '';
		for(datos in action.result.msg){
			errores += action.result.msg[datos] + '<br>';
		}
                Ext.MessageBox.alert('Error en transacción', action.result.msg);
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
                 forma002ActividadLista.main.store_lista.reload();
                 forma002ActividadEditar.main.winformPanel_.close();
             }
        });

			}
			});

    }
});

this.salir = new Ext.Button({
    text:'Salir',
//    iconCls: 'icon-cancelar',
    handler:function(){
        forma002ActividadEditar.main.winformPanel_.close();
    }
});

this.formPanel_ = new Ext.form.FormPanel({
	//frame:true,
	width:800,
	labelWidth: 200,
	border:false,
	autoHeight:true,
	autoScroll:true,
	bodyStyle:'padding:10px;',
	items:[
		this._token,
                this.id_tab_meta_fisica,
		this.fieldset1,
		this.fieldset2
	]
});

this.winformPanel_ = new Ext.Window({
    title:'Formulario: METAS FÍSICAS - FOCO DE ACCIÓN',
    modal:true,
    constrain:true,
width:814,
    frame:true,
    closabled:true,
    autoHeight:true,
    items:[
        this.formPanel_
    ],
    buttons:[
			@if( in_array( array( 'de_privilegio' => 'acseguimiento.nuevo', 'in_habilitado' => true), Session::get('credencial') ))
				@if($data->in_bloquear_002==false)
					this.enviar,'-',
				@endif
			@endif
        this.salir
    ],
    buttonAlign:'center'
});
this.winformPanel_.show();
forma002ActividadLista.main.mascara.hide();
},
getStoreCO_FOCO_ACCION:function(){
    this.store = new Ext.data.JsonStore({
        url:'{{ URL::to('auxiliar/ac/foco') }}',
        root:'data',
        fields:[
            {name: 'tx_foco_accion'}
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
Ext.onReady(forma002ActividadEditar.main.init, forma002ActividadEditar.main);
</script>
