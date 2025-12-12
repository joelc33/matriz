<?php

namespace matriz\Models\Ac;

use Illuminate\Database\Eloquent\Model;

class t80_transformaciones extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'local';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 't80_transformaciones';
    
    protected $primaryKey = 'id';

    public $incrementing = true;
}
