<script type="text/javascript">
Ext.ns("forma005Nuevo");
forma005Nuevo.main = {
init:function(){

this.OBJ = paqueteComunJS.funcion.doJSON({stringData:'{!! $data !!}'});

//<Stores de fk>
this.storeCO_TIPO_INDICADOR = this.getStoreCO_TIPO_INDICADOR();
//<Stores de fk>
this.storeCO_SUB_TIPO_INDICADOR = this.getStoreCO_SUB_TIPO_INDICADOR();
//<Stores de fk>
this.storeCO_MUNICIPIO = this.getStoreCO_MUNICIPIO();
//<Stores de fk>
this.storeCO_PARROQUIA= this.getStoreCO_PARROQUIA();

this.storeCO_COMUNA= this.getStoreCO_COMUNA();

this.storeCO_UNIDADES_MEDIDA = this.getStoreCO_UNIDADES_MEDIDA();
//<token>
this._token = new Ext.form.Hidden({
	name:'_token',
	value:'{{ csrf_token() }}'
});

this.id_tab_meta_fisica = new Ext.form.Hidden({
	name:'id_tab_meta_fisica',
	value:'{{ $data->id }}'
});

this.in_territorial = new Ext.form.Hidden({
	name:'id_territorial'
});

//</token>

this.id_tab_tipo_indicador = new Ext.form.ComboBox({
	fieldLabel:'TIPO DE INDICADOR',
	store: this.storeCO_TIPO_INDICADOR,
	typeAhead: true,
	valueField: 'id',
	displayField:'de_tipo_indicador',
	hiddenName:'tipo_inidcador',
	resizable:true,
	triggerAction: 'all',
	emptyText:'Seleccione tipo de inidcador...',
	selectOnFocus: true,
	mode: 'local',
	width:400,
	itemSelector: 'div.search-item',    
	tpl: new Ext.XTemplate('<tpl for="."><div class="search-item"><div class="desc">{de_tipo_indicador}</div></div></tpl>'),
	resizable:true,
	allowBlank:false,
	listeners:{
        change: function(){
        forma005Nuevo.main.storeCO_SUB_TIPO_INDICADOR.load({
                        params: {id_tab_tipo_indicador:this.getValue(), _token:'{{ csrf_token() }}'}
        })
        }
	}
});

this.storeCO_TIPO_INDICADOR.load();

paqueteComunJS.funcion.seleccionarComboByCo({
	objCMB: this.id_tab_tipo_indicador,
	value:  this.OBJ.id_tab_tipo_indicador,
	objStore: this.storeCO_TIPO_INDICADOR
});

this.id_tab_tipo_indicador.on('beforeselect',function(cmb,record,index){

        	this.id_tab_sub_tipo_indicador.clearValue();
},this);


this.id_tab_sub_tipo_indicador= new Ext.form.ComboBox({
	fieldLabel:'SUB TIPO INDICADOR',
	store: this.storeCO_SUB_TIPO_INDICADOR,
	typeAhead: true,
	valueField: 'id',
	displayField:'de_sub_tipo_indicador',
	hiddenName:'sub_tipo_inidcador',
	forceSelection:true,
	resizable:true,
	triggerAction: 'all',
	emptyText:'Seleccione sub tipo indicador...',
	selectOnFocus: true,
	mode: 'local',
	width:400,
	resizable:true,     
	allowBlank:false
});


this.id_tab_sub_tipo_indicador.on('beforeselect',function(cmb,record,index){

                this.id_tab_municipio_detalle.clearValue();
                this.id_tab_parroquia_detalle.clearValue();
                this.id_tab_comuna.clearValue();
                forma005Nuevo.main.in_territorial.setValue(record.data.in_territorial);
                if(record.data.in_territorial==true){
                forma005Nuevo.main.id_tab_municipio_detalle.show();
                forma005Nuevo.main.id_tab_parroquia_detalle.show();
                forma005Nuevo.main.id_tab_comuna.show();
                }else{
                forma005Nuevo.main.id_tab_municipio_detalle.hide();
                forma005Nuevo.main.id_tab_parroquia_detalle.hide();
                forma005Nuevo.main.id_tab_comuna.hide();
                }
},this);


this.cantidad = new Ext.form.NumberField({
	fieldLabel:'CANTIDAD',
	name:'cantidad',
	width:400,
	allowBlank:false
});

this.id_tab_municipio_detalle = new Ext.form.ComboBox({
	fieldLabel:'MUNICIPIO',
	store: this.storeCO_MUNICIPIO,
	typeAhead: true,
	valueField: 'id',
	displayField:'de_municipio',
	hiddenName:'municipio',
	resizable:true,
	triggerAction: 'all',
	emptyText:'Seleccione ...',
	selectOnFocus: true,
	mode: 'local',
	width:400,
	itemSelector: 'div.search-item', 
	tpl: new Ext.XTemplate('<tpl for="."><div class="search-item"><div class="desc">{de_municipio}</div></div></tpl>'),
	resizable:true,
	listeners:{
        change: function(){
        forma005Nuevo.main.storeCO_PARROQUIA.load({
                        params: {id_tab_municipio:this.getValue(), _token:'{{ csrf_token() }}'}
        });
        
        }
	}
});

this.storeCO_MUNICIPIO.load();
this.id_tab_municipio_detalle.hide();
this.id_tab_municipio_detalle.on('beforeselect',function(cmb,record,index){
        	this.id_tab_parroquia_detalle.clearValue();
},this);

this.id_tab_parroquia_detalle = new Ext.form.ComboBox({
	fieldLabel:'PARROQUIA',
	store: this.storeCO_PARROQUIA,
	typeAhead: true,
	valueField: 'id',
	displayField:'de_parroquia',
	hiddenName:'parroquia',
	forceSelection:true,
	resizable:true,
	triggerAction: 'all',
	emptyText:'Seleccione Parroquia',
	selectOnFocus: true,
	mode: 'local',
	width:400,
	resizable:true,
	listeners:{
        change: function(){
        forma005Nuevo.main.storeCO_COMUNA.load({
                        params: {id_tab_parroquia_detalle:this.getValue(), _token:'{{ csrf_token() }}'}
        });
        
        }
	}        
});
this.id_tab_parroquia_detalle.hide();

this.id_tab_parroquia_detalle.on('beforeselect',function(cmb,record,index){
                this.id_tab_comuna.clearValue();
},this);

this.id_tab_comuna = new Ext.form.ComboBox({
	fieldLabel:'COMUNA',
	store: this.storeCO_COMUNA,
	typeAhead: true,
	valueField: 'id',
	displayField:'de_comuna',
	hiddenName:'comuna',
	forceSelection:true,
	resizable:true,
	triggerAction: 'all',
	emptyText:'Seleccione ...',
	selectOnFocus: true,
	mode: 'local',
	width:400,
	resizable:true
});
this.id_tab_comuna.hide();

this.id_tab_unidad_medida = new Ext.form.ComboBox({
	fieldLabel:'UNIDAD DE MEDIDA',
	store: this.storeCO_UNIDADES_MEDIDA,
	typeAhead: true,
	valueField: 'id',
	displayField:'de_unidad_medida',
	hiddenName:'unidad_medida',
	forceSelection:true,
	resizable:true,
	triggerAction: 'all',
	emptyText:'Seleccione Unidad',
	selectOnFocus: true,
	mode: 'local',
	width:400,
	resizable:true,
	allowBlank:false
});
this.storeCO_UNIDADES_MEDIDA.load();

this.guardar = new Ext.Button({
    text:'Guardar',
    iconCls: 'icon-guardar',
    handler:function(){

        if(forma005Nuevo.main.in_territorial.getValue()==true){
        
        if(forma005Nuevo.main.id_tab_municipio_detalle.getValue()==''){
            
            Ext.Msg.alert("Alerta","Debe ingresar el municipio");
            return false;
            
        }
 
        if(forma005Nuevo.main.id_tab_parroquia_detalle.getValue()==''){
            
            Ext.Msg.alert("Alerta","Debe ingresar la parroquia");
            return false;
            
        } 
        
        if(forma005Nuevo.main.id_tab_comuna.getValue()==''){
            
            Ext.Msg.alert("Alerta","Debe ingresar la comuna");
            return false;
            
        }         
        
        }

        if(!forma005Nuevo.main.formPanel_.getForm().isValid()){
            Ext.Msg.alert("Alerta","Debe ingresar los campos en rojo");
            return false;
        }
        
        
        
        Ext.MessageBox.confirm('Confirmación', '¿Realmente desea enviar los datos?', function(boton){
				if(boton=="yes"){

        forma005Nuevo.main.formPanel_.getForm().submit({
						method:'POST',
						url:'{{ URL::to('ac/seguimiento/005/enviarIndicador') }}',
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
                 forma005ActividadEditar.main.store_lista.load();
                 forma005Nuevo.main.winformPanel_.close();
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
        forma005Nuevo.main.winformPanel_.close();
    }
});

this.formPanel_ = new Ext.form.FormPanel({
	//frame:true,
	width:620,
	labelWidth: 180,
	border:false,
	autoHeight:true,
	autoScroll:true,
	bodyStyle:'padding:10px;',
	items:[
		this._token,
                this.id_tab_meta_fisica,
		this.id_tab_tipo_indicador,
                this.id_tab_sub_tipo_indicador,
                this.id_tab_municipio_detalle,
                this.id_tab_parroquia_detalle,
                this.id_tab_comuna,
                this.id_tab_unidad_medida,
                this.cantidad
	]
});

this.winformPanel_ = new Ext.Window({
    title:'Actividad: NUEVO INDICADOR DE GESTIÓN',
    modal:true,
    constrain:true,
		width:634,
    frame:true,
    closabled:true,
    autoHeight:true,
    items:[
        this.formPanel_
    ],
    buttons:[
			@if( in_array( array( 'de_privilegio' => 'acseguimiento.nuevo', 'in_habilitado' => true), Session::get('credencial') ))
			this.guardar,'-',
			@endif
        this.salir
    ],
    buttonAlign:'center'
});
this.winformPanel_.show();
forma005ActividadEditar.main.mascara.hide();
},
getStoreCO_TIPO_INDICADOR:function(){
    this.store = new Ext.data.JsonStore({
        url:'{{ URL::to('auxiliar/tipoindicador') }}',
        root:'data',
        fields:[
            {name: 'id'},{name: 'de_tipo_indicador'}
            ],
            listeners : {
                exception : function(proxy, response, operation) {
                    Ext.Msg.alert("Aviso", 'Error al obtener respuesta del servidor intente de nuevo!');
                }
            }
    });
    return this.store;
},
getStoreCO_SUB_TIPO_INDICADOR:function(){
    this.store = new Ext.data.JsonStore({
        url:'{{ URL::to('auxiliar/subtipoindicador') }}',
        root:'data',
        fields:[
            {name: 'id'},{name: 'de_sub_tipo_indicador'},{name: 'in_territorial'}
            ],
            listeners : {
                exception : function(proxy, response, operation) {
                    Ext.Msg.alert("Aviso", 'Error al obtener respuesta del servidor intente de nuevo!');
                }
            }
    });
    return this.store;
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
},
getStoreCO_COMUNA:function(){
    this.store = new Ext.data.JsonStore({
        url:'{{ URL::to('auxiliar/comuna/todo') }}',
        root:'data',
        fields:[
            {name: 'id'},{name: 'de_comuna'}
            ],
            listeners : {
                exception : function(proxy, response, operation) {
                    Ext.Msg.alert("Aviso", 'Error al obtener respuesta del servidor intente de nuevo!');
                }
            }
    });
    return this.store;
},
getStoreCO_UNIDADES_MEDIDA:function(){
    this.store = new Ext.data.JsonStore({
        url:'{{ URL::to('auxiliar/unidadmedida') }}',
        root:'data',
        fields:[
            {name: 'id'},{name: 'de_unidad_medida'}
            ]
    });
    return this.store;
}
};
Ext.onReady(forma005Nuevo.main.init, forma005Nuevo.main);
</script>
