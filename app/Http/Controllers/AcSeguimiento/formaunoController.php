<?php

namespace matriz\Http\Controllers\AcSeguimiento;

//*******agregar esta linea******//
use matriz\Models\AcSegto\tab_ac;
use matriz\Models\AcSegto\tab_forma_001;
use matriz\Models\Mantenimiento\tab_lapso;
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

class formaunoController extends Controller
{
    protected $tab_ac;
    protected $tab_forma_001;

    public function __construct(tab_ac $tab_ac, tab_forma_001 $tab_forma_001)
    {
        $this->middleware('auth');
        $this->tab_ac = $tab_ac;
        $this->tab_forma_001 = $tab_forma_001;
    }

    /**
    * Display a listing of the resource.
    *
    * @return Response
    */
    public function lista($id)
    {
        $data = tab_lapso::select(
                'id',
                DB::raw("NOW() between fe_inicio and fe_fin as activo")
                )
        ->where('id', '=', $id)
        ->first();
        
        return View::make('seguimiento.ac.001.lista')->with('data', $data);
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
            $id_lapso = Input::get('id_lapso');

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
                DB::raw("NOW() between t02.fe_inicio and t02.fe_fin as activo"),
                'nu_codigo',
                'de_ac',
                'de_lapso',
                'in_001',
                'ac_seguimiento.tab_ac.id_ejecutor'
            )
            ->where('ac_seguimiento.tab_ac.id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
            ->where('t02.id', '=', $id_lapso)
            ->where('ac_seguimiento.tab_ac.in_activo', '=', true);

            $rol_planificador = array(3, 8);
            if (in_array(Session::get('rol'), $rol_planificador)) {
                $tab_ac->where('ac_seguimiento.tab_ac.id_tab_ejecutores', '=', Session::get('id_tab_ejecutores'));
            }

            if (Input::get("BuscarBy")=="true") {

                if($variable!="") {
                    $tab_ac->where('tx_ejecutor', 'ILIKE', "%$variable%");
                }

                $response['success']  = 'true';
                $response['total'] = $tab_ac->count();
                $tab_ac->skip($start)->take($limit);
                $response['data']  = $tab_ac->orderby('ac_seguimiento.tab_ac.id_ejecutor', 'ASC')->orderby('ac_seguimiento.tab_ac.id', 'ASC')->get()->toArray();
            } else {
                $response['success']  = 'true';
                $response['total'] = $tab_ac->count();
                $tab_ac->skip($start)->take($limit);
                $response['data']  = $tab_ac->orderby('ac_seguimiento.tab_ac.id_ejecutor', 'ASC')->orderby('ac_seguimiento.tab_ac.id', 'ASC')->get()->toArray();
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

        return View::make('seguimiento.ac.001.detalle')->with('data', $data);
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
            'nu_po_beneficiada',
            'nu_em_generado',                  
            'tx_re_esperado',
            'in_activo',
            'id_tab_lapso',
            'in_bloquear_001',
            'de_observacion_001'
        )
        ->where('id', '=', $id)
        ->first();

//        if (tab_forma_001::where('id_tab_ac', '=', $id)
//        ->where('id_tab_estatus', '=', 5)
//        ->where('in_001', '=', false)->exists()) {
//
//            $data = tab_forma_001::select(
//                'id_tab_ac as id',
//                'inst_mision',
//                'inst_vision',
//                'inst_objetivos',
//                'in_001',
//                'created_at',
//                'updated_at',
//                'de_observacion',
//                'id_usuario_solicita',
//                'id_usuario_procesa',
//                'nu_po_beneficiar',
//                'nu_em_previsto',
//                'nu_po_beneficiada',
//                'nu_em_generado',                    
//                'id_tab_estatus',
//                'in_activo as in_bloquear_001'
//            )
//            ->where('id_tab_ac', '=', $id)
//            ->where('id_tab_estatus', '=', 5)
//            ->where('in_001', '=', false)
//            ->first();
//
//        }

        //return View::make('seguimiento.ac.001.datos.lista')->with('data',$data);
        return View::make('seguimiento.ac.001.datos.editar')->with('data', $data);
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
                $validator= Validator::make(Input::all(), tab_ac::$validarEditar);
                if ($validator->fails()) {
                    return Response::json(array(
                      'success' => false,
                      'msg' => $validator->getMessageBag()->toArray()
                    ));
                }
                

                $tabla = tab_ac::find($id);
                $tabla->inst_mision = Input::get("mision");
                $tabla->inst_vision = Input::get("vision");
                $tabla->inst_objetivos = Input::get("objetivos");
                $tabla->save();
                
                $data = tab_ac::select(
                    'id'
                )
                ->where('id_ejecutor', '=', $tabla->id_ejecutor)
                ->where('id_tab_ejercicio_fiscal', '=', $tabla->id_tab_ejercicio_fiscal)
                ->where('id_tab_lapso', '=', $tabla->id_tab_lapso)
                ->get();                

                foreach ($data as $lista){
  
                $tabla_ac = tab_ac::find($lista->id);
                $tabla_ac->inst_mision = Input::get("mision");
                $tabla_ac->inst_vision = Input::get("vision");
                $tabla_ac->inst_objetivos = Input::get("objetivos");
                $tabla_ac->save(); 
                
                $data2 = tab_forma_001::select(
                    'id'
                )
                ->where('id_tab_ac', '=', $lista->id)
                ->first(); 

                if($data2){
                $tabla_001 = tab_forma_001::find($data2->id);
                $tabla_001->inst_mision = Input::get("mision");
                $tabla_001->inst_vision = Input::get("vision");
                $tabla_001->inst_objetivos = Input::get("objetivos");
                $tabla_001->save(); 
                }
                    
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
                $validator = Validator::make(Input::all(), tab_ac::$validarCrear);
                if ($validator->fails()) {
                    return Response::json(array(
                      'success' => false,
                      'msg' => $validator->getMessageBag()->toArray()
                    ));
                }
                $tabla = new tab_ac();
                $tabla->inst_mision = Input::get("mision");
                $tabla->inst_vision = Input::get("vision");
                $tabla->inst_objetivos = Input::get("objetivos");
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

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function enviar($id = null)
    {
        DB::beginTransaction();
        if($id!=''||$id!=null) {

            try {
                $validator= Validator::make(Input::all(), tab_ac::$validarEditar);
                if ($validator->fails()) {
                    return Response::json(array(
                      'success' => false,
                      'msg' => $validator->getMessageBag()->toArray()
                    ));
                }
                
                
                $tabla = tab_ac::find($id);
                $tabla->in_bloquear_001 = true;
                $tabla->save();

                $data = tab_ac::select(
                    'id'
                )
                ->where('id_ejecutor', '=', $tabla->id_ejecutor)
                ->where('id_tab_ejercicio_fiscal', '=', $tabla->id_tab_ejercicio_fiscal)
                ->where('id_tab_lapso', '=', $tabla->id_tab_lapso)
                ->get();                        
                
                
                foreach ($data as $lista){
                    

                $tabla_ac = tab_ac::find($lista->id);
                $tabla_ac->in_bloquear_001 = true;
                $tabla_ac->inst_mision = Input::get("mision");
                $tabla_ac->inst_vision = Input::get("vision");
                $tabla_ac->inst_objetivos = Input::get("objetivos");                
                $tabla_ac->save();  
                
                $tabla_001 = new tab_forma_001();
                $tabla_001->id_tab_ac = $lista->id;
                $tabla_001->inst_mision = Input::get("mision");
                $tabla_001->inst_vision = Input::get("vision");
                $tabla_001->inst_objetivos = Input::get("objetivos");
                $tabla_001->in_001 = false;
                $tabla_001->id_usuario_solicita = Auth::user()->id;
                $tabla_001->in_activo = true;
                $tabla_001->id_tab_estatus = 5;
                $tabla_001->save();                
                    
                }                 
                


                DB::commit();
                return Response::json(array(
                  'success' => true,
                  'msg' => 'Datos enviados con Exito!'
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
                $validator = Validator::make(Input::all(), tab_ac::$validarCrear);
                if ($validator->fails()) {
                    return Response::json(array(
                      'success' => false,
                      'msg' => $validator->getMessageBag()->toArray()
                    ));
                }
                $tabla = new tab_forma_001();
                $tabla->inst_mision = Input::get("mision");
                $tabla->inst_vision = Input::get("vision");
                $tabla->inst_objetivos = Input::get("objetivos");
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

    /**
    * Display a listing of the resource.
    *
    * @return Response
    */
    public function listaCambio()
    {
        return View::make('seguimiento.ac.001.cambio.lista');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function storeListaCambio()
    {
        try {
            $start  = Input::get('start', 0);
            $limit  = Input::get('limit', 20);
            $variable = Input::get('variable');

            $tab_forma_001 = $this->tab_forma_001
            ->join('ac_seguimiento.tab_ac as t01', 'ac_seguimiento.tab_forma_001.id_tab_ac', '=', 't01.id')
            ->join('mantenimiento.tab_ejecutores as t02', 't01.id_tab_ejecutores', '=', 't02.id')
            ->join('mantenimiento.tab_lapso as t03', 't01.id_tab_lapso', '=', 't03.id')
            ->join('mantenimiento.tab_estatus as t04', 't04.id', '=', 'ac_seguimiento.tab_forma_001.id_tab_estatus')
            ->select(
                'ac_seguimiento.tab_forma_001.id',
                'ac_seguimiento.tab_forma_001.id_tab_ac',
                'tx_ejecutor',
                't01.id_tab_ejecutores',
                't02.in_activo',
                'de_estatus',
                DB::raw("to_char(t03.fe_inicio, 'dd/mm/YYYY') as fe_inicio"),
                DB::raw("to_char(t03.fe_fin, 'dd/mm/YYYY') as fe_fin"),
                'nu_codigo',
                'de_ac',
                'de_lapso',
                'ac_seguimiento.tab_forma_001.in_001',
                't01.id_ejecutor',
                DB::raw("to_char(ac_seguimiento.tab_forma_001.created_at, 'dd/mm/YYYY hh12:mi AM') as fe_solicitud")
            )
            ->where('t01.id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
            ->where('t01.in_activo', '=', true);

            $rol_planificador = array(3, 8);
            if (in_array(Session::get('rol'), $rol_planificador)) {
                $tab_forma_001->where('t01.id_tab_ejecutores', '=', Session::get('id_tab_ejecutores'));
            }

            if (Input::get("BuscarBy")=="true") {

                if($variable!="") {
                    $tab_forma_001->where('tx_ejecutor', 'ILIKE', "%$variable%");
                }

                $response['success']  = 'true';
                $response['total'] = $tab_forma_001->count();
                $tab_forma_001->skip($start)->take($limit);
                $response['data']  = $tab_forma_001->orderby('t01.id_ejecutor', 'ASC')->orderby('ac_seguimiento.tab_forma_001.id', 'ASC')->get()->toArray();
            } else {
                $response['success']  = 'true';
                $response['total'] = $tab_forma_001->count();
                $tab_forma_001->skip($start)->take($limit);
                $response['data']  = $tab_forma_001->orderby('t01.id_ejecutor', 'ASC')->orderby('ac_seguimiento.tab_forma_001.id', 'ASC')->get()->toArray();
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
    public function detalleCambio()
    {
        $data = tab_forma_001::join('ac_seguimiento.tab_ac as t01', 'ac_seguimiento.tab_forma_001.id_tab_ac', '=', 't01.id')
        ->join('mantenimiento.tab_ejecutores as t02', 't01.id_tab_ejecutores', '=', 't02.id')
        ->join('mantenimiento.tab_lapso as t03', 't01.id_tab_lapso', '=', 't03.id')
        ->join('autenticacion.tab_usuarios as t04a', 'ac_seguimiento.tab_forma_001.id_usuario_solicita', '=', 't04a.id')
        ->leftJoin('autenticacion.tab_usuarios as t04b', 'ac_seguimiento.tab_forma_001.id_usuario_procesa', '=', 't04b.id')
        ->select(
            'ac_seguimiento.tab_forma_001.id',
            'tx_ejecutor',
            't01.id_tab_ejecutores',
            't02.in_activo',
            't04a.da_login as da_login_a',
            't04b.da_login as da_login_b',
            DB::raw("to_char(t03.fe_inicio, 'dd/mm/YYYY') as fe_inicio"),
            DB::raw("to_char(t03.fe_fin, 'dd/mm/YYYY') as fe_fin"),
            'nu_codigo',
            'de_observacion',
            'de_ac',
            'ac_seguimiento.tab_forma_001.in_001',
            't01.id_ejecutor',
            DB::raw("to_char(ac_seguimiento.tab_forma_001.created_at, 'dd/mm/YYYY hh12:mi AM') as fe_solicitud")
        )
        ->where('ac_seguimiento.tab_forma_001.id', '=', Input::get('codigo'))
        ->first();

        return View::make('seguimiento.ac.001.cambio.detalle')->with('data', $data);
    }

    /**
    * Display a listing of the resource.
    *
    * @return Response
    */
    public function datosCambio($id)
    {
        $data = tab_forma_001::select(
            'id',
            'id_tab_ac',
            'inst_mision',
            'inst_vision',
            'inst_objetivos',
            'in_activo',
            'in_001',
            'created_at',
            'updated_at',
            'de_observacion',
            'nu_po_beneficiar',
            'nu_em_previsto',
            'nu_po_beneficiada',
            'nu_em_generado',                
            'id_usuario_solicita',
            'id_usuario_procesa'
        )
        ->where('id', '=', $id)
        ->first();

        return View::make('seguimiento.ac.001.cambio.editar')->with('data', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function aprobar($id = null)
    {
        DB::beginTransaction();
        if($id!=''||$id!=null) {

            try {
                $validator= Validator::make(Input::all(), tab_ac::$validarEditar);
                if ($validator->fails()) {
                    return Response::json(array(
                      'success' => false,
                      'msg' => $validator->getMessageBag()->toArray()
                    ));
                }
                $tabla = tab_ac::find(Input::get("ac"));
                $tabla->in_001 = true;
                $tabla->save();

                $data = tab_ac::select(
                    'id'
                )
                ->where('id_ejecutor', '=', $tabla->id_ejecutor)
                ->where('id_tab_ejercicio_fiscal', '=', $tabla->id_tab_ejercicio_fiscal)
                ->where('id_tab_lapso', '=', $tabla->id_tab_lapso)
                ->get();                        
                

                foreach ($data as $lista){
                    

                $tabla_ac = tab_ac::find($lista->id);
                $tabla_ac->in_001 = true;
                $tabla_ac->save();  
 
                $data2 = tab_forma_001::select(
                    'id'
                )
                ->where('id_tab_ac', '=', $lista->id)
                ->first();                 
              
                $tabla_001 = tab_forma_001::find($data2->id);
                $tabla_001->in_001 = true;
                $tabla_001->id_tab_estatus = 6;
                $tabla_001->id_usuario_procesa = Auth::user()->id;
                $tabla_001->save();              
                    
                }                
                


                DB::commit();
                return Response::json(array(
                  'success' => true,
                  'msg' => 'Datos aprobados con Exito!'
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
                $validator = Validator::make(Input::all(), tab_ac::$validarCrear);
                if ($validator->fails()) {
                    return Response::json(array(
                      'success' => false,
                      'msg' => $validator->getMessageBag()->toArray()
                    ));
                }
                $tabla = new tab_forma_001();
                $tabla->inst_mision = Input::get("mision");
                $tabla->inst_vision = Input::get("vision");
                $tabla->inst_objetivos = Input::get("objetivos");
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


    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function negar($id = null)
    {
        DB::beginTransaction();
        if($id!=''||$id!=null) {

            try {
                $validator= Validator::make(Input::all(), tab_ac::$validarEditar);
                if ($validator->fails()) {
                    return Response::json(array(
                      'success' => false,
                      'msg' => $validator->getMessageBag()->toArray()
                    ));
                }
                $tabla = tab_ac::find(Input::get("ac"));
                $tabla->in_001 = false;
                $tabla->in_bloquear_001 = false;
                $tabla->save();
                
                $data = tab_ac::select(
                    'id'
                )
                ->where('id_ejecutor', '=', $tabla->id_ejecutor)
                ->where('id_tab_ejercicio_fiscal', '=', $tabla->id_tab_ejercicio_fiscal)
                ->where('id_tab_lapso', '=', $tabla->id_tab_lapso)
                ->get();                        
                
                
                foreach ($data as $lista){
                    

                $tabla_ac = tab_ac::find($lista->id);
                $tabla_ac->in_001 = false;
                $tabla_ac->in_bloquear_001 = false;
                $tabla_ac->save();  

                $data2 = tab_forma_001::select(
                    'id'
                )
                ->where('id_tab_ac', '=', $lista->id)
                ->first();                                 
                
                $tabla_001 = tab_forma_001::find($data2->id);
                $tabla_001->in_001 = true;
                $tabla_001->id_tab_estatus = 7;
                $tabla_001->id_usuario_procesa = Auth::user()->id;
                $tabla_001->save();             
                    
                }                



                DB::commit();
                return Response::json(array(
                  'success' => true,
                  'msg' => 'Solicitud procesada con Exito!'
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
                $validator = Validator::make(Input::all(), tab_ac::$validarCrear);
                if ($validator->fails()) {
                    return Response::json(array(
                      'success' => false,
                      'msg' => $validator->getMessageBag()->toArray()
                    ));
                }
                $tabla = new tab_forma_001();
                $tabla->inst_mision = Input::get("mision");
                $tabla->inst_vision = Input::get("vision");
                $tabla->inst_objetivos = Input::get("objetivos");
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

    public function eliminar()
    {
        DB::beginTransaction();
        try {
            $tabla = tab_ac::find(Input::get("id"));
            $tabla->in_activo = false;
            $tabla->save();

            DB::commit();

            $response['success']  = 'true';
            $response['msg']  = 'Registro borrado con Exito!';
            return Response::json($response, 200);

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            $response['success']  = 'false';
            $response['msg']  = array('ERROR ('.$e->getCode().'):'=> $e->getMessage());
            return Response::json($response, 200);
        }
    }    

}
