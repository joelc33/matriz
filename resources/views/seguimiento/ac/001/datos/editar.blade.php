<script type="text/javascript">
Ext.ns("forma001Editar");
forma001Editar.main = {
init:function(){

this.OBJ = paqueteComunJS.funcion.doJSON({stringData:'{!! $data !!}'});

//<token>
this._token = new Ext.form.Hidden({
	name:'_token',
	value:'{{ csrf_token() }}'
});
//</token>

this.inst_mision = new Ext.form.TextArea({
	fieldLabel: '1.4.1. MISION',
	name: 'mision',
	value:this.OBJ.inst_mision,
	allowBlank: false,
	width:400,
	height: 100,
	maxLength: 3000
});

this.inst_vision = new Ext.form.TextArea({
	fieldLabel: '1.4.2. VISION',
	name: 'vision',
	value:this.OBJ.inst_vision,
	allowBlank: false,
	width:400,
	height: 100,
	maxLength: 3000
});

this.inst_objetivos = new Ext.form.TextArea({
	fieldLabel: '1.4.3. OBJETIVOS DE LA INSTITUCION',
	name: 'objetivos',
	value:this.OBJ.inst_objetivos,
	allowBlank: false,
	width:400,
	height: 200,
	maxLength: 3000
});

this.guardar = new Ext.Button({
    text:'Guardar',
    iconCls: 'icon-guardar',
    handler:function(){

        if(!forma001Editar.main.formPanel_.getForm().isValid()){
            Ext.Msg.alert("Alerta","Debe ingresar los campos en rojo");
            return false;
        }
        forma001Editar.main.formPanel_.getForm().submit({
		method:'POST',
	@if(empty($data->id))
		url:'{{ URL::to('ac/seguimiento/001/guardar') }}',
	@else
		url:'{{ URL::to('ac/seguimiento/001/guardar') }}/{!! $data->id !!}',
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
                 forma001Editar.main.winformPanel_.close();
             }
        });


    }
});

this.enviar = new Ext.Button({
    text:'Enviar Cambios',
    iconCls: 'icon-report',
    handler:function(){

        if(!forma001Editar.main.formPanel_.getForm().isValid()){
            Ext.Msg.alert("Alerta","Debe ingresar los campos en rojo");
            return false;
        }
        forma001Editar.main.formPanel_.getForm().submit({
		method:'POST',
	@if(empty($data->id))
		url:'{{ URL::to('ac/seguimiento/001/enviar') }}',
	@else
		url:'{{ URL::to('ac/seguimiento/001/enviar') }}/{!! $data->id !!}',
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
                 forma001Editar.main.winformPanel_.close();
             }
        });


    }
});

this.salir = new Ext.Button({
    text:'Salir',
//    iconCls: 'icon-cancelar',
    handler:function(){
        forma001Editar.main.winformPanel_.close();
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
		this.inst_mision,
		this.inst_vision,
		this.inst_objetivos
	]
});

this.winformPanel_ = new Ext.Window({
    title:'F001: MARCO NORMATIVO INSTITUCIONAL',
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
			@if( in_array( array( 'de_privilegio' => 'acseguimiento.001.enviar', 'in_habilitado' => true), Session::get('credencial') ))
				this.enviar,'-',
			@endif
			@if( in_array( array( 'de_privilegio' => 'acseguimiento.001.guardar', 'in_habilitado' => true), Session::get('credencial') ))
				this.guardar,'-',
			@endif
        this.salir
    ],
    buttonAlign:'center'
});
this.winformPanel_.show();
forma001Lista.main.mascara.hide();
}
};
Ext.onReady(forma001Editar.main.init, forma001Editar.main);
</script>
