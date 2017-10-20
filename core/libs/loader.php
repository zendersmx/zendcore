<?php
use libs\Language;
$realPath = explode(DIRECTORY_SEPARATOR ."core", realpath(dirname(__DIR__)));
if (isset($realPath[0])) {
    $realPath = $realPath[0];
}
require $realPath.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'libs'.DIRECTORY_SEPARATOR.'constantes.php';
	session_start();
class Loader{
	private $events		=	array();
	
	function __construct(){
	    include 'core/libs/init.php';
		include 'classInputFilter.php';
		include 'core/libs/Controller.php';
		include 'core/libs/Login.php';
		
		spl_autoload_register(function ($clase) {
		  $realPath = explode(DIRECTORY_SEPARATOR . "core", realpath(dirname(__DIR__)));
            if (isset($realPath[0])) {
                $realPath = $realPath[0];
            }
            $newclass = $realPath . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR  . DIRECTORY_SEPARATOR . "$clase.php";
            $newclass = str_replace("\\", "/", $newclass);
            $newclass = str_replace("//", "/", $newclass);
            $nameInclude = "";
            if (file_exists($newclass)) {
                $nameInclude = $newclass;
            }else if (true == file_exists($realPath . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . "$clase.php")) {
                $nameInclude = $realPath . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . "$clase.php";
            }  
    		else 
                if (true == file_exists($realPath . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . "$clase.php")) {
                    $nameInclude = $realPath . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . "$clase.php";
                } else if (file_exists($realPath . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'libs' . DIRECTORY_SEPARATOR . "$clase.php")) {
                $nameInclude = $realPath . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'libs' . DIRECTORY_SEPARATOR . "$clase.php";
            } 
            else if (file_exists($realPath.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'conexion'.DIRECTORY_SEPARATOR."$clase.php")){
    		    $nameInclude = $realPath . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'conexion' . DIRECTORY_SEPARATOR . "$clase.php";
    		}
    		else  if (file_exists($realPath . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'libs' . DIRECTORY_SEPARATOR . 'JOSE' . DIRECTORY_SEPARATOR . "$clase.php")) {
                $nameInclude = $realPath . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'libs' . DIRECTORY_SEPARATOR . 'JOSE' . DIRECTORY_SEPARATOR . "$clase.php";
            } 
    		 else if (file_exists($realPath.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'libs'.DIRECTORY_SEPARATOR.'JOSE'.DIRECTORY_SEPARATOR.'Base64'.DIRECTORY_SEPARATOR."$clase.php")){
    		    $nameInclude = $realPath.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'libs'.DIRECTORY_SEPARATOR.'JOSE'.DIRECTORY_SEPARATOR.'Base64'.DIRECTORY_SEPARATOR."$clase.php";
    		}
    		else if (file_exists($realPath.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'libs'.DIRECTORY_SEPARATOR.'JOSE'.DIRECTORY_SEPARATOR.'Signer'.DIRECTORY_SEPARATOR."$clase.php")){
    		    $nameInclude = $realPath.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'libs'.DIRECTORY_SEPARATOR.'JOSE'.DIRECTORY_SEPARATOR.'Signer'.DIRECTORY_SEPARATOR."$clase.php";
    		}
    		else if (file_exists($realPath.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'libs'.DIRECTORY_SEPARATOR.'JOSE'.DIRECTORY_SEPARATOR.'Signer'.DIRECTORY_SEPARATOR.'OpenSSL'.DIRECTORY_SEPARATOR."$clase.php")){
    		    $nameInclude = $realPath.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'libs'.DIRECTORY_SEPARATOR.'JOSE'.DIRECTORY_SEPARATOR.'Signer'.DIRECTORY_SEPARATOR.'OpenSSL'.DIRECTORY_SEPARATOR."$clase.php";
    		}
    		else if (file_exists($realPath.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'libs'.DIRECTORY_SEPARATOR.'JOSE'.DIRECTORY_SEPARATOR.'Signer'.DIRECTORY_SEPARATOR.'SecLib'.DIRECTORY_SEPARATOR."$clase.php")){
    		    $nameInclude = $realPath.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'libs'.DIRECTORY_SEPARATOR.'JOSE'.DIRECTORY_SEPARATOR.'Signer'.DIRECTORY_SEPARATOR.'SecLib'.DIRECTORY_SEPARATOR."$clase.php";
    		}
    		$nameInclude = str_replace(DIRECTORY_SEPARATOR."".DIRECTORY_SEPARATOR,DIRECTORY_SEPARATOR, $nameInclude);
    		if (true == ! empty($nameInclude)) {
    		    if (true == file_exists($nameInclude)) {
    		        include $nameInclude;
    		    }
    		}
		});
	}
	
    public static function classLoader(){
        if (version_compare(PHP_VERSION, '5.1.0') >= 0)
	        date_default_timezone_set('UTC');
        spl_autoload_register(function ($clase) {
            $realPath = explode(DIRECTORY_SEPARATOR . "core", realpath(dirname(__DIR__)));
            if (isset($realPath[0])) {
                $realPath = $realPath[0];
            }
            $newclass = $realPath . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR  . DIRECTORY_SEPARATOR . "$clase.php";
            $newclass = str_replace("\\", "/", $newclass);
            $newclass = str_replace("//", "/", $newclass);
            $nameInclude = "";
            if (file_exists($newclass)) {
                $nameInclude = $newclass;
            }
            else if (true == file_exists(getcwd(). DIRECTORY_SEPARATOR . '../../core' . DIRECTORY_SEPARATOR . "libs".DIRECTORY_SEPARATOR."$clase.php")) {
                $nameInclude = getcwd(). DIRECTORY_SEPARATOR . '../../core' . DIRECTORY_SEPARATOR . "libs".DIRECTORY_SEPARATOR."$clase.php";
            }
            else if (true == file_exists($realPath . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . "$clase.php")) {
                $nameInclude = $realPath . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . "$clase.php";
            }
            else if (true == file_exists($realPath . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . "$clase.php")) {
                $nameInclude = $realPath . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . "$clase.php";
            } 
    		else if (true == file_exists($realPath . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . "$clase.php")) {
                $nameInclude = $realPath . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . "$clase.php";
            } 
    		else if (file_exists($realPath . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'libs' . DIRECTORY_SEPARATOR . "$clase.php")) {
                $nameInclude = $realPath . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'libs' . DIRECTORY_SEPARATOR . "$clase.php";
            } 
            else if (file_exists($realPath.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'conexion'.DIRECTORY_SEPARATOR."$clase.php")){
    		    $nameInclude = $realPath . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'conexion' . DIRECTORY_SEPARATOR . "$clase.php";
    		}
    		else  if (file_exists($realPath . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'libs' . DIRECTORY_SEPARATOR . 'JOSE' . DIRECTORY_SEPARATOR . "$clase.php")) {
                $nameInclude = $realPath . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'libs' . DIRECTORY_SEPARATOR . 'JOSE' . DIRECTORY_SEPARATOR . "$clase.php";
            } 
    		 else if (file_exists($realPath.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'libs'.DIRECTORY_SEPARATOR.'JOSE'.DIRECTORY_SEPARATOR.'Base64'.DIRECTORY_SEPARATOR."$clase.php")){
    		    $nameInclude = $realPath.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'libs'.DIRECTORY_SEPARATOR.'JOSE'.DIRECTORY_SEPARATOR.'Base64'.DIRECTORY_SEPARATOR."$clase.php";
    		}
    		else if (file_exists($realPath.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'libs'.DIRECTORY_SEPARATOR.'JOSE'.DIRECTORY_SEPARATOR.'Signer'.DIRECTORY_SEPARATOR."$clase.php")){
    		    $nameInclude = $realPath.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'libs'.DIRECTORY_SEPARATOR.'JOSE'.DIRECTORY_SEPARATOR.'Signer'.DIRECTORY_SEPARATOR."$clase.php";
    		}
    		else if (file_exists($realPath.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'libs'.DIRECTORY_SEPARATOR.'JOSE'.DIRECTORY_SEPARATOR.'Signer'.DIRECTORY_SEPARATOR.'OpenSSL'.DIRECTORY_SEPARATOR."$clase.php")){
    		    $nameInclude = $realPath.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'libs'.DIRECTORY_SEPARATOR.'JOSE'.DIRECTORY_SEPARATOR.'Signer'.DIRECTORY_SEPARATOR.'OpenSSL'.DIRECTORY_SEPARATOR."$clase.php";
    		}
    		else if (file_exists($realPath.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'libs'.DIRECTORY_SEPARATOR.'JOSE'.DIRECTORY_SEPARATOR.'Signer'.DIRECTORY_SEPARATOR.'SecLib'.DIRECTORY_SEPARATOR."$clase.php")){
    		    $nameInclude = $realPath.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'libs'.DIRECTORY_SEPARATOR.'JOSE'.DIRECTORY_SEPARATOR.'Signer'.DIRECTORY_SEPARATOR.'SecLib'.DIRECTORY_SEPARATOR."$clase.php";
    		}
    		else if ($realPath.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'libs'.DIRECTORY_SEPARATOR."$clase.php"){
    		    $nameInclude = $realPath.DIRECTORY_SEPARATOR.'libs'.DIRECTORY_SEPARATOR."$clase.php";
    		}
    		else if (file_exists($realPath.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'resful'.DIRECTORY_SEPARATOR.'v1'.DIRECTORY_SEPARATOR.'endpoints'.DIRECTORY_SEPARATOR."$clase.php")){
    		    $nameInclude = $realPath.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'resful'.DIRECTORY_SEPARATOR.'v1'.DIRECTORY_SEPARATOR.'endpoints'.DIRECTORY_SEPARATOR."$clase.php";
    		}
    		$nameInclude = str_replace(DIRECTORY_SEPARATOR."".DIRECTORY_SEPARATOR,DIRECTORY_SEPARATOR, $nameInclude);
    		if (true == ! empty($nameInclude)) {
    		    if (true == file_exists($nameInclude)) {
                    include $nameInclude;
                }
            }
		});
        $realPath = explode(DIRECTORY_SEPARATOR . "core", realpath(dirname(__DIR__)));
        if (isset($realPath[0])) {
            $realPath = $realPath[0];
        }
        if (true==file_exists($realPath.DIRECTORY_SEPARATOR."core".DIRECTORY_SEPARATOR."settings/config.json")) {
            $data_json = \Functions::convertObjToArray(json_decode(file_get_contents($realPath.DIRECTORY_SEPARATOR."core".DIRECTORY_SEPARATOR."settings/config.json")));
            foreach ($data_json as $key => $object) {
                if ($key != 'VIEWS_SET' && $key != 'LANGUAGE_SET') {
                    foreach ($object as $object_key => $value_object) {
                        if (true!=empty($value_object)) {
                            defined($object_key) or define($object_key, $value_object);
                        }
                    }
                }
            }
        }
        else if (true==file_exists('../../settings/config.json')) {
            $data_json = \Functions::convertObjToArray(json_decode(file_get_contents('../../settings/config.json')));
            foreach ($data_json as $key => $object) {
                if ($key != 'VIEWS_SET' && $key != 'LANGUAGE_SET') {
                    foreach ($object as $object_key => $value_object) {
                        if (true!=empty($value_object)) {
                            defined($object_key) or define($object_key, $value_object);
                        }
                    }
                }
            }    
        }else if(true == file_exists('../settings/config.json')){
            $data_json = \Functions::convertObjToArray(json_decode(file_get_contents('../settings/config.json')));
            foreach ($data_json as $key => $object) {
                if ($key != 'VIEWS_SET' && $key != 'LANGUAGE_SET') {
                    foreach ($object as $object_key => $value_object) {
                        if (true != empty($value_object)) {
                            defined($object_key) or define($object_key, $value_object);
                        }
                    }
                }
            }
        }
	} 
	
	public function handler() {
		$filtrador	=	new classInputFilter();
		$language = new Language();
		
		$event 		= 	'';
		$event2 	=	'';
		$event3		=	'';
		
		$_POST		=	$filtrador->process($_POST);
		$_GET		=	$filtrador->process($_GET);
		$uri		=	$filtrador->process($_SERVER['REQUEST_URI']);
		$uri = str_replace('client/', '', $uri);
		
		$realPath = getcwd();
		if (isset($realPath[0])) {
		    $realPath = $realPath[0];
		}

		foreach ($GLOBALS['LANGUAGE'] as $idioma) {
		    $idioma = $filtrador->process($idioma);
			$uri_peticion	=	MODULO.$idioma;
			if(true ==  stristr($uri, $uri_peticion) ){
				$event = $idioma;
				$_SESSION['idioma'] =	$event;
			}
		}
		
		if ('' == $event ){
		    if (!isset($_SESSION['idioma'])) {
				$event	=	$language->getLanguage();
				$_SESSION['idioma'] =	$event;
			}else{
			    if ($_SESSION['idioma'] == "") {
					$event = $language->getLanguage();
					$_SESSION['idioma'] = $event;
				}else{
					 $event = $_SESSION['idioma'];
				}
			}
		}
		else{
		    $event = $filtrador->process($event);
		    if (isset($_SESSION['idioma']))
				$_SESSION['idioma'] =	$event;
		}
		
		foreach ($GLOBALS['VIEWS'] as $pagina) {
		    $pagina = $filtrador->process($pagina);
			$uri_peticion = MODULO.$event."/".$pagina;
			$evaluacion = stristr($uri, $uri_peticion);
			if( true == $evaluacion ) {
				$event2 = $pagina;
				$_SESSION['pagina'] = $event2;
			}
		}
		if ('' == $event2) {	
			foreach ($GLOBALS['VIEWS'] as $pagina) {
				$uri_peticion = MODULO.$pagina;
				if( stristr($uri, $uri_peticion) == true ) {
					$event2 = $pagina;
					$_SESSION['pagina'] = $event2;
				}
			}
			if ('' == $event2) {
				if ( isset($_SESSION['pagina']) ){
					if ($_SESSION['pagina']!= '' ) {
						$event2 = $_SESSION['pagina'];
					}
				}else {
					if ($uri == MODULO){
						$_SESSION['pagina'] = DEFAULT_VIEW;
					}
					if (isset($_SESSION['pagina'])) {
						$event2 = $_SESSION['pagina'];
					}
					else{
						$_SESSION['pagina'] = DEFAULT_VIEW;
						$event2 = DEFAULT_VIEW;
					}
				}
			}else {
				if (!isset($_SESSION['pagina'])) {
					if ($_SESSION[ 'pagina' ] != '') {
						$_SESSION['pagina'] = $event2;
					}
				} 
			}
		}
		if ('' != $event  && '' != $event2 ){
			$uri_peticion = MODULO.$event."/".$event2."-";
			
			if( true == stristr($uri, $uri_peticion)){
				$event3 = str_replace($uri_peticion,"", $uri);
			}
			else{
				$uri_peticion = MODULO.$event2."-";
				if( true == stristr($uri, $uri_peticion) ){
					$event3 = str_replace($uri_peticion,"", $uri);
					$_SESSION['parametro'] = $event3;
				}
			}
			if ($event3 == "" && isset($_SESSION['parametro'])) {
				$event3 = $_SESSION['parametro'];
			}
		}
		
		
		$events = array();
		$events['eventlang'] = $event;
		$events['eventpage'] = $event2;
		$events['eventparam'] = $event3;
		$events['PORT_URL'] = self::get_port_server();
		$events['SERVER'] = self::name_server_static();
		$events['SERVER'] = str_replace("design", "", $events['SERVER']);
		$events['SERVER_SSL'] = self::ssl_name_server_static();
		
		$data = array_merge($events,$_POST,$_GET);
		if (true == isset($data['idioma'])) {
		    if ($events['eventlang'] != $data['idioma']) {
		        foreach ($GLOBALS['LANGUAGE'] as $lang) {
		            if (strtolower($data['idioma']) == $lang) {
		                $data['eventlang'] = $lang;
		                $_SESSION['idioma'] = $lang;
		                break;
		            }
		        }
		    }    
		}
		unset($events);
		unset($_POST);
		unset($_GET);
		unset($GLOBALS['VIEWS']);
		unset($GLOBALS['LANGUAGE']);
		$init = new Init($data);
	}
	
	static function  get_port_server() {
	    $port="http";
	    if (443 == $_SERVER['SERVER_PORT']) $port="https";
	    return $port;
	}
	
	static function  name_server_static() {
		$port="http";
		if (443 == $_SERVER['SERVER_PORT']) $port="https";
		$SERVIDOR 	="$port://".$_SERVER['HTTP_HOST']."".MODULO;
		return $SERVIDOR;
	}
	
	static function  ssl_name_server_static() {
	    $port="http";
	    if (443 == $_SERVER['SERVER_PORT']) $port="https";
	    $SERVIDOR 	="https://".$_SERVER['HTTP_HOST']."".MODULO;
	    return $SERVIDOR;
	}
}