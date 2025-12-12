<?php

namespace matriz\Models\Ac;

use Illuminate\Database\Eloquent\Model;

class t83_foco_accion extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'local';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 't83_foco_accion';
    
    protected $primaryKey = 'id';

    public $incrementing = true;
}
