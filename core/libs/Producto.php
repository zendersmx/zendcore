<?php
	final class Producto extends classConexion {
		private $id;
		private $id_usuario;
		private $usuario = array();
		private $categorias;
		private $nombre;
		private $descripcion;
		private $precio;
		private $precio_anterior;
		private $disponibles;
		private $color;
		private $size;
		private $unidad_de_medida;
		private $imagenes;
		private $comments;
		
		function __construct() {
			parent::__construct();
		}
		function __destruct() {
			unset( $this );		
		}
		
		public function get( $id_name = "" ) {
			$dataProyecto = array();
			$filtrador = new classInputFilter();
			$id_name = $filtrador->process( trim( $id_name ) );
			if (true == !empty( $id_name ) ) {
				$campos = array(
						"id","id_usuario","id_name","categorias","nombre",
						"descripcion","precio","precio_anterior","disponibles",
						"color","size","unidad_de_medida","imagenes");
				$tabla = "producto";
				$clausula = " id_name = '$id_name' ";
				$result = parent::seleccionar( $campos , $tabla , $clausula , 0 , "" , "" , 0 , 1 );
				if ( $result ){
					$dataProyecto = parent::asociarResultado( $result );
					self::setId($dataProyecto['id']);
					self::setIdName($dataProyecto['id_name']);
					self::setCategorias($dataProyecto['categorias']);
					self::setNombre($dataProyecto['nombre']);
					self::setDescripcion($dataProyecto['descripcion']);
					self::setPrecio($dataProyecto['precio']);
					self::setPrecioAnterior($dataProyecto['precio_anterior']);
					self::setDisponibles($dataProyecto['disponibles']);
					self::setColor($dataProyecto['color']);
					self::setSize($dataProyecto['size']);
					self::setUnidadDeMedida($dataProyecto['unidad_de_medida']);
					self::setImagenes($dataProyecto['imagenes']);
					$sql="
						SELECT nombre,apellido_paterno,apellido_materno,mail,nick,avatar 
						FROM usuario 
						JOIN persona 
						ON usuario.id_persona=persona.id
						WHERE usuario.id='".$dataProyecto['id_usuario']."'
						LIMIT 0,1;";
					$resultUser = parent::consulta( $sql );
					if ( $resultUser ) {
						$user = parent::asociarResultado( $resultUser );
						self::setUsuario($user);
					}
				}
			}
			return $dataProyecto;
		}
		
		public function set( $data = array() ) {
			
		}
		
		public function exists($id_name = "" ) {
			
		}
		
		public function delete( $id_name = "" ) {
			
		}
		
		public function edit( $id_name = "" , $data = array() ) {
			
		}

		public function getId() {
			return $this->id;
		}
		
		public function setId($id) {
			$this->id = $id;
			return $this;
		}
		
		public function getUsuario() {
			return $this->usuario;
		}
		
		public function setUsuario($usuario) {
			$this->usuario = $usuario;
			return $this;
		}
		
		public function getIdName() {
			return $this->id_name;
		}
		
		public function setIdName($id_name) {
			$this->id_name = $id_name;
			return $this;
		}
		
		public function getCategorias() {
			return $this->categorias;
		}
		
		public function setCategorias($categorias) {
			$this->categorias = $categorias;
			return $this;
		}
		
		public function getNombre() {
			return $this->nombre;
		}
		
		public function setNombre($nombre) {
			$this->nombre = $nombre;
			return $this;
		}
		
		public function getDescripcion() {
			return $this->descripcion;
		}
		
		public function setDescripcion($descripcion) {
			$this->descripcion = $descripcion;
			return $this;
		}
		
		public function getPrecio() {
			return $this->precio;
		}
		
		public function setPrecio($precio) {
			$this->precio = $precio;
			return $this;
		}
		
		public function getPrecioAnterior() {
			return $this->precio_anterior;
		}
		
		public function setPrecioAnterior($precio_anterior) {
			$this->precio_anterior = $precio_anterior;
			return $this;
		}
		
		public function getDisponibles() {
			return $this->disponibles;
		}
		
		public function setDisponibles($disponibles) {
			$this->disponibles = $disponibles;
			return $this;
		}
		
		public function getColor() {
			return $this->color;
		}
		
		public function setColor($color) {
			$this->color = $color;
			return $this;
		}
		
		public function getSize() {
			return $this->size;
		}
		
		public function setSize($size) {
			$this->size = $size;
			return $this;
		}
		
		public function getUnidadDeMedida() {
			return $this->unidad_de_medida;
		}
		
		public function setUnidadDeMedida($unidad_de_medida) {
			$this->unidad_de_medida = $unidad_de_medida;
			return $this;
		}
		
		public function getImagenes() {
			return $this->imagenes;
		}
		
		public function setImagenes($imagenes) {
			$this->imagenes = $imagenes;
			return $this;
		}

		public function getComments( $id_producto ) {
			$filtrador = new classInputFilter();
			$id_name = $filtrador->process( trim( $id_name ) );
			if (true == !empty( $id_name ) ) {
				$campos = array("id_comentario","id_producto","id_usuario","fecha","comentario");
				$tabla = "comentarios_producto";
				$clausula="id_producto = '$id_producto' ";
				$result = parent::seleccionar( $campos , $tabla , $clausula , 0 , "fecha" , "ASC" );
				if ( $result ){
					while ($comment = parent::asociarResultado( $result )){
						$sql="SELECT nombre,apellido_paterno,avatar
						  FROM usuario join persona on usuario.id_persona = persona.id
						  WHERE usuario.id_persona='".$comment['id_usuario']."' LIMIT 0 , 1";
						$resultUser = parent::consulta( $sql );
						if ( $resultUser ) {
							$comment["user"] = parent::asociarResultado($resultUser);
							$this->comments[] = $comment;
						}
					}
				}
			}
			return $this->comments;
		}
	
		public function setComments($comments) {
			$this->comments = $comments;
			return $this;
		}
		
		
		
	}

?>