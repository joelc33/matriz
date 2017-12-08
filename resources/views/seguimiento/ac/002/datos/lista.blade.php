<script type="text/javascript">
Ext.ns("forma002DetalleLista");
function change(val){
	if(val==true){
	    return '<span style="color:green;">Activo</span>';
	}else if(val==false){
	    return '<span style="color:red;">Inactivo</span>';
	}
return val;
};
function movimiento(val){
	if(val==true){
	    return '<span style="color:green;">Si</span>';
	}else if(val==false){
	    return '<span style="color:red;">No</span>';
	}
return val;
};
forma002DetalleLista.main = {
condicion:function(codigo){
    return (codigo=='0')?'NO':'SI';
},
init:function(){
//Mascara general del modulo
this.mascara = new Ext.LoadMask(Ext.getBody(), {msg:"Cargando..."});

//objeto store
this.store_lista = this.getLista();

//Agregar un registro
this.nuevo = new Ext.Button({
    text:'Nuevo',
    iconCls: 'icon-nuevo',
    handler:function(){
        forma002DetalleLista.main.mascara.show();
        this.msg = Ext.get('forma002Detalle');
        this.msg.load({
         url:"{{ URL::to('mantenimiento/aplicacion/nuevo') }}",
         scripts: true,
         text: "Cargando.."
        });
    }
});

//Editar un registro
this.editar= new Ext.Button({
    text:'Editar',
    iconCls: 'icon-editar',
    handler:function(){
	this.codigo  = forma002DetalleLista.main.gridPanel_.getSelectionModel().getSelected().get('co_metas');
	forma002DetalleLista.main.mascara.show();
        this.msg = Ext.get('forma002Detalle');
        this.msg.load({
         url:"{{ URL::to('ac/seguimiento/002/editar') }}/"+this.codigo,
         scripts: true,
         text: "Cargando.."
        });
    }
});

//Desabilitar un registro
this.eliminar= new Ext.Button({
    text:'Deshabilitar',
    iconCls: 'icon-cancelar',
    handler:function(){
	this.codigo  = forma002DetalleLista.main.gridPanel_.getSelectionModel().getSelected().get('id');
	Ext.MessageBox.confirm('Confirmación', '¿Realmente desea Deshabilitar Registro?', function(boton){
	if(boton=="yes"){
        Ext.Ajax.request({
            method:'POST',
            url:'{{ URL::to('mantenimiento/aplicacion/eliminar') }}',
            params:{
		_token: '{{ csrf_token() }}',
                id: forma002DetalleLista.main.gridPanel_.getSelectionModel().getSelected().get('id')
            },
            success:function(result, request ) {
                obj = Ext.util.JSON.decode(result.responseText);
                if(obj.success=="true"){
		    forma002DetalleLista.main.store_lista.load();
                    Ext.Msg.alert("Notificación",obj.msg);
                }else{
                    Ext.Msg.alert("Notificación",obj.msg);
                }
                forma002DetalleLista.main.mascara.hide();
            }});
	}});
    }
});

this.habilitar= new Ext.Button({
    text:'Habilitar',
    iconCls: 'icon-fin',
    handler:function(){
	this.codigo  = forma002DetalleLista.main.gridPanel_.getSelectionModel().getSelected().get('id');
	Ext.MessageBox.confirm('Confirmación', '¿Realmente desea Habilitar Registro?', function(boton){
	if(boton=="yes"){
        Ext.Ajax.request({
            method:'POST',
            url:'{{ URL::to('mantenimiento/aplicacion/habilitar') }}',
            params:{
		_token: '{{ csrf_token() }}',
                id: forma002DetalleLista.main.gridPanel_.getSelectionModel().getSelected().get('id')
            },
            success:function(result, request ) {
                obj = Ext.util.JSON.decode(result.responseText);
                if(obj.success=="true"){
		    forma002DetalleLista.main.store_lista.load();
                    Ext.Msg.alert("Notificación",obj.msg);
                }else{
                    Ext.Msg.alert("Notificación",obj.msg);
                }
                forma002DetalleLista.main.mascara.hide();
            }});
	}});
    }
});

this.editar.disable();
this.eliminar.disable();
this.habilitar.disable();

this.buscador = new Ext.form.TwinTriggerField({
	initComponent : function(){
		Ext.ux.form.SearchField.superclass.initComponent.call(this);
		this.on('specialkey', function(f, e){
			if(e.getKey() == e.ENTER){
				this.onTrigger2Click();
			}
		}, this);
	},
	xtype: 'twintriggerfield',
	trigger1Class: 'x-form-clear-trigger',
	trigger2Class: 'x-form-search-trigger',
	enableKeyEvents : true,
	validationEvent:false,
	validateOnBlur:false,
	emptyText: 'Campo de Filtro',
	width:350,
	hasSearch : false,
	paramName : 'variable',
	onTrigger1Click : function() {
		if (this.hiddenField) {
			this.hiddenField.value = '';
		}
		this.setRawValue('');
		this.lastSelectionText = '';
		this.applyEmptyText();
		this.value = '';
		this.fireEvent('clear', this);
		forma002DetalleLista.main.store_lista.baseParams={};
		forma002DetalleLista.main.store_lista.baseParams.paginar = 'si';
		forma002DetalleLista.main.store_lista.baseParams._token = '{{ csrf_token() }}';
		forma002DetalleLista.main.store_lista.load();
	},
	onTrigger2Click : function(){
		var v = this.getRawValue();
		if(v.length < 1){
			    Ext.MessageBox.show({
				       title: 'Notificación',
				       msg: 'Debe ingresar un parametro de busqueda',
				       buttons: Ext.MessageBox.OK,
				       icon: Ext.MessageBox.WARNING
			    });
		}else{
			forma002DetalleLista.main.store_lista.baseParams={}
			forma002DetalleLista.main.store_lista.baseParams.BuscarBy = true;
			forma002DetalleLista.main.store_lista.baseParams._token = '{{ csrf_token() }}';
			forma002DetalleLista.main.store_lista.baseParams[this.paramName] = v;
			forma002DetalleLista.main.store_lista.baseParams.paginar = 'si';
			forma002DetalleLista.main.store_lista.load();
		}
	}
});

//Grid principal
this.gridPanel_ = new Ext.grid.GridPanel({
    iconCls: 'icon-libro',
    store: this.store_lista,
    border:false,
    loadMask:true,
    autoWidth: true,
    autoHeight:true,
    tbar:[
			@if( in_array( array( 'de_privilegio' => 'aplicacion.nuevo', 'in_habilitado' => true), Session::get('credencial') ))
			  this.nuevo,'-',
			@endif
			@if( in_array( array( 'de_privilegio' => 'aplicacion.editar', 'in_habilitado' => true), Session::get('credencial') ))
				this.editar,'-',
			@endif
			@if( in_array( array( 'de_privilegio' => 'aplicacion.habilitar', 'in_habilitado' => true), Session::get('credencial') ))
				this.habilitar,'-',
			@endif
			@if( in_array( array( 'de_privilegio' => 'aplicacion.deshabilitar', 'in_habilitado' => true), Session::get('credencial') ))
				this.eliminar,'-',
			@endif
				this.buscador
    ],
    columns: [
    new Ext.grid.RowNumberer(),
		{header: 'co_metas',hidden:true, menuDisabled:true,dataIndex: 'co_metas'},
    {header: 'CODIGO', width:100,  menuDisabled:true, sortable: true,  dataIndex: 'codigo'},
    {header: 'ACTIVIDAD', width:200,  menuDisabled:true, sortable: true, renderer: textoLargo, dataIndex: 'nb_meta'},
    {header: 'UNIDAD DE MEDIDA', width:150,  menuDisabled:true, sortable: true, renderer: textoLargo, dataIndex: 'co_unidades_medida'},
    {header: 'PROGRAMADO ANUAL', width:150,  menuDisabled:true, sortable: true, renderer: textoLargo, dataIndex: 'tx_prog_anual'},
    {header: 'INICIO', width:100,  menuDisabled:true, sortable: true, renderer: textoLargo, dataIndex: 'fecha_inicio'},
    {header: 'FINAL', width:100,  menuDisabled:true, sortable: true, renderer: textoLargo, dataIndex: 'fecha_fin'},
    {header: 'TOTAL CARGADO', width:120,  menuDisabled:true, sortable: true, dataIndex: 'mo_cargado'},
    {header: 'RESPONSABLE', width:120,  menuDisabled:true, sortable: true, renderer: textoLargo, dataIndex: 'nb_responsable'},
    ],
    stripeRows: true,
    autoScroll:true,
    stateful: true,
    listeners:{cellclick:function(Grid, rowIndex, columnIndex,e ){
			forma002DetalleLista.main.editar.enable();
			forma002DetalleLista.main.habilitar.enable();
			forma002DetalleLista.main.eliminar.enable();
		}},
    bbar: new Ext.PagingToolbar({
        pageSize: 20,
        store: this.store_lista,
        displayInfo: true,
        displayMsg: '<span style="color:black">Registros: {0} - {1} de {2}</span>',
        emptyMsg: "<span style=\"color:black\">No se encontraron registros</span>"
    })
});

this.gridPanel_.render("contenedorforma002DetalleLista");

//Cargar el grid
this.store_lista.baseParams.paginar = 'si';
this.store_lista.baseParams._token = '{{ csrf_token() }}';
this.store_lista.load();
this.store_lista.on('load',function(){
forma002DetalleLista.main.editar.disable();
forma002DetalleLista.main.habilitar.disable();
forma002DetalleLista.main.eliminar.disable();
});
this.store_lista.on('beforeload',function(){
panel_detalle.collapse();
});
},
getLista: function(){
    this.store = new Ext.data.JsonStore({
    url:'{{ URL::to('ac/seguimiento/002/datos/storeLista') }}',
    root:'data',
    fields:[
			{name: 'co_metas'},
	    {name: 'codigo'},
	    {name: 'co_proyecto_acc_espec'},
	    {name: 'nb_meta'},
	    {name: 'co_unidades_medida'},
	    {name: 'tx_prog_anual'},
	    {name: 'fecha_inicio'},
	    {name: 'fecha_fin'},
	    {name: 'nb_responsable'},
	    {name: 'mo_cargado'},
           ]
    });
    return this.store;
}
};
Ext.onReady(forma002DetalleLista.main.init, forma002DetalleLista.main);
</script>
<div id="contenedorforma002DetalleLista"></div>
<div id="forma002Detalle"></div>
