<?php

namespace matriz\Models\Ac;

use Illuminate\Database\Eloquent\Model;

class tab_ac extends Model
{
  //Nombre de la conexion que utitlizara este modelo
	protected $connection= 'local';

	//Todos los modelos deben extender la clase Eloquent
	protected $table = 't46_acciones_centralizadas';

	protected $primaryKey = 'id';
	//public $timestamps = false;
	public $incrementing = true;

	/**
	 * The name of the "created at" column.
	 */
	const CREATED_AT = 'fecha_creacion';

	/**
	 * The name of the "updated at" column.
	 */
	const UPDATED_AT = 'fecha_actualizacion';
	
}
