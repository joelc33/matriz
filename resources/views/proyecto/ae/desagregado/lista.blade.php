<script type="text/javascript">
Ext.ns("desagregadoLista");
desagregadoLista.main = {
init:function(){

this.OBJ = paqueteComunJS.funcion.doJSON({stringData:'{!! $data !!}'});

this.winformPanel_ = new Ext.Window({
    title:'Partidas Desagregadas de la Acción Específica: '+this.OBJ.tx_codigo+' - '+this.OBJ.descripcion,
    modal:true,
    constrain:true,
    width:614,
    frame:true,
    closabled:true,
    autoHeight:true,
    items:[

    ]
});
this.winformPanel_.show();
partidaLista.main.mascara.hide();
}
};
Ext.onReady(desagregadoLista.main.init, desagregadoLista.main);
</script>
