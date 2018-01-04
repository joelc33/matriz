<script type="text/javascript">
Ext.ns("acejecucionLista");
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
acejecucionLista.main = {
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
        acejecucionLista.main.mascara.show();
        this.msg = Ext.get('formularioacseguimiento');
        this.msg.load({
         url:"{{ URL::to('ac/seguimiento/nuevo') }}",
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
	this.codigo  = acejecucionLista.main.gridPanel_.getSelectionModel().getSelected().get('id');
	acejecucionLista.main.mascara.show();
        this.msg = Ext.get('formularioacseguimiento');
        this.msg.load({
         url:"{{ URL::to('ac/seguimiento/editar') }}/"+this.codigo,
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
	this.codigo  = acejecucionLista.main.gridPanel_.getSelectionModel().getSelected().get('id');
	Ext.MessageBox.confirm('Confirmación', '¿Realmente desea Deshabilitar Registro?', function(boton){
	if(boton=="yes"){
        Ext.Ajax.request({
            method:'POST',
            url:'{{ URL::to('seguimiento/ac/eliminar') }}',
            params:{
		_token: '{{ csrf_token() }}',
                id: acejecucionLista.main.gridPanel_.getSelectionModel().getSelected().get('id')
            },
            success:function(result, request ) {
                obj = Ext.util.JSON.decode(result.responseText);
                if(obj.success=="true"){
		    acejecucionLista.main.store_lista.load();
                    Ext.Msg.alert("Notificación",obj.msg);
                }else{
                    Ext.Msg.alert("Notificación",obj.msg);
                }
                acejecucionLista.main.mascara.hide();
            }});
	}});
    }
});

this.habilitar= new Ext.Button({
    text:'Habilitar',
    iconCls: 'icon-fin',
    handler:function(){
	this.codigo  = acejecucionLista.main.gridPanel_.getSelectionModel().getSelected().get('id');
	Ext.MessageBox.confirm('Confirmación', '¿Realmente desea Habilitar Registro?', function(boton){
	if(boton=="yes"){
        Ext.Ajax.request({
            method:'POST',
            url:'{{ URL::to('seguimiento/ac/habilitar') }}',
            params:{
		_token: '{{ csrf_token() }}',
                id: acejecucionLista.main.gridPanel_.getSelectionModel().getSelected().get('id')
            },
            success:function(result, request ) {
                obj = Ext.util.JSON.decode(result.responseText);
                if(obj.success=="true"){
		    acejecucionLista.main.store_lista.load();
                    Ext.Msg.alert("Notificación",obj.msg);
                }else{
                    Ext.Msg.alert("Notificación",obj.msg);
                }
                acejecucionLista.main.mascara.hide();
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
		acejecucionLista.main.store_lista.baseParams={};
		acejecucionLista.main.store_lista.baseParams.paginar = 'si';
		acejecucionLista.main.store_lista.baseParams._token = '{{ csrf_token() }}';
		acejecucionLista.main.store_lista.load();
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
			acejecucionLista.main.store_lista.baseParams={}
			acejecucionLista.main.store_lista.baseParams.BuscarBy = true;
			acejecucionLista.main.store_lista.baseParams._token = '{{ csrf_token() }}';
			acejecucionLista.main.store_lista.baseParams[this.paramName] = v;
			acejecucionLista.main.store_lista.baseParams.paginar = 'si';
			acejecucionLista.main.store_lista.load();
		}
	}
});

//Grid principal
this.gridPanel_ = new Ext.grid.GridPanel({
    iconCls: 'icon-libro',
    store: this.store_lista,
    border:true,
    loadMask:true,
    autoWidth: true,
    autoHeight:true,
    tbar:[
			@if( in_array( array( 'de_privilegio' => 'acseguimiento.nuevo', 'in_habilitado' => true), Session::get('credencial') ))
			  this.habilitar,'-',
			@endif
				this.buscador
    ],
    columns: [
    new Ext.grid.RowNumberer(),
    {header: 'id',hidden:true, menuDisabled:true,dataIndex: 'id'},
		{header: 'Código', width:80,  menuDisabled:true, sortable: true, dataIndex: 'periodo'},
    {header: 'Denominación', width:200,  menuDisabled:true, sortable: true, dataIndex: 'ejecutor'},
		{header: 'P. Inicial', width:100,  menuDisabled:true, sortable: true, dataIndex: 'nu_codigo'},
    {header: 'P. Modificado', width:100,  menuDisabled:true, sortable: true, dataIndex: 'de_ac'},
    {header: 'P. Actualizado (Total)', width:100,  menuDisabled:true, sortable: true, dataIndex: 'in_activo'},
		{header: 'Comprometido', width:100,  menuDisabled:true, sortable: true, dataIndex: 'de_ac'},
		{header: 'Causado', width:100,  menuDisabled:true, sortable: true, dataIndex: 'de_ac'},
		{header: 'Pagado', width:100,  menuDisabled:true, sortable: true, dataIndex: 'de_ac'},
    ],
    stripeRows: true,
    autoScroll:true,
    stateful: true,
    listeners:{cellclick:function(Grid, rowIndex, columnIndex,e ){
			acejecucionLista.main.editar.enable();
			acejecucionLista.main.habilitar.enable();
			acejecucionLista.main.eliminar.enable();
		}},
    bbar: new Ext.PagingToolbar({
        pageSize: 20,
        store: this.store_lista,
        displayInfo: true,
        displayMsg: '<span style="color:black">Registros: {0} - {1} de {2}</span>',
        emptyMsg: "<span style=\"color:black\">No se encontraron registros</span>"
    }),
		sm: new Ext.grid.RowSelectionModel({
			singleSelect: true,
			/*AQUI ES DONDE ESTA EL LISTENER*/
				listeners: {
				rowselect: function(sm, row, rec) {
					var msg = Ext.get('detalle');
					msg.load({
									url: '{{ URL::to('ac/seguimiento/001/detalle') }}',
									scripts: true,
									params: {_token:'{{ csrf_token() }}', codigo:rec.json.id},
									text: 'Cargando...'
					});
					if(panel_detalle.collapsed == true)
					{
						panel_detalle.toggleCollapse();
					}
				}
			}
		})
});

/*Evento Doble Click*/
this.gridPanel_.on('rowdblclick', function( grid, row, evt){
	panel_detalle.toggleCollapse(true);
	this.record = acejecucionLista.main.store_lista.getAt(row);
	this.codigo = this.record.data["id"];
	this.msg = Ext.get('detalle');
	this.msg.load({
	    url: '{{ URL::to('ac/seguimiento/001/detalle') }}',
	    scripts: true,
	    params: {_token:'{{ csrf_token() }}', codigo:this.codigo},
	    text: "Cargando..."
	});
});

this.panel = new Ext.Panel({
	layout: "fit",
	border: false,
	padding: 5,
	items: [
		this.gridPanel_
	]
});

this.panel.render("contenedoracejecucionLista");

//Cargar el grid
this.store_lista.baseParams.paginar = 'si';
this.store_lista.baseParams._token = '{{ csrf_token() }}';
this.store_lista.load();
this.store_lista.on('load',function(){
acejecucionLista.main.editar.disable();
acejecucionLista.main.habilitar.disable();
acejecucionLista.main.eliminar.disable();
});
this.store_lista.on('beforeload',function(){
panel_detalle.collapse();
});
},
getLista: function(){
    this.store = new Ext.data.JsonStore({
	    url:'{{ URL::to('ac/seguimiento/ejecucion/storeLista') }}',
	    root:'data',
	    fields:[
		    {name: 'id'},
				{name: 'id_tab_ejecutores'},
				{name: 'id_tab_ejecutores'},
		    {name: 'tx_ejecutor'},
				{name: 'nu_codigo'},
		    {name: 'de_ac'},
				{
						name: 'ejecutor',
						convert: function(v, r) {
								return r.id_tab_ejecutores + ' - ' + r.tx_ejecutor;
						}
				},
				{
						name: 'periodo',
						convert: function(v, r) {
								return r.fe_inicio + ' - ' + r.fe_fin;
						}
				}
	    ]
    });
    return this.store;
}
};
Ext.onReady(acejecucionLista.main.init, acejecucionLista.main);
</script>
<div id="contenedoracejecucionLista"></div>
<div id="formularioacseguimiento"></div>
