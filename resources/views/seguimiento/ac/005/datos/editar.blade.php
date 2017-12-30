<script type="text/javascript">
Ext.ns("forma005Editar");
forma005Editar.main = {
init:function(){

this.OBJ = paqueteComunJS.funcion.doJSON({stringData:'{!! $data !!}'});

//<token>
this._token = new Ext.form.Hidden({
	name:'_token',
	value:'{{ csrf_token() }}'
});
//</token>

this.de_programado_anual = new Ext.form.TextArea({
	fieldLabel: 'PRODUCTO PROGRAMADO ANUAL DEL OBJETIVO INSTITUCIONAL',
	name: 'programado_anual',
	value:this.OBJ.de_programado_anual,
	allowBlank: false,
	width:400,
	height: 100,
	maxLength: 3000
});

this.tp_indicador = new Ext.form.TextField({
	fieldLabel:'INDICADORES DE GESTIÓN (EFICIENCIA, EFICACIA, EFECTIVIDAD)',
	name:'tipo_indicador',
	value:this.OBJ.tp_indicador,
	width:400,
	maxLength: 600,
	allowBlank:false
});

this.nb_indicador = new Ext.form.TextField({
	fieldLabel:'NOMBRE DEL INDICADOR',
	name:'nombre_indicador',
	value:this.OBJ.nb_indicador,
	width:400,
	maxLength: 600,
	allowBlank:false
});

this.valor_objetivo = new Ext.form.TextField({
	fieldLabel:'VALOR OBJETIVO',
	name:'valor_objetivo',
	value:this.OBJ.valor_objetivo,
	width:400,
	maxLength: 600,
	allowBlank:false
});

this.valor_obtenido = new Ext.form.TextField({
	fieldLabel:'VALOR OBTENIDO',
	name:'valor_obtenido',
	value:this.OBJ.valor_obtenido,
	width:400,
	maxLength: 600,
	allowBlank:false
});

this.nu_cumplimiento = new Ext.form.NumberField({
	fieldLabel:'CUMPLIMIENTO %',
	name:'cumplimiento',
	value:this.OBJ.nu_cumplimiento,
	allowBlank:false,
	width:100,
	minLength : 0,
	maxLength: 18,
	autoCreate: {tag: "input", type: "numeric", autocomplete: "off", maxlength: 18},
});

this.de_indicador = new Ext.form.TextField({
	fieldLabel:'DESCRIPCIÓN DEL INDICADOR',
	name:'indicador',
	value:this.OBJ.de_indicador,
	width:400,
	maxLength: 600,
	allowBlank:false
});

this.de_formula = new Ext.form.TextField({
	fieldLabel:'FÓRMULA',
	name:'formula',
	value:this.OBJ.de_formula,
	width:400,
	maxLength: 600,
	allowBlank:false
});

this.guardar = new Ext.Button({
    text:'Guardar',
    iconCls: 'icon-guardar',
    handler:function(){

        if(!forma005Editar.main.formPanel_.getForm().isValid()){
            Ext.Msg.alert("Alerta","Debe ingresar los campos en rojo");
            return false;
        }
        forma005Editar.main.formPanel_.getForm().submit({
		method:'POST',
	@if(empty($data->id))
		url:'{{ URL::to('ac/seguimiento/005/guardar') }}',
	@else
		url:'{{ URL::to('ac/seguimiento/005/guardar') }}/{!! $data->id !!}',
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
                 forma005Lista.main.store_lista.load();
                 forma005Editar.main.winformPanel_.close();
             }
        });


    }
});

this.salir = new Ext.Button({
    text:'Salir',
//    iconCls: 'icon-cancelar',
    handler:function(){
        forma005Editar.main.winformPanel_.close();
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
		this.de_programado_anual,
		this.tp_indicador,
		this.nb_indicador,
		this.valor_objetivo,
		this.valor_obtenido,
		this.nu_cumplimiento,
		this.de_indicador,
		this.de_formula
	]
});

this.winformPanel_ = new Ext.Window({
    title:'F005: INDICADORES DE GESTIÓN',
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
			@if( in_array( array( 'de_privilegio' => 'aplicacion.guardar', 'in_habilitado' => true), Session::get('credencial') ))
				this.guardar,
			@endif
        this.salir
    ],
    buttonAlign:'center'
});
this.winformPanel_.show();
forma005Lista.main.mascara.hide();
}
};
Ext.onReady(forma005Editar.main.init, forma005Editar.main);
</script>
