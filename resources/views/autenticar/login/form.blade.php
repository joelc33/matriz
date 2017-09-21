@extends('app')

@section('htmlheader_title')  Iniciar Sesion @endsection

@section('main-content')

<style type="text/css">
body {
background-color:white;
}
.x-window-mc {background-color : white !important;}
</style>

<script type="text/javascript">
Ext.QuickTips.init();
Ext.form.Field.prototype.msgTarget = 'side';

Ext.onReady(function(){

	this.captchaURL = "{{ URL::to('autenticar/captcha') }}?t=";

	function onCapthaChange(){
			var curr = Ext.get('codigoimagen');
			curr.slideOut('b', {callback: function(){
					Ext.get('codigoimagen').dom.src=this.captchaURL+new Date().getTime();
					curr.slideIn('t');
			}},this);
	};

	/*Ventana para validar*/
	function Validar(){
	if (this.validarForm.form.isValid()) {
		this.validarForm.form.submit({
			waitTitle: "Validando",
			waitMsg : "Espere un momento por favor......",
			failure: function(form,action){
			    try{
						if(action.result.msg!=null){
						    Ext.utiles.msg('Error de Validaci&oacute;n', action.result.msg);
						    onCapthaChange();
						}else{
						    throw Exception();
						}
			    }catch(Exception){
				    Ext.utiles.msg('Error durante el proceso','Consulta al administrador del Sistema');
			    }
			},
			success: function(form,action) {
					Ext.MessageBox.show({title: 'Iniciando sesi&oacute;n', msg: '<br>Por favor  Espere...',width:300,closable:false,icon:Ext.MessageBox.INFO});
			    location.href=action.result.url;
			}
		});
	}
	};

	this._token = new Ext.form.Hidden({
			name:'_token',
			value:'{{ csrf_token() }}'
	});

	this.usuario = new Ext.form.TextField({
			fieldLabel:'Usuario',
			name: 'usuario',
			id:'usuario',
			allowBlank:false,
			maxLength:250,
			width:235
	});

	this.password = new Ext.form.TextField({
			fieldLabel:'Contraseña',
			inputType:'password',
			name: 'contraseña',
			id:'password',
			allowBlank:false,
			maxLength:60,
			width:235
	});

	this.codigoseg = new Ext.form.TextField({
			autoCreate: {tag: "input", type: "text", autocomplete: "off", maxlength: 4 },
			fieldLabel:'Cod. Validacion',
			name: 'codigoseg',
			id:'codigoseg',
			allowBlank:false,
			maxLength:'4',
		  width:80
	});

	this.boxCaptcha = new Ext.BoxComponent({
			width:150,
			height:80,
			autoEl: {
					tag:'img',
					id:'codigoimagen',
					title:'Click para refrescar codigo',
					src:this.captchaURL+new Date().getTime()
		 }
	});

	this.compositefieldCódigo = new Ext.form.CompositeField({
			fieldLabel: 'Código de Seguridad',
			items: [
				this.codigoseg,
				this.boxCaptcha
			]
	});

	this.Panel = new Ext.Panel ({
			baseCls : 'x-plain',
			html    : '<b>El acceso a este lugar está restringido a los usuarios no autorizados.<br>Por favor escriba su nombre de usuario y contraseña.</b>',
			cls     : 'icon-autorizacion',
			region  : 'north',
			height  : 70
	});

	this.validarForm = new Ext.form.FormPanel({
		baseCls: 'x-plain',
		labelWidth: 180,
		autoWidth:true,
		autoHeight:true,
		frame:true,
		autoScroll:false,
		bodyStyle:'padding:10px;',
		url:'{{ URL::to('autenticar') }}',
		items: [
			{
				xtype:'box',
				anchor:'',
				autoEl:{tag:'div', style:'margin:0px 0px 8px 80px', children:[{tag:'img',src:'images/logo.png'}]}
			},
				this.Panel,
			{
				xtype:'fieldset',title:'Usuario / Contraseña', autoWidth:true, labelWidth: 90, height:170, frame:false, defaultType: 'textfield',
				items:[
					this._token,
					this.usuario,
					this.password,
					this.compositefieldCódigo
				],
				keys: [
					{
						key: [
							Ext.EventObject.ENTER
						],
						handler: function() {
							Validar();
						}
				  }
				]
		  }
		]
	});

  this.login = new Ext.Window({
							title:'Nueva Etapa - Validaci&oacute;n de Usuario',
							layout:'fit',
							iconCls: 'icon-bloqueado',
							bodyStyle:'padding:5px;',
							width:415,
						  height: 455,
							modal:true,
							autoScroll: true,
							maximizable:false,
							closable:false,
							draggable: false,
							resizable: false,
							plain: true,
							buttonAlign:'center',
							html: '<a class="blue" href="#" onclick="cambiar();">¿Olvido su contrase&ntilde;a?</a>',
							items:[
									this.validarForm
							],
							buttons: [{
							    text:'Entrar',
							    align:'center',
							    iconCls: 'icon-login',
							    handler: function (){
										Validar();
							    }
							}]
  });

	this.boxCaptcha.on('render',function (){
		var curr = Ext.get('codigoimagen');
		curr.on('click',onCapthaChange,this);
	},this);

	setTimeout(function(){
		usuario.focus(true,true);
	},500);

  this.login.show();
});

</script>

@endsection
