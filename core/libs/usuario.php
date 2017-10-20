<?php

	/** @class: Usuario (PHP5-Strict with comments)
	 * @project: PHP clase persona
	 * @date: 03-16-2014
	 * @version: 0.0.1_php5
	 * @author: Victor Eduardo Sierra Cano
	 * @copyright: Victor Eduardo Sierra Cano
	 * @email: victorsierracano <at> gmail <dot> com
	 * @license: GNU General Public License (GPL)
	 */
	
	final class Usuario extends Persona implements Login{
		Private	$idUsuario;
		private	$email;
		private	$clave;
		Private	$nick;
		private	$permiso;
		private	$KeyToken;
		private $avatar;
		private $estado;
		private $biografia;
		
		/**
		 * constructor de la clase Usuario
		 */
		public function __construct(){ parent::__construct(); }
		
		/**
		 * (non-PHPdoc)
		 * @see Persona::__destruct()
		 */
		function __destruct(){
			unset($this);
			parent::__destruct();
		}
		/**
		 * @param String $mail - mail del nuevo usuario
		 * @return array $infoUsuario datos del usuario a obtener
		 */
		public function get($mail=''){
			$infoUsuario = array();
			$filtrador = new classInputFilter();
			$mail = $filtrador->process(trim($mail));
			if($mail !== '')
			{
				$resultado = null;
				$sql = "SELECT 
						persona.id as id_persona,
						nombre,apellido_paterno,apellido_materno,
						fecha_nacimiento,telefono,permiso,mail,
						password,nick,avatar,estado,biografia
					  FROM 
					    usuario JOIN persona ON persona.id = usuario.id_persona 
						JOIN permiso ON permiso.id = usuario.id_permiso 
					WHERE mail='$mail' LIMIT 0 , 1;";
				$resultado = parent::consulta($sql);
				if ($resultado) {
					$infoUsuario = parent::asociarResultado($resultado);
					parent::setIdPesona( $infoUsuario[ 'id_persona' ] );
					parent::setNombre( $infoUsuario[ 'nombre' ] );
					parent::setApPaterno( $infoUsuario[ 'apellido_paterno' ] );
					parent::setApMaterno( $infoUsuario[ 'apellido_materno' ] );
					parent::setFechaNacimiento( $infoUsuario[ 'fecha_nacimiento' ] );
					parent::setTelefono( $infoUsuario[ 'telefono' ] );
					self::setPermiso( $infoUsuario[ 'permiso' ] );
					self::setEmail( $infoUsuario[ 'mail' ] );
					self::setClave( $infoUsuario[ 'password' ] );
					self::setNick( $infoUsuario[ 'nick' ] );
					self::setAvatar( $infoUsuario[ 'avatar' ] );
					self::setEstado( $infoUsuario[ 'estado' ] );
					self::setBiografia( $infoUsuario[ 'biografia' ] );
				}
				unset($filtrador);
			}
			return $infoUsuario;
		}

		/**
		 * @param String $mail - mail del nuevo usuario
		 * @param String $password - mail del nuevo usuario
		 * @param String $nombre - mail del nuevo usuario
		 * @param String $apellido_paterno - mail del nuevo usuario
		 * @param String $apellido_materno - mail del nuevo usuario
		 * @return int - regresa un int de acuerdo con el resultado de la creacion del usuario puede ser: 
		 * -1 donde el registro del usuario no pudo ser correcto
		 * -2 donde el registro de los datos personales no se registro correctamente
		 * -3 los parametros no son validos o estan vacios
		 */
		public function set($mail = "" , $password = "" , $nombre = "", $apellido_paterno = "", $apellido_materno = "" ){
			parent::init_transaction( 1 );
			$filtrador = new classInputFilter();
			$mail = $filtrador->process(trim($mail));
			$password = $filtrador->process(trim($password));
			$nombre = $filtrador->process(trim($nombre));
			$apellido_paterno = $filtrador->process(trim($apellido_paterno));
			$apellido_materno = $filtrador->process(trim($apellido_materno));
			
			if ( $mail != "" && $password != "" && $nombre != "" && $apellido_paterno != "" ) {
				$campos = array( 
						"nombre" => $nombre , 
						"apellido_paterno" => $apellido_paterno , 
						"apellido_materno" => $apellido_materno
						);
				$tabla="persona";
				$id_insert = parent::insertar( $tabla , $campos );
				if ( -1 != $id_insert ) 
				{
					$campos = array( 
							"id_persona" => $id_insert , "id_permiso" => 2,
							"mail" => $mail , "password" => base64_encode( $password )
							);
					$result = parent::insertar("usuario", $campos);
					if (-1 != $result ) {
						parent::success_transaction();
						unset( $filtrador );
						return 1; 
					}else{
						parent::error_transaction();
						unset( $filtrador );
						return $result;
					}
				}else{
					parent::error_transaction();
					unset( $filtrador );
					return -2;
				}
			}else{
				parent::error_transaction();
				unset( $filtrador );
				return -3;
			}
		}

		/**
		 * @see Persona::edit()
		 * @param String $key - clausula que para buscar el registro a editar 
		 * @param array $dataPersona - datos personales del usuario 
		 * @param array $dataUsuario - datos actualizados del usuario
		 * @return int - regresa un int de acuerdo con el resultado de la actualizacion del usuario
		 * puede ser -1 si ocurrio un error en la actualizacion,
		 * -2 si la $key esta vacia
		 * 1 si se actulizaron correctamente los campos
		 */
		public function edit( $key = "" , $dataPersona = array() , $dataUsuario = array() ){
			parent::init_transaction( 1 );
			$filtrador = new classInputFilter();
			$key = (int)$filtrador -> process( trim( $key ) );
			$dataPersona = $filtrador -> process( $dataPersona );
			$dataUsuario = $filtrador -> process( $dataUsuario );
			
			if ( $key != "" && true == is_numeric( $key ) ) {
				$result = 0 ;
				$clausulaPersona = " id = '$key' ";
				$clausulaUsuario = " id_persona = '$key' ";
				
				if ( !empty( $dataPersona ) ) 
					$result += parent::actualizar( "persona" , $dataPersona , $clausulaPersona);
				if ( !empty( $dataUsuario ) ) 
					$result += parent::actualizar( "usuario" , $dataUsuario , $clausulaUsuario);
				
				if ($result > -1 ) {
					parent::success_transaction();
					unset( $filtrador );
					return 1;
				}else{
					parent::error_transaction();
					unset( $filtrador );
					return -1;
				}
			}else{
				parent::error_transaction();
				unset( $filtrador );
				return -2;
			}
		}

		/**
		 * @param String $key - esta varible permitira encontrar al usuario y eliminarlo
		 * @return int regresa un numero de las filas afectadas
		 * 1 si se borro conrrectamente el usuario,
		 * -1 si no se elimino correctamente
		 * -2 si la $key esta vacia o tiene un formato no permitido
		 */
		public function delete( $key = '' ){
			parent::init_transaction( 1 );
			$filtrador = new classInputFilter();
			$key = (int) $filtrador->process(trim($key));
			if ( "" != $key &&  true == is_numeric($key) ) {
				$result = 0 ;
				$clausulaPersona = " id = '$key' ";
				$clausulaUsuario = " id_persona = '$key' ";
				$result += parent::eliminar("usuario", $clausulaUsuario);
				$result += parent::eliminar("persona", $clausulaPersona);
				if ( $result > -1 ) {
					parent::success_transaction();
					unset( $filtrador );
					return 1;
				}else{
					parent::error_transaction();
					unset( $filtrador );
					return -1;
				}
			}else{
				parent::error_transaction();
				unset( $filtrador );
				return -2;	
			}
		}
		
		/**
		 * @param String $key mail del usuario a buscar
		 * @return int regresa el numero de resultados encontrados.
		 * 0 no exite el Usuario
		 * 1 el usuario existe,
		 * -1 el $key esta vacio o tiene un formato incorrecto
		 */
		public function exists($key = "" ){
			$filtrador = new classInputFilter();
			$key = $filtrador->process(trim($key));
			if ($key != "") {
				$campos = array("mail");
				$tabla = "usuario";
				$clasula=" mail='$key'";
				$result = parent::numeroResultadosSelect( parent::seleccionar( $campos , $tabla , $clasula ) );
				if ($result > 0 ) {
					return true;
				}else{
					return false;
				}
			}else{
				return false;
			}
		}
		
		
		/**
		 * funcion que permitira al usuario loguearse devolviendo un true o false 
	 	* @param $mail - recibe el mail para buscarlo en la base de datos
	 	* @param $pasword - recibe el pasword para verificar el acceso
	 	* @param $pasword - recibe el pasword para verificar el acceso
	 	* @return boolean true si accedio correctamente o false si no pudo acceder
	 	*/
		public function login( $mail = '' , $pasword = '' , $token = '' ) {
			$filtrador = new classInputFilter();
			$mail = $filtrador->process( trim( $mail ) );
			$pasword = $filtrador->process( trim( $pasword ) );
			$token = $filtrador->process( trim( $token ) );
			if ( !empty( $mail ) && !empty( $pasword ) ) {
				$campos = ("mail");
				$table = "usuario";
				$clausula = " mail='$mail' and password = '".base64_encode($pasword)."' ";
				$result = parent::seleccionar($campos,$table,$clausula);
				$numresultado = parent::numeroResultadosSelect( $result );
				if (1 == $numresultado) {
					$_SESSION["mail"] = $mail;
					return true;
				}else{
					return false;
				}
			}else{
				return false;
			}
		}

		/**
		 * (non-PHPdoc)
		 * @see login::cerrarSesion()
		 */
		public function logout() {
			unset($_SESSION);
			session_unset();
			session_destroy();
			session_write_close();
			setcookie(session_name(),'',0,'/');
			session_regenerate_id(true);
		}
		
		/**
		 * (non-PHPdoc)
		 * @see Login::isLogin()
		 */
		public function isLogin($mail){
			if (isset( $_SESSION[ "mail" ] ) ){
				if ($_SESSION[ "mail" ] != "" ) 
				{
					return true;
				}else{
					return false;
				}
			}
			else{
				return false;
			}
		}

		public function getIdUsuario() {
			return $this->idUsuario;
		}
		
		public function getEmail() {
			return $this->email;
		}
		
		public function setEmail($email) {
			$this->email = $email;
			return $this;
		}
		
		public function getClave() {
			return $this->clave;
		}
		
		public function setClave($clave) {
			$this->clave = $clave;
			return $this;
		}
		
		public function getNick() {
			return $this->nick;
		}
		
		public function setNick($nick) {
			$this->nick = $nick;
			return $this;
		}
		
		public function getPermiso() {
			return $this->permiso;
		}
		
		public function setPermiso($permiso) {
			$this->permiso = $permiso;
			return $this;
		}
		
		public function getKeyToken() {
			return $this->KeyToken;
		}
		
		public function setKeyToken($KeyToken) {
			$this->KeyToken = $KeyToken;
			return $this;
		}

		public function getAvatar() {
			return $this->avatar;
		}
		
		public function setAvatar($avatar) {
			$this->avatar = $avatar;
			return $this;
		}

		public function getEstado() {
			return $this->estado;
		}
		
		public function setEstado($estado) {
			$this->estado = $estado;
			return $this;
		}
		
		public function getBiografia() {
			return $this->biografia;
		}
		
		public function setBiografia($biografia) {
			$this->biografia = $biografia;
			return $this;
		}
	}