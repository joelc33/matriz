@extends('home')

@section('htmlheader_title') @endsection

@section('main-content')

<script type="text/javascript">
(function(){
    var mensaje = {
        title: 'Error',
        msg: 'Su sesión ha expirado. Debe volver a identificarse.',
        buttons: Ext.Msg.OK,
        icon: Ext.MessageBox.ERROR,
        fn: function(){
            document.location.href = 'autenticar';
        }
    };
    Ext.Ajax.on('requestcomplete', function(conn, resp){
        var head, rHead;

        head = "<!DOCTYPE html PUBLIC";
        rHead = resp.responseText.substring(0, head.length);
        if ( rHead == head ) {
            Ext.Msg.show(mensaje);
        }
    });
    Ext.Ajax.on('requestexception', function(conn, resp){
        if ( resp.status === 403 ) {
            Ext.Msg.show(mensaje);
        }
    });
}());

Ext.QuickTips.init();
Ext.form.Field.prototype.msgTarget = 'side';
this.panel_detalle =  new Ext.Panel({
        region: 'east',
        title: 'Detalles del Registro',
        id: 'detalle_registro',
        collapsible: true,
        collapseMode: 'mini',
        collapsed:true,
        split: true,
        autoScroll: true,
        titleCollapse: true,
        deferredRender: false,
        width:350,
	margins: '0 0 0 0',
        script:true,
	iconCls: 'icon-reporteest',
        items:[
		new Ext.Panel({
			id: 'detalle'
		})
        ]
});

this.comboTemas = new Ext.ux.ThemeCombo({
	width:100
});

this.tabpanel = new Ext.TabPanel({
	region: 'center',
	deferredRender: false,
	id: 'tabpanel',
	border:true,
	autoScroll: false,
	enableTabScroll:true,
	activeItem:0,
	listeners: {'tabchange': function(tabPanel, tab){panel_detalle.collapse();}},
	items:[{
		id: 'tabPrincipal',
		border:false,
		title: '{{ $bandeja->de_bandeja }}',
                autoScroll:true,
		iconCls:'icon-inicio',
		contentEl:'centro',
	        layout:'fit',
		padding: 0,
		autoLoad: {url: '{{ $bandeja->de_url_bandeja }}', scripts: true, scope: this}
	}]
});
Ext.onReady(function(){
this.datosUsuario = '<p class="registro_detalle"><b><span style="color:red;font-size:13px;">Bienvenido, {!! $funcionario->nb_funcionario !!} {!! $funcionario->ap_funcionario !!} </span></b></p>';
this.datosUsuario += '<p class="registro_detalle"><b>Fecha de Registro: </b>{!! trim(date_format(date_create($funcionario->fe_registro),'d/m/Y')) !!}</p>';
this.datosUsuario += '<p class="registro_detalle"><b>Cédula: </b>{!! $funcionario->inicial !!}-{!! $funcionario->nu_cedula !!}</p>';
this.datosUsuario +='<p class="registro_detalle"><b>Nombre: </b> {!! $funcionario->nb_funcionario !!} {!! $funcionario->ap_funcionario !!}</p>';
this.datosUsuario +='<p class="registro_detalle"><b>Unidad Ejecutora: </b> {!! $funcionario->tx_ejecutor !!}</p>';
this.datosUsuario +='<p class="registro_detalle"><b>Ejercicio Fiscal: </b> {!! Session::get('ejercicio') !!}</p>';
this.datosUsuario += '<p class="registro_detalle"><b>Ultimo login: </b>{!! trim(date_format(date_create($ultimo_login->created_at),"d/m/Y - h:i A")) !!}</p>';

this.btnSalir = new Ext.Button({
	text: 'Cerrar sesi&oacute;n',
	handler: logOut,
	iconCls:'icon-salir2'
});

this.reloj = new Ext.Toolbar.TextItem('');
/*correr reloj*/
Ext.TaskMgr.start({run: function(){Ext.fly(reloj.getEl()).update(new Date().format('g:i:s A'));},interval: 1000});
/*descargador*/
this.bajar = new CMS.view.FileDownload();
/*barra de estatus*/
/*this.estatusbar = new Ext.Toolbar({items:[this.reloj,'-',this.btnSalir]});*/
var viewport = new Ext.Viewport({
layout: 'fit',
items: [{
	layout: 'border',
	items: [
		    /*create instance immediately*/
		    new Ext.BoxComponent({
		        region: 'north',
		        height: 60, /*give north and south regions a height*/
		 	contentEl:'header'
		    }),{
			region: 'south',
			split:true,
			layout:'fit',
			maxSize: 10,
			border:false,
			bbar : [
				this.reloj,'-',this.btnSalir,'->',{xtype: 'tbtext', text: '<span style="color:red;"><b>Sistema Nueva Etapa y Matriz de Seguimiento - Secretaría de Planificación y Estadística - Gobierno Bolivariano del Zulia.</b></span>'}
			]
               		},{
		        region: 'west',
		        id: 'navegador', /*see Ext.getCmp() below*/
		        title: '.::NUEVA ETAPA::.',
			iconCls: 'icon-navegacion',
		        split: true,
		        width: 240,
		        minSize: 200,
		        maxSize: 600,
			autoScroll:true,
		        collapsible: true,
			animCollapse: true,
			collapsedTitle: true,
		        margins: '0 0 0 0',
			/*bbar: this.estatusbar,*/
			bodyStyle: "background-image:url('{{ asset('/images/logotipo.png') }}');background-repeat: no-repeat;    background-attachment: fixed; background-position: 4.5% 85%; background-size: 120px 230px; !important;",
			layout: 'accordion',
			layoutConfig: {
				animate: true
			},
		        items: [
				{
				title:'<b>Mi Cuenta</b>',
				autoScroll:true,
				border:false,
				collapsed:false,
				iconCls:'icon-usuario',
				autoHeight:true,
				html: miCuenta(this.datosUsuario)
				},
				{!! $menu !!}
				]
		    },
		    this.tabpanel,
            	    this.panel_detalle
           ]
}, this.bajar ]
});
});

function showResult(btn){
	if(btn=="yes"){
		Ext.MessageBox.show({title: 'Cerrando sesi&oacute;n', msg: '<br>Por favor  Espere...',width:300,closable:false,icon:Ext.MessageBox.INFO});
		location.href='autenticar';
	}
}

function logOut(){
	Ext.MessageBox.confirm('Confirmar', '¿Seguro que desea salir del Sistema?', showResult);
}
</script>
<div id="formulario_ubicacion"></div>

@endsection
