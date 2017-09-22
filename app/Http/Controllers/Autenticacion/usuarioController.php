<?php

namespace matriz\Http\Controllers\Autenticacion;
//*******agregar esta linea******//
use matriz\Models\Autenticacion\tab_usuarios;
use matriz\Models\Mantenimiento\tab_funcionario;
use matriz\Models\Autenticacion\tab_rol;
use matriz\Models\Mantenimiento\tab_cargo;
use matriz\Models\Mantenimiento\tab_documento;
use matriz\Models\Autenticacion\tab_usuario_rol;
use matriz\Models\Mantenimiento\tab_ejecutores;
use View;
use Validator;
use Input;
use Response;
use DB;
use Auth;
use Crypt;
use Mail;
use Hash;
use Session;
//*******************************//
use Illuminate\Http\Request;

use matriz\Http\Requests;
use matriz\Http\Controllers\Controller;

class usuarioController extends Controller
{
    protected $tab_usuarios;

    public function __construct(tab_usuarios $tab_usuarios)
    {
      $this->middleware('auth');
      $this->tab_usuarios = $tab_usuarios;
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    protected function genera_clave(){
      $cadena = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
      $longitudCadena=strlen($cadena);
      $pass = "";
      $longitudPass=8;
      for($i=1 ; $i<=$longitudPass ; $i++){
        $pos=rand(0,$longitudCadena-1);
        $pass .= substr($cadena,$pos,1);
      }
      return $pass;
    }

    function acomodar($string) {
      $string =ucwords(strtolower($string));

      foreach (array('-', '\'') as $delimiter) {
        if (strpos($string, $delimiter)!==false) {
          $string =implode($delimiter, array_map('ucfirst', explode($delimiter, $string)));
        }
      }
      return $string;
    }
    /**
  	* Display a listing of the resource.
  	*
  	* @return Response
  	*/
  	public function contrasena()
  	{
  		return View::make('autenticar.usuario.contrasena');
  	}

    /**
  	 * Update the specified resource in storage.
  	 *
  	 * @param  int  $id
  	 * @return Response
  	 */
  	public function cambioContrasena()
  	{
  	DB::beginTransaction();
  	   try {

  		$clave_actual = tab_usuarios::find( Auth::user()->id );
  		$valido = Hash::check(Input::get("contraseña_actual"), $clave_actual->da_password);

  		$datos = array(
  			"valido" => $valido,
  			"contraseña_actual" => Input::get("contraseña_actual"),
  			"contraseña" => Input::get("contraseña"),
  			"contraseña_confirmation" => Input::get("contraseña_confirmation")
  		);
  		$mensajes = array(
  			'valido.in'=>'La Contraseña ingresada no coincide.'
  		);

  		$validator = Validator::make($datos, tab_usuarios::$validarContrasena, $mensajes);
  		if ($validator->fails()) {
  			return Response::json(array(
  				'success' => false,
  				'msg' => $validator->getMessageBag()->toArray()
  			));
  		}

  		$usuario = tab_usuarios::find( Auth::user()->id );
  		$usuario->da_password = bcrypt(Input::get("contraseña"));
  		$usuario->da_pass_recuperar = Crypt::encrypt(Input::get("contraseña"));
  		$usuario->save();

  		//DB::commit();

  		$cuentaUsr = tab_usuarios::findOrFail(Auth::user()->id);

  		/*try{
  			Mail::send('correo.cambioContrasena', ['usuario' => $cuentaUsr ], function ($message) use ($cuentaUsr) {
  				$message->to($cuentaUsr->da_email, $cuentaUsr->da_email)
  				->subject('SEDATEZ - CAMBIO DE CONTRASEÑA');
  			});

  		}catch(\Exception $e){

  			return Response::json(array(
  				'success' => false,
  				'msg' => array('ERROR ('.$e->getCode().'):'=> 'Error al enviar Correo Electronico. Intente mas tarde.')
  			));
  		}*/

  		DB::commit();

  		return Response::json(array(
  			'success' => true,
  			'msg' => 'La Contraseña se cambio Satisfactoriamente!'
  		));

  	      }catch (\Illuminate\Database\QueryException $e)
  	      {
  			DB::rollback();
  			return Response::json(array(
  				'success' => false,
  				'msg' => array('ERROR ('.$e->getCode().'):'=> $e->getMessage())
  			));
  	      }
  	}

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function datos()
    {
      $data = tab_usuarios::join('autenticacion.tab_usuario_rol as t01', 'autenticacion.tab_usuarios.id', '=', 't01.id_tab_usuarios')
      ->join('mantenimiento.tab_funcionario as t02', 'autenticacion.tab_usuarios.id', '=', 't02.id_tab_usuarios')
      ->select('da_login', 'da_email', 'id_tab_rol','t02.id as id_funcionario', 'id_tab_documento', 'nu_cedula', 'nb_funcionario', 'ap_funcionario', 'id_tab_cargo', 'tx_direccion', 'tx_telefono')
      ->where('autenticacion.tab_usuarios.id', '=', Auth::user()->id)
      ->first();

      $ejecutor = tab_ejecutores::select('id', 'de_correo', 'de_telefono', 'in_verificado')
      ->where('id_ejecutor', '=', Session::get('ejecutor'))
      ->first();

      return View::make('autenticar.usuario.datos')->with('data', $data)->with('ejecutor', $ejecutor);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function cambios()
    {
    DB::beginTransaction();
       try {
      $validarFuncionario = Validator::make(Input::all(), tab_funcionario::$validarEditar);
      if ($validarFuncionario->fails()) {
        return Response::json(array(
          'success' => false,
          'msg' => $validarFuncionario->getMessageBag()->toArray()
        ));
      }
      $usuario = tab_usuarios::find(Auth::user()->id);
      $usuario->da_email = Input::get("correo_funcionario");
      $usuario->save();

      $usuario_funcionario = tab_funcionario::find(Input::get("id_funcionario"));
      $usuario_funcionario->id_tab_documento = Input::get("documenton");
      $usuario_funcionario->nu_cedula = Input::get("cedula");
      $usuario_funcionario->nb_funcionario = Input::get("nombre");
      $usuario_funcionario->ap_funcionario = Input::get("apellido");
      $usuario_funcionario->id_tab_cargo = Input::get("cargo");
      $usuario_funcionario->tx_direccion = Input::get("direccion");
      $usuario_funcionario->tx_telefono = Input::get("telefono_funcionario");
      $usuario_funcionario->tx_email = Input::get("correo_funcionario");
      $usuario_funcionario->save();

      $tabla = tab_ejecutores::updateOrCreate(array('id_ejecutor' => Session::get('ejecutor')));
      $tabla->de_correo = Input::get("correo");
      $tabla->de_telefono = Input::get("telefono");
      $tabla->in_verificado = true;
      $tabla->save();

      DB::commit();
      return Response::json(array(
        'success' => true,
        'msg' => 'Datos Editados con Exito!'
      ));

          }catch (\Illuminate\Database\QueryException $e)
          {
        DB::rollback();
        return Response::json(array(
          'success' => false,
          'msg' => array('ERROR ('.$e->getCode().'):'=> $e->getMessage())
        ));
          }
    }
}
