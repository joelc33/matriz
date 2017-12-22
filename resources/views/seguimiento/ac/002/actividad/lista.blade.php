<script type="text/javascript">
Ext.ns("forma002ActividadLista");
forma002ActividadLista.main = {
init:function(){
//Mascara general del modulo
this.mascara = new Ext.LoadMask(Ext.getBody(), {msg:"Cargando..."});

//objeto store
this.store_lista = this.getLista();

this.OBJ = paqueteComunJS.funcion.doJSON({stringData:'{!! $data !!}'});

//Editar un registro
this.editar= new Ext.Button({
    text:'Datos',
    iconCls: 'icon-editar',
    handler:function(){
	this.codigo  = forma002ActividadLista.main.gridPanel_.getSelectionModel().getSelected().get('id');
	forma002ActividadLista.main.mascara.show();
        this.msg = Ext.get('forma002Actividad');
        this.msg.load({
         url:"{{ URL::to('ac/seguimiento/002/actividad/editar') }}/"+this.codigo,
         scripts: true,
         text: "Cargando.."
        });
    }
});

this.editar.disable();

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
		forma002ActividadLista.main.store_lista.baseParams={};
		forma002ActividadLista.main.store_lista.baseParams.paginar = 'si';
		forma002ActividadLista.main.store_lista.baseParams._token = '{{ csrf_token() }}';
    forma002ActividadLista.main.store_lista.baseParams.ac_ae = '{{ $data['id'] }}';
		forma002ActividadLista.main.store_lista.load();
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
			forma002ActividadLista.main.store_lista.baseParams={}
			forma002ActividadLista.main.store_lista.baseParams.BuscarBy = true;
			forma002ActividadLista.main.store_lista.baseParams._token = '{{ csrf_token() }}';
      forma002ActividadLista.main.store_lista.baseParams.ac_ae = '{{ $data['id'] }}';
			forma002ActividadLista.main.store_lista.baseParams[this.paramName] = v;
			forma002ActividadLista.main.store_lista.baseParams.paginar = 'si';
			forma002ActividadLista.main.store_lista.load();
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
    height:510,
    tbar:[
			@if( in_array( array( 'de_privilegio' => 'aplicacion.editar', 'in_habilitado' => true), Session::get('credencial') ))
				this.editar,'-',
			@endif
				this.buscador
    ],
    columns: [
    new Ext.grid.RowNumberer(),
    {header: 'id',hidden:true, menuDisabled:true,dataIndex: 'id'},
		{header: 'Codigo', width:50,  menuDisabled:true, sortable: true, dataIndex: 'codigo'},
		{header: 'Actividad', width:300,  menuDisabled:true, sortable: true, dataIndex: 'nb_meta'},
    {header: 'Programado', width:120,  menuDisabled:true, sortable: true,  dataIndex: 'programado'},
    {header: 'Inicio', width:80,  menuDisabled:true, sortable: true,  dataIndex: 'fecha_inicio'},
    {header: 'Final', width:80,  menuDisabled:true, sortable: true,  dataIndex: 'fecha_fin'},
    {header: 'Responsable', width:130,  menuDisabled:true, sortable: true, dataIndex: 'nb_responsable'},
    ],
    stripeRows: true,
    autoScroll:true,
    stateful: true,
    listeners:{cellclick:function(Grid, rowIndex, columnIndex,e ){
			forma002ActividadLista.main.editar.enable();
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
    title:'Formulario: METAS FÍSICAS',
    modal:true,
    constrain:true,
    width:814,
    frame:true,
    closabled:true,
    autoHeight:true,
    items:[
      this.gridPanel_
    ]
});
this.winformPanel_.show();
forma002DetalleLista.main.mascara.hide();

//Cargar el grid
this.store_lista.baseParams.paginar = 'si';
this.store_lista.baseParams._token = '{{ csrf_token() }}';
this.store_lista.baseParams.ac_ae = '{{ $data['id'] }}';
this.store_lista.load();
this.store_lista.on('load',function(){
forma002ActividadLista.main.editar.disable();
});
this.store_lista.on('beforeload',function(){
panel_detalle.collapse();
});
},
getLista: function(){
    this.store = new Ext.data.JsonStore({
    url:'{{ URL::to('ac/seguimiento/002/actividad/storeLista') }}',
    root:'data',
    fields:[
    {name: 'id'},
		{name: 'codigo'},
    {name: 'nb_meta'},
    {name: 'de_unidad_medida'},
    {name: 'fecha_inicio'},
    {name: 'fecha_fin'},
    {name: 'nb_responsable'},
    {
        name: 'programado',
        convert: function(v, r) {
            return r.tx_prog_anual + ' ' + r.de_unidad_medida;
        }
    }
           ]
    });
    return this.store;
}
};
Ext.onReady(forma002ActividadLista.main.init, forma002ActividadLista.main);
</script>
