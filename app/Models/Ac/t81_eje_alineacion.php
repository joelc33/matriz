<?php

namespace matriz\Models\Ac;

use Illuminate\Database\Eloquent\Model;

class t81_eje_alineacion extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'local';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 't81_eje_alineacion';
    
    protected $primaryKey = 'id';

    public $incrementing = true;
}
