<script type="text/javascript">
Ext.ns("forma005ActividadEditar");
function actividadEstado(val){
	if(val==6){
	    return '<tpl><div style="margin-bottom: -4px; margin-top: -4px;" class="x-grid-row">'+'<img src="{{ asset('images/16x16/check.png') }}" style="cursor:pointer;">'+' <span style="color:green;"> Cargado</span>'+'</div></tpl>';
	}else{
            if(val==5){
	    return '<tpl><div style="margin-bottom: -4px; margin-top: -4px;" class="x-grid-row">'+'<img src="{{ asset('images/16x16/seguimiento.png') }}" style="cursor:pointer;">'+' <span style="color:red;"> Pendiente</span>'+'</div></tpl>';
	}else{
        return '<tpl><div style="margin-bottom: -4px; margin-top: -4px;" class="x-grid-row">'+'<img src="{{ asset('images/16x16/seguimiento.png') }}" style="cursor:pointer;">'+' <span style="color:red;"> Negado</span>'+'</div></tpl>';
        }
        }
return val;
};
forma005ActividadEditar.main = {
init:function(){
//Mascara general del modulo
this.mascara = new Ext.LoadMask(Ext.getBody(), {msg:"Cargando..."});

//objeto store
this.store_lista = this.getLista();

this.nuevo = new Ext.Button({
    text:'Nuevo',
    iconCls: 'icon-nuevo',
	handler:function(){
	this.codigo  = '{{ $id_tab_meta_fisica }}';
	forma005ActividadEditar.main.mascara.show();
			this.msg = Ext.get('formularioActividadEditar');
			this.msg.load({
			 url:"{{ URL::to('ac/seguimiento/005/nuevoActividad') }}/"+this.codigo,
			 scripts: true,
			 text: "Cargando.."
			});
	}
});
//Editar un registro
this.editar= new Ext.Button({
    text:'Editar Indicadores',
    iconCls: 'icon-editar',
	handler:function(){
	this.codigo  = forma005ActividadEditar.main.gridPanel_.getSelectionModel().getSelected().get('id');
	forma005ActividadEditar.main.mascara.show();
			this.msg = Ext.get('formularioActividadEditar');
			this.msg.load({
			 url:"{{ URL::to('ac/seguimiento/005/editar') }}/"+this.codigo,
			 scripts: true,
			 text: "Cargando.."
			});
	}
});

this.editar.disable();

this.eliminar= new Ext.Button({
	text:'Eliminar',
	iconCls: 'icon-eliminar',
	handler: function(boton){
		this.codigo  = forma005ActividadEditar.main.gridPanel_.getSelectionModel().getSelected().get('id');
		Ext.MessageBox.confirm('Confirmación', '¿Realmente desea Eliminar?', function(boton){
		if(boton=="yes"){
	        Ext.Ajax.request({
	            method:'POST',
	            url:'{{ URL::to('ac/seguimiento/005/eliminarIndicador') }}',
	            params:{
			_token: '{{ csrf_token() }}',
	                id: forma005ActividadEditar.main.gridPanel_.getSelectionModel().getSelected().get('id')
	            },
	            success:function(result, request ) {
	                obj = Ext.util.JSON.decode(result.responseText);
	                if(obj.success=="true"){
			    forma005ActividadEditar.main.store_lista.load();
	                    Ext.Msg.alert("Notificación",obj.msg);
	                }else{
	                    Ext.Msg.alert("Notificación",obj.msg);
	                }
	                forma005ActividadEditar.main.mascara.hide();
	            }});
		}});
	}
});

this.eliminar.disable();

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
		forma005ActividadEditar.main.store_lista.baseParams={};
		forma005ActividadEditar.main.store_lista.baseParams.paginar = 'si';
		forma005ActividadEditar.main.store_lista.baseParams._token = '{{ csrf_token() }}';
    forma005ActividadEditar.main.store_lista.baseParams.id_tab_meta_fisica = '{{ $id_tab_meta_fisica }}';
		forma005ActividadEditar.main.store_lista.load();
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
			forma005ActividadEditar.main.store_lista.baseParams={}
			forma005ActividadEditar.main.store_lista.baseParams.BuscarBy = true;
			forma005ActividadEditar.main.store_lista.baseParams._token = '{{ csrf_token() }}';
                        forma005ActividadEditar.main.store_lista.baseParams.id_tab_meta_fisica = '{{ $id_tab_meta_fisica }}';
			forma005ActividadEditar.main.store_lista.baseParams[this.paramName] = v;
			forma005ActividadEditar.main.store_lista.baseParams.paginar = 'si';
			forma005ActividadEditar.main.store_lista.load();
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
    height:610,
    tbar:[
			@if( in_array( array( 'de_privilegio' => 'acseguimiento.nuevo', 'in_habilitado' => true), Session::get('credencial') ))
				this.nuevo,'-',
			@endif
			@if( in_array( array( 'de_privilegio' => 'acseguimiento.nuevo', 'in_habilitado' => true), Session::get('credencial') ))
				this.eliminar,'-',
			@endif                        
				this.buscador
    ],
    columns: [
    new Ext.grid.RowNumberer(),
    {header: 'id',hidden:true, menuDisabled:true,dataIndex: 'id'},
    {header: 'TIPO DE INDICADOR', width:180, renderer: textoLargo, menuDisabled:true, sortable: true, dataIndex: 'de_tipo_indicador'},
    {header: 'SUB TIPO DE INDICADOR', width:160, renderer: textoLargo, menuDisabled:true, sortable: true, dataIndex: 'de_sub_tipo_indicador'},
    {header: 'CANTIDAD', width:50,  menuDisabled:true, sortable: true, renderer: textoLargo, dataIndex: 'nu_cantidad'},
    {header: 'UNIDAD DE MEDIDA', width:120,  menuDisabled:true, sortable: true, renderer: textoLargo, dataIndex: 'de_unidad_medida'},
    {header: 'MUNICIPIO', width:150,  menuDisabled:true, sortable: true, renderer: textoLargo, dataIndex: 'de_municipio'},
    {header: 'PARROQUIA', width:150,  menuDisabled:true, sortable: true, renderer: textoLargo, dataIndex: 'de_parroquia'},
    {header: 'COMUNA', width:150,  menuDisabled:true, sortable: true, renderer: textoLargo, dataIndex: 'de_comuna'},
    ],
    stripeRows: true,
    autoScroll:true,
    stateful: true,
    listeners:{cellclick:function(Grid, rowIndex, columnIndex,e ){
			forma005ActividadEditar.main.editar.enable();
                        forma005ActividadEditar.main.eliminar.enable();
		}},
    bbar: new Ext.PagingToolbar({
        pageSize: 20,
        store: this.store_lista,
        displayInfo: true,
        displayMsg: '<span style="color:black">Registros: {0} - {1} de {2}</span>',
        emptyMsg: "<span style=\"color:black\">No se encontraron registros</span>"
    })
});

this.winformPanel_ = new Ext.Window({
    title:'Actividades: INDICADORES DE GESTIÓN',
    modal:true,
    constrain:true,
    width:1000,
    frame:true,
    closabled:true,
    autoHeight:true,
    items:[
      this.gridPanel_
    ]
});
this.winformPanel_.show();
forma005Lista.main.mascara.hide();

//Cargar el grid
this.store_lista.baseParams.paginar = 'si';
this.store_lista.baseParams._token = '{{ csrf_token() }}';
this.store_lista.baseParams.id_tab_meta_fisica = '{{ $id_tab_meta_fisica }}';
this.store_lista.load();
this.store_lista.on('load',function(){
forma005ActividadEditar.main.editar.disable();
forma005ActividadEditar.main.eliminar.disable();
});
this.store_lista.on('beforeload',function(){
panel_detalle.collapse();
});
},
getLista: function(){
    this.store = new Ext.data.JsonStore({
    url:'{{ URL::to('ac/seguimiento/005/datos/storeListaIndicadores') }}',
    root:'data',
    fields:[
    {name: 'id'},
    {name: 'de_tipo_indicador'},
    {name: 'de_sub_tipo_indicador'},
    {name: 'de_municipio'},
    {name: 'de_parroquia'},
    {name: 'de_comuna'},
    {name: 'de_unidad_medida'},
    {name: 'nu_cantidad'}
           ]
    });
    return this.store;
}
};
Ext.onReady(forma005ActividadEditar.main.init, forma005ActividadEditar.main);
</script>
<div id="formularioActividadEditar"></div>