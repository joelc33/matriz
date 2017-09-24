<?php

namespace matriz\Models\Mantenimiento;

use Illuminate\Database\Eloquent\Model;

class tab_ac_ae_partida extends Model
{
  //Nombre de la conexion que utitlizara este modelo
	protected $connection= 'local';

	//Todos los modelos deben extender la clase Eloquent
	protected $table = 'mantenimiento.tab_ac_ae_partida';

	public static $validarCrear = array(
		"descripcion" => "required|min:1|max:1200"
	);

	public static $validarEditar = array(
		"descripcion" => "required|min:1|max:1200"
	);
}
