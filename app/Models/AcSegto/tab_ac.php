<?php

namespace matriz\Models\AcSegto;

use Illuminate\Database\Eloquent\Model;

class tab_ac extends Model
{
  //Nombre de la conexion que utitlizara este modelo
	protected $connection= 'local';

	//Todos los modelos deben extender la clase Eloquent
	protected $table = 'ac_seguimiento.tab_ac';

	public static $validarCrear = array(
		"ejercicio" => "required|numeric",
		"ac" => "required|numeric"
	);

	public static $validarEditar = array(
		"mision" => "required",
		"vision" => "required",
		"objetivos" => "required"
	);
}
