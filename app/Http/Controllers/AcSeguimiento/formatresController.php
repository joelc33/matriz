<?php

namespace matriz\Http\Controllers\AcSeguimiento;

//*******agregar esta linea******//
use matriz\Models\AcSegto\tab_ac;
use matriz\Models\AcSegto\tab_ac_ae;
use matriz\Models\AcSegto\tab_meta_fisica;
use matriz\Models\AcSegto\tab_meta_financiera;
use View;
use Validator;
use Input;
use Response;
use DB;
use Session;
//*******************************//
use Illuminate\Http\Request;

use matriz\Http\Requests;
use matriz\Http\Controllers\Controller;

class formatresController extends Controller
{
    protected $tab_ac;

    public function __construct(tab_ac $tab_ac)
    {
        $this->middleware('auth');
        $this->tab_ac = $tab_ac;
    }

    /**
    * Display a listing of the resource.
    *
    * @return Response
    */
    public function lista()
    {
        return View::make('seguimiento.ac.003.lista');
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

            $tab_ac = $this->tab_ac
            ->join('mantenimiento.tab_ejecutores as t01', 'ac_seguimiento.tab_ac.id_tab_ejecutores', '=', 't01.id')
            ->join('mantenimiento.tab_lapso as t02', 'ac_seguimiento.tab_ac.id_tab_lapso', '=', 't02.id')
            ->select(
                'ac_seguimiento.tab_ac.id',
                'tx_ejecutor',
                'ac_seguimiento.tab_ac.id_tab_ejecutores',
                'ac_seguimiento.tab_ac.in_activo',
                DB::raw("to_char(t02.fe_inicio, 'dd/mm/YYYY') as fe_inicio"),
                DB::raw("to_char(t02.fe_fin, 'dd/mm/YYYY') as fe_fin"),
                'nu_codigo',
                'de_ac',
                'in_003',
                'ac_seguimiento.tab_ac.id_ejecutor'
            )
            ->where('ac_seguimiento.tab_ac.id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
            ->where('ac_seguimiento.tab_ac.in_activo', '=', true);

            $rol_planificador = array(3, 8);
            if (in_array(Session::get('rol'), $rol_planificador)) {
                $tab_ac->where('ac_seguimiento.tab_ac.id_tab_ejecutores', '=', Session::get('id_tab_ejecutores'));
            }

            if (Input::get("BuscarBy")=="true") {

                if($variable!="") {
                    $tab_ac->where('de_aplicacion', 'ILIKE', "%$variable%");
                }

                $response['success']  = 'true';
                $response['total'] = $tab_ac->count();
                $tab_ac->skip($start)->take($limit);
                $response['data']  = $tab_ac->orderby('ac_seguimiento.tab_ac.id', 'ASC')->get()->toArray();
            } else {
                $response['success']  = 'true';
                $response['total'] = $tab_ac->count();
                $tab_ac->skip($start)->take($limit);
                $response['data']  = $tab_ac->orderby('ac_seguimiento.tab_ac.id', 'ASC')->get()->toArray();
            }

            return Response::json($response, 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return Response::json(array('success' => false, 'message' => utf8_encode($e->getMessage())), 500);
        }
    }

    /**
    * Display a listing of the resource.
    *
    * @return Response
    */
    public function detalle()
    {
        $data = tab_ac::join('mantenimiento.tab_ejecutores as t01', 'ac_seguimiento.tab_ac.id_tab_ejecutores', '=', 't01.id')
        ->join('mantenimiento.tab_lapso as t02', 'ac_seguimiento.tab_ac.id_tab_lapso', '=', 't02.id')
        ->select(
            'ac_seguimiento.tab_ac.id',
            'tx_ejecutor',
            'ac_seguimiento.tab_ac.id_tab_ejecutores',
            'ac_seguimiento.tab_ac.in_activo',
            DB::raw("to_char(t02.fe_inicio, 'dd/mm/YYYY') as fe_inicio"),
            DB::raw("to_char(t02.fe_fin, 'dd/mm/YYYY') as fe_fin"),
            'nu_codigo',
            'de_ac'
        )
        ->where('ac_seguimiento.tab_ac.id', '=', Input::get('codigo'))
        ->first();

        return View::make('seguimiento.ac.003.detalle')->with('data', $data);
    }

    /**
    * Display a listing of the resource.
    *
    * @return Response
    */
    public function datos($id)
    {
        $data = tab_ac::select(
            'id',
            'nu_codigo',
            'id_tab_ejecutores',
            'id_tab_ejercicio_fiscal',
            'id_tab_ac_predefinida',
            'id_tab_sectores',
            'id_tab_estatus',
            'id_tab_situacion_presupuestaria',
            'id_tab_tipo_registro',
            'co_new_etapa',
            'de_ac',
            'mo_ac',
            'mo_calculado',
            'fe_inicio',
            'fe_fin',
            'inst_mision',
            'inst_vision',
            'inst_objetivos',
            'nu_po_beneficiar',
            'nu_em_previsto',
            'tx_re_esperado',
            'in_activo',
            'id_tab_lapso'
        )
        ->where('id', '=', $id)
        ->first();

        return View::make('seguimiento.ac.003.datos.lista')->with('data', $data);
    }

    /**
    * Display a listing of the resource.
    *
    * @return Response
    */
    public function datosstoreLista()
    {
        try {
            $start  = Input::get('start', 0);
            $limit  = Input::get('limit', 20);
            $variable = Input::get('variable');

            $tab_ac = tab_ac_ae::select(
                'ac_seguimiento.tab_ac_ae.id',
                'id_tab_ac',
                'id_tab_ac_ae_predefinida',
                'id_tab_ejecutores',
                'bien_servicio',
                'id_tab_unidad_medida',
                'meta',
                'ponderacion',
                'id_tab_tipo_fondo',
                'mo_ae',
                'mo_ae_calculado',
                'ac_seguimiento.tab_ac_ae.in_activo',
                'nu_numero',
                'de_nombre',
                'de_unidad_medida',
                'tx_ejecutor',
                DB::raw("to_char(fecha_inicio, 'dd-mm-YYYY') as fecha_inicio"),
                DB::raw("to_char(fecha_fin, 'dd-mm-YYYY') as fecha_fin")
            )
            ->join('mantenimiento.tab_ac_ae_predefinida as t01', 'ac_seguimiento.tab_ac_ae.id_tab_ac_ae_predefinida', '=', 't01.id')
            ->join('mantenimiento.tab_unidad_medida as t02', 'ac_seguimiento.tab_ac_ae.id_tab_unidad_medida', '=', 't02.id')
            ->join('mantenimiento.tab_ejecutores as t03', 'ac_seguimiento.tab_ac_ae.id_tab_ejecutores', '=', 't03.id')
            ->where('id_tab_ac', '=', Input::get('ac'))
            ->where('ac_seguimiento.tab_ac_ae.in_activo', '=', true);

            if (Input::get("BuscarBy")=="true") {

                if($variable!="") {
                    $tab_ac->where('de_nombre', 'ILIKE', "%$variable%");
                }

                $response['success']  = 'true';
                $response['total'] = $tab_ac->count();
                $tab_ac->skip($start)->take($limit);
                $response['data']  = $tab_ac->orderby('ac_seguimiento.tab_ac_ae.id', 'ASC')->get()->toArray();
            } else {
                $response['success']  = 'true';
                $response['total'] = $tab_ac->count();
                $tab_ac->skip($start)->take($limit);
                $response['data']  = $tab_ac->orderby('ac_seguimiento.tab_ac_ae.id', 'ASC')->get()->toArray();
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
    public function editar($id)
    {
        $data = tab_ac_ae::select(
            'id',
            'id_tab_ac',
            'id_tab_ac_ae_predefinida',
            'id_tab_ejecutores',
            'bien_servicio',
            'id_tab_unidad_medida',
            'meta',
            'ponderacion',
            'id_tab_tipo_fondo',
            'mo_ae',
            'mo_ae_calculado',
            'fecha_inicio',
            'fecha_fin',
            'in_activo'
        )
        ->where('id', '=', $id)
        ->first();
        return View::make('seguimiento.ac.003.actividad.lista')->with('data', $data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function actividadstoreLista()
    {
        try {
            $start  = Input::get('start', 0);
            $limit  = Input::get('limit', 20);
            $variable = Input::get('variable');

            $tab_meta_financiera = tab_meta_financiera::select(
                'ac_seguimiento.tab_meta_financiera.id',
                'id_tab_meta_fisica',
                'ac_seguimiento.tab_meta_financiera.id_tab_municipio_detalle',
                'ac_seguimiento.tab_meta_financiera.id_tab_parroquia_detalle',
                'mo_presupuesto',
                'co_partida',
                'id_tab_fuente_financiamiento',
                'ac_seguimiento.tab_meta_financiera.in_activo',
                'ac_seguimiento.tab_meta_financiera.in_cargado',
                'codigo',
                'nb_meta',
                'de_fuente_financiamiento',
                'nu_numero',
                'nu_original',
                'co_sector'
            )
             ->join('ac_seguimiento.tab_meta_fisica as t01', 'ac_seguimiento.tab_meta_financiera.id_tab_meta_fisica', '=', 't01.id')
             ->join('mantenimiento.tab_fuente_financiamiento as t02', 'ac_seguimiento.tab_meta_financiera.id_tab_fuente_financiamiento', '=', 't02.id')
             ->join('ac_seguimiento.tab_ac_ae as t03', 't01.id_tab_ac_ae', '=', 't03.id')
             ->join('mantenimiento.tab_ac_ae_predefinida as t04', 't03.id_tab_ac_ae_predefinida', '=', 't04.id')
             ->join('ac_seguimiento.tab_ac as t05', 't03.id_tab_ac', '=', 't05.id')
             ->join('mantenimiento.tab_ac_predefinida as t06', 't05.id_tab_ac_predefinida', '=', 't06.id')
             ->join('mantenimiento.tab_sectores as t07', 't05.id_tab_sectores', '=', 't07.id')
             ->where('id_tab_ac_ae', '=', Input::get('ac_ae'));

            if (Input::get("BuscarBy")=="true") {

                if($variable!="") {
                    $tab_meta_financiera->where('nb_meta', 'ILIKE', "%$variable%");
                }

                $response['success']  = 'true';
                $response['total'] = $tab_meta_financiera->count();
                $tab_meta_financiera->skip($start)->take($limit);
                $response['data']  = $tab_meta_financiera->orderby('ac_seguimiento.tab_meta_financiera.id', 'ASC')->get()->toArray();
            } else {
                $response['success']  = 'true';
                $response['total'] = $tab_meta_financiera->count();
                $tab_meta_financiera->skip($start)->take($limit);
                $response['data']  = $tab_meta_financiera->orderby('ac_seguimiento.tab_meta_financiera.id', 'ASC')->get()->toArray();
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
    public function editarActividad($id)
    {
        $data = tab_meta_financiera::select(
            'ac_seguimiento.tab_meta_financiera.id',
            'id_tab_meta_fisica',
            'ac_seguimiento.tab_meta_financiera.id_tab_municipio_detalle',
            'ac_seguimiento.tab_meta_financiera.id_tab_parroquia_detalle',
            'mo_presupuesto',
            'co_partida',
            'id_tab_fuente_financiamiento',
            'ac_seguimiento.tab_meta_financiera.in_activo',
            'ac_seguimiento.tab_meta_financiera.in_cargado',
            'codigo',
            'nb_meta',
            'de_fuente_financiamiento',
            'nu_numero',
            'nu_original',
            'co_sector',
            'mo_modificado_anual',
            'mo_actualizado_anual',
            'mo_comprometido',
            'mo_causado',
            'mo_pagado',
            DB::raw("to_char(t01.fecha_inicio, 'dd-mm-YYYY') as fecha_inicio"),
            DB::raw("to_char(t01.fecha_fin, 'dd-mm-YYYY') as fecha_fin")
        )
         ->join('ac_seguimiento.tab_meta_fisica as t01', 'ac_seguimiento.tab_meta_financiera.id_tab_meta_fisica', '=', 't01.id')
         ->join('mantenimiento.tab_fuente_financiamiento as t02', 'ac_seguimiento.tab_meta_financiera.id_tab_fuente_financiamiento', '=', 't02.id')
         ->join('ac_seguimiento.tab_ac_ae as t03', 't01.id_tab_ac_ae', '=', 't03.id')
         ->join('mantenimiento.tab_ac_ae_predefinida as t04', 't03.id_tab_ac_ae_predefinida', '=', 't04.id')
         ->join('ac_seguimiento.tab_ac as t05', 't03.id_tab_ac', '=', 't05.id')
         ->join('mantenimiento.tab_ac_predefinida as t06', 't05.id_tab_ac_predefinida', '=', 't06.id')
         ->join('mantenimiento.tab_sectores as t07', 't05.id_tab_sectores', '=', 't07.id')
        ->where('ac_seguimiento.tab_meta_financiera.id', '=', $id)
        ->first();

        return View::make('seguimiento.ac.003.actividad.editar')->with('data', $data);
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
                $validator= Validator::make(Input::all(), tab_meta_financiera::$validarEditar);
                if ($validator->fails()) {
                    return Response::json(array(
                      'success' => false,
                      'msg' => $validator->getMessageBag()->toArray()
                    ));
                }
                $tabla = tab_meta_financiera::find($id);
                $tabla->mo_modificado_anual = Input::get("modificado_anual");
                $tabla->mo_actualizado_anual = $tabla->mo_presupuesto + Input::get("modificado_anual");
                $tabla->mo_comprometido = Input::get("comprometido");
                $tabla->mo_causado = Input::get("causado");
                $tabla->mo_pagado = Input::get("pagado");
                $tabla->in_cargado = true;
                $tabla->save();
                
                $data = tab_meta_fisica::join('ac_seguimiento.tab_ac_ae as t05', 'tab_meta_fisica.id_tab_ac_ae', '=', 't05.id')
                ->join('ac_seguimiento.tab_ac as t01', 't05.id_tab_ac', '=', 't01.id')
                ->select(
                    't01.id',
                    'id_tab_ac_ae'    
                )
                ->where('tab_meta_fisica.id', '=', $tabla->id_tab_meta_fisica)
                ->first();        
                
                $cant = tab_meta_fisica::join('ac_seguimiento.tab_meta_financiera as t01', 't01.id_tab_meta_fisica', '=', 'tab_meta_fisica.id')
                ->where('id_tab_ac_ae', '=', $data->id_tab_ac_ae)
                ->where('t01.in_cargado', '=', false)
                ->count(); 
                
//                        var_dump($cant);
//        exit();
                
                if($cant==0){
                    
                $tabla = tab_ac::find($data->id);
                $tabla->in_003 = true;
                $tabla->save();
                
                }

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
                $validator = Validator::make(Input::all(), tab_meta_financiera::$validarCrear);
                if ($validator->fails()) {
                    return Response::json(array(
                      'success' => false,
                      'msg' => $validator->getMessageBag()->toArray()
                    ));
                }
                $tabla = new tab_meta_financiera();
                $tabla->mo_modificado_anual = Input::get("modificado_anual");
                $tabla->mo_actualizado_anual = Input::get("actualizado_anual");
                $tabla->mo_comprometido = Input::get("comprometido");
                $tabla->mo_causado = Input::get("causado");
                $tabla->mo_pagado = Input::get("pagado");
                $tabla->in_activo = 'TRUE';
                $tabla->save();

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

}
