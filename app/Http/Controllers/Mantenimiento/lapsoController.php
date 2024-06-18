<?php

namespace matriz\Http\Controllers\Mantenimiento;

//*******agregar esta linea******//
use matriz\Models\Mantenimiento\tab_lapso;
use matriz\Models\Ac\tab_ac as ac;
use matriz\Models\Ac\tab_ac_ae as ac_ae;
use matriz\Models\Ac\tab_ac_ae_partida as ac_ae_partida;
use matriz\Models\Ac\tab_meta_fisica as ac_ae_mf;
use matriz\Models\Ac\tab_meta_financiera as ac_ae_mff;
use matriz\Models\AcSegto\tab_ac;
use matriz\Models\AcSegto\tab_ac_ae;
use matriz\Models\AcSegto\tab_ac_ae_partida;
use matriz\Models\AcSegto\tab_meta_fisica;
use matriz\Models\AcSegto\tab_meta_financiera;
use View;
use Validator;
use Input;
use Response;
use DB;
//*******************************//
use Illuminate\Http\Request;

use matriz\Http\Requests;
use matriz\Http\Controllers\Controller;

class lapsoController extends Controller
{
    protected $tab_lapso;

    public function __construct(tab_lapso $tab_lapso)
    {
        $this->middleware('auth');
        $this->tab_lapso = $tab_lapso;
    }

    /**
    * Display a listing of the resource.
    *
    * @return Response
    */
    public function lista()
    {
        return View::make('mantenimiento.lapso.lista');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function storeLista()
    {
        try {
            $start  = Input::get('start', 0);
            $limit  = Input::get('limit', 20);
            $variable = Input::get('variable');

            $tab_lapso = $this->tab_lapso
            ->join('mantenimiento.tab_periodo as t01', 't01.id', '=', 'mantenimiento.tab_lapso.id_tab_periodo')
            ->select(
                'mantenimiento.tab_lapso.id',
                'id_tab_ejercicio_fiscal',
                'de_periodo',
                'nu_lapso',
                'de_lapso',
                DB::raw("to_char(fe_inicio, 'dd/mm/YYYY') as fe_inicio"),
                DB::raw("to_char(fe_fin, 'dd/mm/YYYY') as fe_fin"),
                'mantenimiento.tab_lapso.in_activo'
            );

            if (Input::get("BuscarBy")=="true") {

                if($variable!="") {
                    $tab_lapso->where('de_periodo', 'ILIKE', "%$variable%");
                }

                $response['success']  = 'true';
                $response['total'] = $tab_lapso->count();
                $tab_lapso->skip($start)->take($limit);
                $response['data']  = $tab_lapso->orderby('mantenimiento.tab_lapso.id', 'ASC')->get()->toArray();
            } else {
                $response['success']  = 'true';
                $response['total'] = $tab_lapso->count();
                $tab_lapso->skip($start)->take($limit);
                $response['data']  = $tab_lapso->orderby('mantenimiento.tab_lapso.id', 'ASC')->get()->toArray();
            }

            return Response::json($response, 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return Response::json(array('success' => false, 'message' => utf8_encode($e->getMessage())), 200);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function nuevo()
    {
        $data = json_encode(array("id" => ""));
        return View::make('mantenimiento.lapso.editar')->with('data', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function editar($id)
    {
        $data = tab_lapso::select(
            'id',
            'id_tab_ejercicio_fiscal',
            'id_tab_periodo',
            'nu_lapso',
            'fe_inicio',
            'fe_fin',
            'in_activo',
            'de_lapso'
        )
        ->where('id', '=', $id)
        ->first();
        return View::make('mantenimiento.lapso.editar')->with('data', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function guardar($id = null)
    {
        DB::beginTransaction();
        if($id!=''||$id!=null) {

            try {
                $validator= Validator::make(Input::all(), tab_lapso::$validarEditar);
                if ($validator->fails()) {
                    return Response::json(array(
                      'success' => false,
                      'msg' => $validator->getMessageBag()->toArray()
                    ));
                }
                $tabla = tab_lapso::find($id);
                $tabla->id_tab_ejercicio_fiscal = Input::get("ejercicio");
                $tabla->id_tab_periodo = Input::get("periodo");
                $tabla->fe_inicio = Input::get("fecha_inicio");
                $tabla->fe_fin = Input::get("fecha_cierre");
                $tabla->de_lapso = Input::get("descripcion");
                $tabla->save();     
                

                DB::commit();
                return Response::json(array(
                  'success' => true,
                  'msg' => 'Registro Editado con Exito!'
                ));

            } catch (\Illuminate\Database\QueryException $e) {
                DB::rollback();
                return Response::json(array(
                  'success' => false,
                  'msg' => array('ERROR ('.$e->getCode().'):'=> $e->getMessage())
                ));
            }

        } else {

            try {
                $validator = Validator::make(Input::all(), tab_lapso::$validarCrear);
                if ($validator->fails()) {
                    return Response::json(array(
                      'success' => false,
                      'msg' => $validator->getMessageBag()->toArray()
                    ));
                }
                $lapso = new tab_lapso();
                $lapso->id_tab_ejercicio_fiscal = Input::get("ejercicio");
                $lapso->id_tab_periodo = Input::get("periodo");
                $lapso->nu_lapso = 1;
                $lapso->fe_inicio = Input::get("fecha_inicio");
                $lapso->fe_fin = Input::get("fecha_cierre");
                $lapso->de_lapso = Input::get("descripcion");
                $lapso->in_activo = 'TRUE';
                $lapso->save();
                
                
                
        $tab_ac = ac::join('mantenimiento.tab_ejecutores as t01', 'public.t46_acciones_centralizadas.id_ejecutor', '=', 't01.id_ejecutor')
        ->join('mantenimiento.tab_ac_predefinida as t03', 'public.t46_acciones_centralizadas.id_accion', '=', 't03.id')
            ->select(
                'public.t46_acciones_centralizadas.id',
                'public.t46_acciones_centralizadas.id_ejecutor',
                'id_ejercicio',
                'id_accion',
                'id_subsector',
                'id_estatus',
                'sit_presupuesto',
                'codigo_new_etapa',
                'de_nombre',
                'monto',
                'monto_calc',
                'fecha_inicio',
                'fecha_fin',
                'de_nombre',
                'tx_ejecutor',
                't01.id as id_tab_ejecutores',
                'inst_mision',
                'inst_vision',
                'inst_objetivos',
                'nu_po_beneficiar',
                'nu_em_previsto',
                'tx_re_esperado',
                'tx_pr_objetivo',
                DB::raw("'AC' || public.t46_acciones_centralizadas.id_ejecutor || id_ejercicio || lpad(id_accion::text, 5, '0') as codigo")
            )
        ->where('edo_reg', '=', true)
        ->where('id_estatus', '=', 3)
        ->where('id_ejercicio', '=', Input::get("ejercicio"))
        ->orderby('public.t46_acciones_centralizadas.id_ejecutor', 'ASC')
        ->orderby('public.t46_acciones_centralizadas.id_accion', 'ASC')
        ->get();  
        
        foreach ($tab_ac as $arreglo_ac) {
            
                $tabla = new tab_ac();
                $tabla->nu_codigo = $arreglo_ac->codigo;
                $tabla->id_ejecutor = $arreglo_ac->id_ejecutor;
                $tabla->id_tab_ejecutores = $arreglo_ac->id_tab_ejecutores;
                $tabla->id_tab_ejercicio_fiscal = $arreglo_ac->id_ejercicio;
                $tabla->id_tab_ac_predefinida = $arreglo_ac->id_accion;
                $tabla->id_tab_sectores = $arreglo_ac->id_subsector;
                $tabla->id_tab_estatus = $arreglo_ac->id_estatus;
                $tabla->id_tab_situacion_presupuestaria = $arreglo_ac->sit_presupuesto;
                $tabla->id_tab_tipo_registro = 1;
                $tabla->co_new_etapa = $arreglo_ac->codigo_new_etapa;
                $tabla->de_ac = $arreglo_ac->de_nombre;
                $tabla->mo_ac = $arreglo_ac->monto;
                $tabla->mo_calculado = $arreglo_ac->monto_calc;
                $tabla->fe_inicio = $arreglo_ac->fecha_inicio;
                $tabla->fe_fin = $arreglo_ac->fecha_fin;
                $tabla->inst_mision = $arreglo_ac->inst_mision;
                $tabla->inst_vision = $arreglo_ac->inst_vision;
                $tabla->inst_objetivos = $arreglo_ac->inst_objetivos;
                $tabla->nu_po_beneficiar = $arreglo_ac->nu_po_beneficiar;
                $tabla->nu_em_previsto = $arreglo_ac->nu_em_previsto;
                $tabla->tx_re_esperado = $arreglo_ac->tx_re_esperado;
                $tabla->pp_anual = $arreglo_ac->tx_pr_objetivo;
                $tabla->id_tab_lapso = $lapso->id;
                $tabla->id_tab_origen = 1;
                $tabla->in_activo = 'TRUE';
                $tabla->in_001 = false;
                $tabla->in_005 = false;
                $tabla->in_bloquear_001 = false;
                $tabla->in_bloquear_005 = false;
                $tabla->id_accion_centralizada = $arreglo_ac->id;
                $tabla->save();  
                
            $tab_ac_ae = ac_ae::select(
                'id_accion_centralizada',
                'id_accion',
                'public.t47_ac_accion_especifica.id_ejecutor',
                'bien_servicio',
                'id_unidad_medida',
                'meta',
                'ponderacion',
                'id_tipo_fondo',
                'monto',
                'monto_calc',
                'fecha_inicio',
                'fecha_fin',
                'edo_reg',
                'fecha_creacion',
                'fecha_actualizacion',
                'objetivo_institucional',
                'id_tab_ejecutor',
                'in_definitivo',
                't01.id as id_tab_ejecutores'
            )
            ->join('mantenimiento.tab_ejecutores as t01', 'public.t47_ac_accion_especifica.id_ejecutor', '=', 't01.id_ejecutor')
            ->where('id_accion_centralizada', '=', $arreglo_ac->id)
            ->orderby('id_accion', 'ASC')
            ->get();
            
            foreach ($tab_ac_ae as $arreglo_ac_ae) {
                
                    $tabla_ac_ae= new tab_ac_ae();
                    $tabla_ac_ae->id_tab_ac = $tabla->id;
                    $tabla_ac_ae->id_tab_ac_ae_predefinida = $arreglo_ac_ae->id_accion;
                    $tabla_ac_ae->id_ejecutor = $arreglo_ac_ae->id_ejecutor;
                    $tabla_ac_ae->id_tab_ejecutores = $arreglo_ac->id_tab_ejecutores;
                    $tabla_ac_ae->bien_servicio = $arreglo_ac_ae->bien_servicio;
                    $tabla_ac_ae->id_tab_unidad_medida = $arreglo_ac_ae->id_unidad_medida;
                    $tabla_ac_ae->meta = $arreglo_ac_ae->meta;
                    $tabla_ac_ae->ponderacion = $arreglo_ac_ae->ponderacion;
                    $tabla_ac_ae->id_tab_tipo_fondo = $arreglo_ac_ae->id_tipo_fondo;
                    $tabla_ac_ae->mo_ae = $arreglo_ac_ae->monto;
                    $tabla_ac_ae->mo_ae_calculado = $arreglo_ac_ae->monto_calc;
                    $tabla_ac_ae->fecha_inicio = $arreglo_ac_ae->fecha_inicio;
                    $tabla_ac_ae->fecha_fin = $arreglo_ac_ae->fecha_fin;
                    $tabla_ac_ae->objetivo_institucional = $arreglo_ac_ae->objetivo_institucional;
                    $tabla_ac_ae->id_tab_origen = 1;
                    $tabla_ac_ae->in_activo = 'TRUE';
                    $tabla_ac_ae->id_accion_centralizada = $arreglo_ac_ae->id_accion_centralizada;
                    $tabla_ac_ae->save();                       
                                
            }

        }                   

                DB::commit();
                return Response::json(array(
                  'success' => true,
                  'msg' => 'Registro Guardado con Exito!'
                ));

            } catch (\Illuminate\Database\QueryException $e) {
                DB::rollback();
                return Response::json(array(
                  'success' => false,
                  'msg' => array('ERROR ('.$e->getCode().'):'=> $e->getMessage())
                ));
            }
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function eliminar()
    {
        DB::beginTransaction();
        try {
            $tabla = tab_lapso::find(Input::get("id"));
            $tabla->in_activo = 'FALSE';
            $tabla->save();
            DB::commit();

            $response['success']  = 'true';
            $response['msg']  = 'Registro Deshabilitado con Exito!';
            return Response::json($response, 200);

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            $response['success']  = 'false';
            $response['msg']  = array('ERROR ('.$e->getCode().'):'=> $e->getMessage());
            return Response::json($response, 200);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function habilitar()
    {
        DB::beginTransaction();
        try {
            $tabla = tab_lapso::find(Input::get("id"));
            $tabla->in_activo = 'TRUE';
            $tabla->save();
            DB::commit();

            $response['success']  = 'true';
            $response['msg']  = 'Registro Habilitado con Exito!';
            return Response::json($response, 200);

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            $response['success']  = 'false';
            $response['msg']  = array('ERROR ('.$e->getCode().'):'=> $e->getMessage());
            return Response::json($response, 200);
        }
    }

}
