<?php

namespace matriz\Http\Controllers\Panel;
//*******agregar esta linea******//
use matriz\Models\Mantenimiento\tab_unidad_medida;
use matriz\Models\Autenticacion\tab_menu;
use matriz\Models\Autenticacion\tab_rol;
use matriz\Models\Autenticacion\tab_usuarios;
use matriz\Models\Autenticacion\tab_usuario_rol;
use matriz\Models\Auditoria\tab_login_acceso;
use matriz\Models\Mantenimiento\tab_funcionario;
use View;
use Validator;
use Input;
use Response;
use DB;
use Session;
use Auth;
//*******************************//
use Illuminate\Http\Request;

use matriz\Http\Requests;
use matriz\Http\Controllers\Controller;

class panelController extends Controller
{
    /**
    * Create a new controller instance.
    *
    * @return void
    */
    public function __construct()
    {
      $this->middleware('auth');
      $this->middleware('optimizar');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function inicio()
    {
        $menu = tab_menu::select('id_tab_menu', 'de_icono', 'de_menu', 'autenticacion.tab_menu.id as id_menu')
        ->join('autenticacion.tab_rol_menu AS rol_menu', 'rol_menu.id_tab_menu', '=', 'autenticacion.tab_menu.id')
        ->where('id_padre', '=', 0)
        ->where('rol_menu.id_tab_rol', '=', Session::get('rol'))
        //->where('rol_menu.in_estatus', '=', true)
        ->orderBy('nu_orden', 'ASC')
        ->get();

        $arbol = '';
        foreach($menu as $item){
          $cantidad = tab_menu::sp_catidad_menu_hijo($item->id_tab_menu, Session::get('rol'));
          if($cantidad[0]->sp_catidad_menu_hijo > 0)
          {

                       $arbol.= "{
                                 title:'<b>".$item->de_menu."</b>',
                                 autoScroll:true,
               border:false,
               collapsed:true,
               iconCls:'".$item->de_icono."',
               autoHeight:true,
               items:[    new Ext.tree.TreePanel({
                    id:'".$item->id_menu."',
                    loader: new Ext.tree.TreeLoader(),
                    rootVisible:false,
                    lines:true,
                    autoScroll:true,
                    border: false,
                    autoHeight:true,
                    listeners: {
                    click : {
                    scope : this,
                    fn    : function( n, e )
                    {
                  if(n.leaf){
                      /*Accedemos a los a atributod del json que usamos para crear el nodo con*/
                      myobject = n;
                      if (n.attributes.url){url = n.attributes.url;} else {url =n.id;}
                      /*Abrimos el nuevo tab*/
                      addTab(n.id,n.text,url,n.attributes.tabType,n.attributes.iconCls, '', n.attributes.nu_margen);
                  }
                    }
                    }
                    },
                    root: new Ext.tree.AsyncTreeNode({
                  children:[".self::ArmaSubmenu($item->id_tab_menu, Session::get('rol'))."]
                    })
                  })]
                                },";

          }
        }

        $ultimo_login = tab_login_acceso::orderBy('created_at', 'desc')
        ->where('auditoria.tab_login_acceso.id_tab_usuarios', '=', Auth::user()->id)
        //->take(1)->skip(1)
        ->first();

        $funcionario = tab_usuarios::select( 'inicial', 'nu_cedula', 'nb_funcionario', 'ap_funcionario',
        't01.created_at as fe_registro',
        'id_ejecutor' , 'tx_email', 'tx_ejecutor' )
        ->Join('mantenimiento.tab_funcionario as t01', 't01.id_tab_usuarios', '=', 'autenticacion.tab_usuarios.id')
        ->Join('mantenimiento.tab_documento as t02', 't02.id', '=', 't01.id_tab_documento')
        ->Join('mantenimiento.tab_ejecutores as t03', 't01.id_tab_ejecutores', '=', 't03.id')
        ->where('autenticacion.tab_usuarios.id', '=', Auth::user()->id)
        ->first();

        $bandeja = tab_rol::select('de_bandeja', 'de_url_bandeja')
        ->where('id', '=', Session::get('rol'))
        ->first();

        return View::make('inicio.ejecutor')
        ->with('bandeja', $bandeja)
        ->with('funcionario', $funcionario)
        ->with('ultimo_login', $ultimo_login)
        ->with('menu',$arbol);
    }

    static public function ArmaSubmenu($co_padre, $co_rol){

      $menu = tab_menu::select('t04.id','id_tab_menu','de_menu','de_icono','da_url','nu_margen', 'de_detalle')
      ->join('autenticacion.tab_rol_menu as t04', 'autenticacion.tab_menu.id', '=', 't04.id_tab_menu')
      ->where('id_padre', '=', $co_padre)
      ->where('t04.id_tab_rol', '=', $co_rol)
      ->where('autenticacion.tab_menu.in_estatus', '=', true)
      ->where('t04.in_estatus', '=', true)
      ->orderBy('nu_orden', 'ASC')->get();

      $submenu = '';
      foreach($menu as $items){
        $cantidad = tab_menu::sp_catidad_menu_privilegio($items->id_tab_menu, $co_rol);
        if($cantidad[0]->sp_catidad_menu_privilegio > 0)
        {
           $submenu.= "{
                              text:'".$items->de_menu."',
                              children:[".self::ArmaSubmenu($items->id_tab_menu, $co_rol)."]
                              },";
        }else{
          $submenu.= "{
            id: '".$items->id_tab_menu."',
            url: '".$items->da_url."',
            tabType:'load',
            text:'".$items->de_menu."',
            iconCls:'".$items->de_icono."',
            nu_margen:'".$items->nu_margen."',
            qtip : '".$items->de_detalle."',
            leaf:true
          },";
          }
        }
        return  $submenu;
    }
}
