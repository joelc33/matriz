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

//Editar un registro
this.editar= new Ext.Button({
    text:'Ver Ficha',
    iconCls: 'icon-pdf',
    handler:function(){
	this.codigo  = forma001Lista.main.gridPanel_.getSelectionModel().getSelected().get('id');
	forma001Lista.main.mascara.show();
        this.msg = Ext.get('formularioacseguimiento');
        this.msg.load({
         url:"{{ URL::to('ac/seguimiento/editar') }}/"+this.codigo,
         scripts: true,
         text: "Cargando.."
        });
    }
});

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
			  this.editar,'-',
			@endif
				this.buscador
    ],
    columns: [
    new Ext.grid.RowNumberer(),
    {header: 'id',hidden:true, menuDisabled:true,dataIndex: 'id'},
		{header: 'Código', width:80,  menuDisabled:true, sortable: true, dataIndex: 'co_partida'},
    {header: 'Denominación', width:200,  menuDisabled:true, sortable: true, dataIndex: 'tx_nombre'},
		{header: 'P. Inicial', width:120,  menuDisabled:true, sortable: true, renderer: formatoNumero, dataIndex: 'mo_presupuesto'},
    {header: 'P. Modificado', width:120,  menuDisabled:true, sortable: true, renderer: formatoNumero, dataIndex: 'mo_modificado_anual'},
    {header: 'P. Actualizado (Total)', width:120,  menuDisabled:true, sortable: true, renderer: formatoNumero, dataIndex: 'mo_actualizado_anual'},
		{header: 'Comprometido', width:120,  menuDisabled:true, sortable: true, renderer: formatoNumero, dataIndex: 'mo_comprometido'},
		{header: 'Causado', width:120,  menuDisabled:true, sortable: true, renderer: formatoNumero, dataIndex: 'mo_causado'},
		{header: 'Pagado', width:120,  menuDisabled:true, sortable: true, renderer: formatoNumero, dataIndex: 'mo_pagado'},
    ],
    stripeRows: true,
    autoScroll:true,
    stateful: true,
    listeners:{cellclick:function(Grid, rowIndex, columnIndex,e ){
			acejecucionLista.main.editar.enable();
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
									url: '{{ URL::to('ac/seguimiento/ejecucion/detalle') }}',
									scripts: true,
									params: {_token:'{{ csrf_token() }}', codigo:rec.json.co_partida},
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
	this.codigo = this.record.data["co_partida"];
	this.msg = Ext.get('detalle');
	this.msg.load({
	    url: '{{ URL::to('ac/seguimiento/ejecucion/detalle') }}',
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
				{name: 'co_partida'},
				{name: 'mo_presupuesto'},
		    {name: 'tx_nombre'},
				{name: 'mo_modificado_anual'},
		    {name: 'mo_actualizado_anual'},
				{name: 'mo_comprometido'},
				{name: 'mo_causado'},
				{name: 'mo_pagado'}
	    ]
    });
    return this.store;
}
};
Ext.onReady(acejecucionLista.main.init, acejecucionLista.main);
</script>
<div id="contenedoracejecucionLista"></div>
<div id="formularioacseguimiento"></div>
