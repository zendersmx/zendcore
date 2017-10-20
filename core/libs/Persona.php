<?php
	abstract class Persona extends classConexion{
		/** 
		* Propiedades de una persona
		*/
		private   $id_persona;
		private   $nombre;
		private   $apPaterno;
		private   $apMaterno;
		private   $fechaNacimiento;
		private	  $telefono;
		
		/**
		 * Constructor de la clase
		 */
		public function __construct(){parent::__construct();}
		
		/**
		 * 
		 * El destructor que nos permitir eliminar a nuestra persona
		 */		
		public function __destruct(){unset($this);}

		public function getIdPersona() {
			return $this->id_persona;
		}
		
		public function setIdPesona($id) {
			$this->id_persona = $id;
			return $this;
		}
		
		public function getNombre() {
			return $this->nombre;
		}
		
		public function setNombre($nombre) {
			$this->nombre = $nombre;
			return $this;
		}
		
		public function getApPaterno() {
			return $this->apPaterno;
		}
		
		public function setApPaterno($apPaterno) {
			$this->apPaterno = $apPaterno;
			return $this;
		}
		
		public function getApMaterno() {
			return $this->apMaterno;
		}
		
		public function setApMaterno($apMaterno) {
			$this->apMaterno = $apMaterno;
			return $this;
		}
		
		public function getFechaNacimiento() {
			return $this->fechaNacimiento;
		}
		
		public function setFechaNacimiento($fechaNacimiento) {
			$this->fechaNacimiento = $fechaNacimiento;
			return $this;
		}
		
		public function getTelefono() {
			return $this->telefono;
		}
		
		public function setTelefono($telefono) {
			$this->telefono = $telefono;
			return $this;
		}		
	}