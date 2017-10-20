<?php
 class Logs extends classConexion{
 	
	/* (non-PHPdoc)
	 * @see classConexion::__construct()
	 */
	public function __construct() {
		parent::__construct();
	}
	
	/* (non-PHPdoc)
	 * @see classConexion::get()
	 */
	public function get($key) {
		// TODO Auto-generated method stub
		
	}
	
	/* (non-PHPdoc)
	 * @see classConexion::set()
	 */
	public function set($id_user=0,$accion=0,$fecha='',$ip='') {
		if ($accion != 0 && $fecha != 0 && $ip != '') {
			$campos=array('id_usuario','accion','fecha','ip');
			$datosUsuario=array('id_usuario'=>$id_user,'accion'=>$accion,'fecha'=>$fecha,'ip'=>$ip);
			parent::insertar("logs",$campos,$datosUsuario);
		}
	}

	/* (non-PHPdoc)
	 * @see classConexion::edit()
	 */
	public function edit($key,$data) {
		// TODO Auto-generated method stub
		
	}

	/* (non-PHPdoc)
	 * @see classConexion::delete()
	 */
	public function delete( $key ) {
		// TODO Auto-generated method stub
		
	}
	
	public function exists($key){}
	
	public function getLogs() {
		$archivo = getcwd().'/core/usuario/GeoIP.dat';
		if (true == is_file($archivo)) {
			require_once 'geoip.php';
			$abir_bd = geoip_open($archivo,GEOIP_STANDARD);
		}
		$logs = array();
		$sql="SELECT usuario.nick AS usuario, acciones.accion AS accion, logs.fecha AS fecha, logs.ip AS ip
				FROM logs JOIN usuario ON logs.id_usuario = usuario.idusuario
				JOIN acciones ON acciones.id = logs.accion 
				ORDER BY fecha DESC";
		$data = parent::consulta($sql);
		if($data){
			while ($log = parent::asociarResultado($data)) {
				$log['pais'] = geoip_country_code_by_addr($abir_bd, $log['ip']);
				$logs[] = $log;
			}
			geoip_close($abir_bd);
		}
		return $logs;
	}
 }