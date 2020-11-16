<?php

namespace matriz\Console\Commands;
//*******agregar esta linea******//
use matriz\Models\Ac\tab_ac;
use matriz\Models\Ac\t49_ac_planes;
use matriz\Models\Ac\t50_ac_localizacion;
use matriz\Models\Ac\tab_ac_responsable;
use matriz\Models\Ac\tab_ac_ae;
use matriz\Models\Ac\tab_ac_ae_partida;
use matriz\Models\Ac\t56_ac_ae_fuente;
use matriz\Models\Ac\tab_meta_fisica;
use matriz\Models\Ac\tab_meta_financiera;
use matriz\Models\Ac\tab_partida_importar;
use DB;
//*******************************//
use Illuminate\Console\Command;

class cargaMasiva extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'matriz:cargaMasiva { ejercicio : Ejercicio a cargar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para carga masiva de partidas';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $ejercicio = $this->argument('ejercicio');

        DB::beginTransaction();
        try {

            $tab_partida_importar = tab_partida_importar::select('id', 'ejercicio_fiscal', 'codigo_ejecutor', 'descripcion_ejecutor', 
            'codigo_ac', 'descripcion_ac', 'codigo_ae', 'descripcion_ae', 'codigo_partida', 
            'descripcion_partida', 'monto_partida', 'in_cargado', 'created_at', 'updated_at', 
            'id_accion_centralizada', 'nu_ac')
            ->where('ejercicio_fiscal', $ejercicio)
            ->orderby('id','ASC')
            ->get();

            foreach ($tab_partida_importar as $lista){

                $codigo_ac = substr($lista->codigo_ac, -1);

                $update = tab_partida_importar::where('ejercicio_fiscal', $ejercicio)
                ->where('id', $lista->id)
                ->update(['nu_ac' => $codigo_ac]);

                $update = tab_partida_importar::where('ejercicio_fiscal', $ejercicio)->
                update(['in_cargado' => false]);

                DB::commit();

                $this->info('AC: '.$lista->codigo_ac.' actualizado.');

            }

            foreach ($tab_partida_importar as $lista){

                $tab_ac = tab_ac::where('id_ejercicio', $lista->ejercicio_fiscal)
                ->where('id_accion', $lista->nu_ac)
                ->where('id_ejecutor', $lista->codigo_ejecutor)
                ->first();

                $update = tab_partida_importar::where('id', $lista->id)
                ->update(['id_accion_centralizada' => $tab_ac->id]);

                DB::commit();

                $this->info('AC: '.$lista->codigo_ac.' actualizado.');

            }

            foreach ($tab_partida_importar as $lista_ac){

                $borrar_ac_ae_partida = tab_ac_ae_partida::where('id_accion_centralizada', '=', $lista_ac->id_accion_centralizada )
                ->where('id_accion', '=', $lista_ac->nu_ac )->delete();

                DB::commit();

                $this->info('AC: '.$lista_ac->nu_ac.' limpiado.');

            }

            $tab_partida_importar = tab_partida_importar::select('id_accion_centralizada', 'nu_ac', 'codigo_partida', 'ejercicio_fiscal', 'descripcion_partida', 
            DB::raw('sum(monto_partida) as monto_partida') )
            ->groupBy('ejercicio_fiscal')
            ->groupBy('id_accion_centralizada')
            ->groupBy('nu_ac')
            ->groupBy('codigo_partida')
            ->groupBy('descripcion_partida')
            ->orderBy('id_accion_centralizada','ASC')
            ->get();

            foreach ($tab_partida_importar as $lista_ac_ae_partida){

                $replica_ac_ae_partida = new tab_ac_ae_partida;
                $replica_ac_ae_partida->id_accion_centralizada = $lista_ac_ae_partida->id_accion_centralizada;
                $replica_ac_ae_partida->id_accion = $lista_ac_ae_partida->nu_ac;
                $replica_ac_ae_partida->co_partida = trim($lista_ac_ae_partida->codigo_partida);
                $replica_ac_ae_partida->monto = $lista_ac_ae_partida->monto_partida;
                $replica_ac_ae_partida->edo_reg = true;
                $replica_ac_ae_partida->id_tab_ejercicio_fiscal = $lista_ac_ae_partida->ejercicio_fiscal;
                $replica_ac_ae_partida->de_denominacion = $lista_ac_ae_partida->descripcion_partida;
                $replica_ac_ae_partida->save();

                $update = tab_partida_importar::where('id_accion_centralizada', $lista_ac_ae_partida->id_accion_centralizada)
                ->where('nu_ac', $lista_ac_ae_partida->nu_ac)
                ->where('codigo_partida', $lista_ac_ae_partida->codigo_partida)
                ->update(['in_cargado' => true]);

                DB::commit();

                $this->info('AC: '.$lista_ac_ae_partida->nu_ac.' cargado.');

            }

        }catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            $this->info(utf8_encode( $e->getMessage()));
        }
    }
}
