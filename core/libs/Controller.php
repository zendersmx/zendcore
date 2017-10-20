<?php
	abstract class Controller{
		var $data = array();
		/**
		 * 
		 * @param unknown $_ZENDR
		 * @example
		 * $this->data['cpu'] = Functions::get_server_load().' {#usage} ';
		 */
		abstract function index($_ZENDR);
	}
?>