<?php

namespace matriz\Models\Ac;

use Illuminate\Database\Eloquent\Model;

class t82_linea_impulso extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'local';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 't82_linea_impulso';
    
    protected $primaryKey = 'id';

    public $incrementing = true;
}
