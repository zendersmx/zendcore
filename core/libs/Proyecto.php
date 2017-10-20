<?php
final class Proyecto extends classConexion {
	private $id;
	private $titulo_proyecto;
	private $subtitulo_proyecto;
	private $descripcion;
	private $tags;
	private $url;
	private $imgs;
	
	function __construct() {
		parent::__construct();
	}
	function __destruct() {
		unset($this);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see classConexion::get()
	 */
	public function get($id = "" ) {
		$dataProyecto = array();
		$filtrador = new classInputFilter();
		$id = $filtrador->process( trim( $id ) );
		if ( !empty( $id ) ) {
			$campos = array("id","titulo_proyecto","subtitulo_proyecto","descripcion","tags","url","imgs");
			$tabla = "proyecto";
			$clausula="id = '$id' ";
			$result = parent::seleccionar( $campos , $tabla , $clausula );
			if ( $result ){
				$dataProyecto = parent::asociarResultado( $result );
				self::setId($dataProyecto['id']);
				self::setTituloProyecto($dataProyecto['titulo_proyecto']);
				self::setSubtituloProyecto($dataProyecto['subtitulo_proyecto']);
				self::setDescripcion($dataProyecto['descripcion']);
				self::setTags($dataProyecto['tags']);
				self::setUrl($dataProyecto['url']);
				self::setImgs($dataProyecto['imgs']);
			}
		}
		unset( $filtrador );
		return $dataProyecto;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see classConexion::set()
	 */
	public function set( $data = array() ) {
		if (!empty($data)) {
			$filtrador = new classInputFilter();
			$data = $filtrador->process( $data );
			if (key_exists("id", $data)) {
				$data["id"] = str_replace(" " , "_", $data["id"] );
				$tabla = "proyecto";
				$resultado = parent::insertar($tabla, $data);
				if (-1 != $resultado) {
					unset( $filtrador );
					return 1;
				}else{
					unset( $filtrador );
					return -1;
				}
			}else{
				unset( $filtrador );
				return -2;
			}
		}else{
			unset( $filtrador );
			return -3;
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see classConexion::edit()
	 */
	public function edit( $id = "" , $data = array() ) {
		$filtrador = new classInputFilter();
		$id = $filtrador->process( trim( $id ) );
		$data = $filtrador->process( $data );
		if (!empty( $id )) {
			$tabla="proyecto";
			$clausula=" id = '$id' ";
			$result = parent::actualizar($tabla,$data,$clausula);
			if ($result > -1 ) {
				unset( $filtrador );
				return 1;
			}else{
				unset( $filtrador );
				return -1;
			}
		}else{
			unset( $filtrador );
			return -2;
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see classConexion::delete()
	 */
	public function delete( $id = "" ) {
		$filtrador = new classInputFilter();
		$id = $filtrador->process( trim( $id ) );
		if (!empty( $id )) {
			$tabla="proyecto";
			$clausula=" id = '$id' ";
			$result = parent::eliminar($tabla, $clausula);
			if ($result > 0 ) {
				unset( $filtrador );
				return 1;
			}else{
				unset( $filtrador );
				return -1;
			}
		}else{
			unset( $filtrador );
			return -2;
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see classConexion::exists()
	 */
	public function exists( $id = "" ) {
		$filtrador = new classInputFilter();
		$id = $filtrador->process( trim( $id ) );
		if (!empty( $id )) {
			$tabla="proyecto";
			$clausula=" id = '$id' ";
			$result = parent::numeroResultadosSelect(parent::seleccionar(array("id"),$tabla,$clausula));
			if ($result == 1 ) {
				unset( $filtrador );
				return 1;
			}else{
				unset( $filtrador );
				return -1;
			}
		}else{
			unset( $filtrador );
			return -2;
		}
		
	}

	public function getId() {
		return $this->id;
	}
	
	public function setId($id) {
		$this->id = $id;
		return $this;
	}
	
	public function getTituloProyecto() {
		return $this->titulo_proyecto;
	}
	
	public function setTituloProyecto($titulo_proyecto) {
		$this->titulo_proyecto = $titulo_proyecto;
		return $this;
	}
	
	public function getSubtituloProyecto() {
		return $this->subtitulo_proyecto;
	}
	
	public function setSubtituloProyecto($subtitulo_proyecto) {
		$this->subtitulo_proyecto = $subtitulo_proyecto;
		return $this;
	}
	
	public function getDescripcion() {
		return $this->descripcion;
	}
	
	public function setDescripcion($descripcion) {
		$this->descripcion = $descripcion;
		return $this;
	}
	
	public function getTags() {
		return $this->tags;
	}
	
	public function setTags($tags) {
		$this->tags = $tags;
		return $this;
	}
	
	public function getUrl() {
		return $this->url;
	}
	
	public function setUrl($url) {
		$this->url = $url;
		return $this;
	}

	public function getImgs() {
		return $this->imgs;
	}
	
	public function setImgs($imgs) {
		$this->imgs = $imgs;
		return $this;
	}
	
	public function getLastProyects() {
		$proyectos = array();
		$campos = array("id","titulo_proyecto","img_thumb");
		$tabla  = "proyecto";
		$limit  = "";
		$result = parent::seleccionar( $campos , $tabla , "" , 0 , "fecha_creacion" , "desc" , 0 , 4 );
		if ( $result  ) {
			while ($proyect = parent::asociarResultado( $result ) ) {
				$proyectos[] = $proyect;
			}
		}
		return $proyectos;
	}
}

?>