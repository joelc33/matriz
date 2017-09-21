<?php

namespace matriz\Models\Mantenimiento;

use Illuminate\Database\Eloquent\Model;

class tab_ejecutores extends Model
{
  //Nombre de la conexion que utitlizara este modelo
	protected $connection= 'local';

	//Todos los modelos deben extender la clase Eloquent
	protected $table = 'mantenimiento.tab_ejecutores';

	public static $datosEjecutor = array(
		"ejercicio" => "required|numeric|min:2015|max:3000",
		"correo"    => "required|email",
		"telefono" => "required|regex:/^([0-9]{4})([-]{1})([0-9]{7}$)/",
		"documenton"    => "required|integer|in:1,2,3",
		"cedula" => "required|integer|min:1000000|max:99999999|unique:tab_funcionario,nu_cedula",
		"nombre" => "required|min:2|max:50",
		"apellido" => "required|min:2|max:50",
		"correo_funcionario"    => "required|email|unique:tab_funcionario,tx_email",
		"telefono_funcionario"    => "required|regex:/^([0-9]{4})([-]{1})([0-9]{7}$)/"
	);
}
