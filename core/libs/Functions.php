<?php

class Functions
{
    static function convertobjectToArray($obj)
    {
        if (is_object($obj)) {
            $obj = get_object_vars($obj);
        }
        if (is_array($obj)) {
            $obj = array_map(call_user_func_array('convertobjectToArray', array($obj)), $obj);
            foreach ($obj as $key => $value) {
                if (is_object($value)) {
                    $tmp = get_object_vars($value);
                    if (true==is_array($tmp)) {
                        $obj[$key] =$tmp;
                    }
                }
            }
            return $obj;
        } else {
            return $obj;
        }
    }
    static function parse_size($size)
    {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
        $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
        if ($unit) {
            // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
            $val = round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
            if ($val>0) {
                return ($val/1024);
            }else return 0;
        } else {
            $val = round($size);
            $val  = $val/1024;
            if ($val>0) {
                return $val;
            }else return 0;
        }
    }

    static function file_upload_max_size()
    {
        static $max_size = - 1;
        
        if ($max_size < 0) {
            // Start with post_max_size.
            $max_size = Functions::parse_size(ini_get('post_max_size'));
            // If upload_max_size is less, then reduce. Except if upload_max_size is
            // zero, which indicates no limit.
            $upload_max = Functions::parse_size(ini_get('upload_max_filesize'));
            if ($upload_max > 0 && $upload_max < $max_size) {
                $max_size = $upload_max;
            }
        }
        return $max_size;
    }

    static function objToArray($obj)
    {
        if (is_object($obj)) {
            $obj = get_object_vars($obj);
        }
        if (is_array($obj)) {
            $obj = array_map(call_user_func(__FUNCTION__), $obj);
            foreach ($obj as $key => $value) {
                if (is_object($value)) {
                    $tmp = get_object_vars($value);
                    if (true == is_array($tmp)) {
                        $obj[$key] = $tmp;
                    }
                }
            }
            return $obj;
        } else {
            return $obj;
        }
    }

    static function objectToArrayPure($d)
    {
        $result = array();
        foreach ($d as $key => $value) {
            $result[$key] = $value;
        }
        return $result;
    }

    /**
     *
     * @return number
     */
    public static function get_server_load()
    {
        if (stristr(PHP_OS, 'win')) {
            
            $wmi = new \COM("Winmgmts://");
            $server = $wmi->execquery("SELECT LoadPercentage FROM Win32_Processor");
            
            $cpu_num = 0;
            $load_total = 0;
            
            foreach ($server as $cpu) {
                $cpu_num ++;
                $load_total += $cpu->loadpercentage;
            }
            
            $load = round($load_total / $cpu_num);
        } else {
            
            $sys_load = sys_getloadavg();
            $load = $sys_load[0];
        }
        return (int) $load;
    }

    static function convertObjToArray($obj)
    {
        if (is_object($obj)) {
            $obj = get_object_vars($obj);
        }
        if (true == is_array($obj)) {
            $obj = array_map(__METHOD__, $obj);
            foreach ($obj as $key => $value) {
                if (is_object($value)) {
                    $tmp = get_object_vars($value);
                    if (true == is_array($tmp)) {
                        $obj[$key] = $tmp;
                    }
                }
            }
            return $obj;
        } else {
            return $obj;
        }
    }
    
    
    static function setLabelsArray($array, $case=CASE_UPPER, $flag_rec=false)
    {
        $tmp = array_change_key_case($array, $case);
        if (true == $flag_rec) {
            foreach ($tmp as $key => $value) {
                if (true == is_array($value)) {
                     $tmp[$key] = \Functions::setLabelsArray($tmp[$key], $case, true);
                }
            }
        }
        return $tmp;
    }
    
    // sDestination: lista de numeros, comenzando por 34 y separados por comas
    // sMessage: hasta 160 caracteres
    // debug: Si es true muestra por pantalla la respuesta completa del servidor
    // XX, YY y ZZ se corresponden con los valores de identificacion del
    // usuario en el sistema.
    static function AltiriaSMS($sDestination, $sMessage, $debug)
    {
        $sData = "cmd=sendsms&";
        $sData .= "domainId=demopr&";
        $sData .= "login=protecsaing&";
        $sData .= "passwd=xxoxobte&";
        $sData .= "dest=" . str_replace(",", "&dest=", $sDestination) . "&";
        $sData .= "msg=" . urlencode(utf8_encode(substr("TEST " . $sMessage, 0, 160)));
        $fp = fsockopen("www.enviosmsphp.net", 80, $errno, $errstr, 10);
        if (! $fp) {
            // Error de conexion
            $output = "ERROR de conexion: $errno - $errstr\n";
            $output .= "Compruebe que ha configurado correctamente la direccion/url ";
            $output .= "suministrada por altiria";
            return $output;
        } else {
            // Reemplazar la cadena ’/sustituirPOSTsms’ por la parte correspondiente
            // de la URL suministrada por Altiria al dar de alta el servicio
            $buf = "POST /enviosmsphp HTTP/1.0\r\n";
            $buf .= "Host: www.enviosmsphp.net\r\n";
            $buf .= "Content-type: application/x-www-form-urlencoded; charset=UTF-8\r\n";
            $buf .= "Content-length: " . strlen($sData) . "\r\n";
            $buf .= "\r\n";
            $buf .= $sData;
            fputs($fp, $buf);
            $buf = "";
            while (! feof($fp))
                $buf .= fgets($fp, 128);
            fclose($fp);
            // Si la llamada se hace con debug, se muestra la respuesta completa del servidor
            if ($debug) {
                print "Respuesta del servidor: " . $buf . "";
            }
            // Se comprueba que se ha conectado realmente con el servidor
            // y que se obtenga un codigo HTTP OK 200
            if (strpos($buf, "HTTP/1.1 200 OK") === false) {
                $output = "ERROR. Codigo error HTTP: " . substr($buf, 9, 3) . "\n";
                $output .= "Compruebe que ha configurado correctamente la direccion/url ";
                $output .= "suministrada por Altiria";
                return $output;
            }
            // Se comprueba la respuesta de Altiria
            if (strstr($buf, "ERROR")) {
                $output = $buf . "\n";
                $output .= " Codigo de error de Altiria. Compruebe la especificacion";
                return $output;
            } else
                return "";
        }
    }

    static function objectToArray($d)
    {
        $result = array();
        foreach ($d as $key => $value) {
            $result[$key] = base64_decode($value);
        }
        return $result;
    }

    static function getVariable($name)
    {
        $value = NULL;
        if (true == key_exists($name, $_POST)) {
            if ($_POST[$name] != '') {
                $value = $_POST[$name];
            }
        } elseif (true == key_exists($name, $_GET)) {
            if ($_GET[$name] != '') {
                $value = $_GET[$name];
            }
        }
        return $value;
    }

    static function URLSERVER()
    {
        $port = "http";
        if (443 == $_SERVER['SERVER_PORT'])
            $port = "https";
        $SERVIDOR = "$port://" . $_SERVER['HTTP_HOST'];
        return $SERVIDOR;
    }

    static function URLSERVERSLASH()
    {
        $port = "http";
        if (443 == $_SERVER['SERVER_PORT'])
            $port = "https";
        $SERVIDOR = "$port://" . $_SERVER['HTTP_HOST'] . "/";
        return $SERVIDOR;
    }

    public static function verify_Date($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    /**
     *
     * @param unknown $email            
     * @return boolean
     */
    public static function verify_email($email)
    {
        if (preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/', $email)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *
     * @param unknown $color            
     * @return boolean
     */
    public static function verify_color($color)
    {
        if (preg_match('/^#(?:(?:[a-f\d]{3}){1,2})$/i', $color)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *
     * @param unknown $IP            
     * @return boolean
     */
    public static function verify_IP4($IP)
    {
        if (preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\z/', $IP)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *
     * @param unknown $code            
     * @return boolean
     */
    public static function verify_codePostal($code)
    {
        if (preg_match('/^[0-9]{5,5}([- ]?[0-9]{4,4})?$/', $code)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *
     * @param String $password            
     * @return number
     */
    public static function verify_password($password)
    {
        trim($password, "\t\n\r\0\x0B");
        $count = strlen($password);
        $entropia = 0;
        // Si el password tiene menos de 6 caracteres
        if ($count < 6)
            return 1; // "Password muy debil"
                          // Contamos cuantas mayusculas, minusculas, numeros y simbolos existen
        $upper = 0;
        $lower = 0;
        $numeros = 0;
        $otros = 0;
        for ($i = 0, $j = strlen($password); $i < $j; $i ++) {
            $c = substr($password, $i, 1);
            if (preg_match('/^[[:upper:]]$/', $c))
                $upper ++;
            elseif (preg_match('/^[[:lower:]]$/', $c))
                $lower ++;
            elseif (preg_match('/^[[:digit:]]$/', $c))
                $numeros ++;
            else
                $otros ++;
        }
        // Calculamos la entropia
        $entropia = ($upper * 4.7) + ($lower * 4.7) + ($numeros * 3.32) + ($otros * 6.55);
        if ($entropia < 28)
            return 1; // "Password muy debil"
        elseif ($entropia < 36)
            return 2; // "Password debil"
        elseif ($entropia < 60)
            return 3; // "Password Razonable"
        elseif ($entropia < 128)
            return 4; // "Password Fuerte"
        else
            return 5; // "Password Muy Fuerte"
    }

    public static function getSize($bytes)
    {
        if (false == is_numeric($bytes))
            return '0 Byte';
        if ($bytes == 0)
            return '0 Byte';
        $k = 1000;
        $sizes = array(
            'Bytes',
            'KB',
            'MB',
            'GB',
            'TB',
            'PB',
            'EB',
            'ZB',
            'YB'
        );
        $i = floor(log($bytes) / log($k));
        $total = round((($bytes / pow($k, $i)) * 100) / 100, 2);
        return $total . ' ' . $sizes[$i];
    }

    public static function random_color()
    {
        return str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT) . str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT) . str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
    }

    public static function random_color_hex()
    {
        return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    }

    public static function getBrowser()
    {
        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version = "";
        
        // First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        } elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }
        
        // Next get the name of the useragent yes seperately and for good reason
        if (preg_match('/MSIE/i', $u_agent) && ! preg_match('/Opera/i', $u_agent)) {
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        } elseif (preg_match('/Firefox/i', $u_agent)) {
            $bname = 'Mozilla Firefox';
            $ub = "Firefox";
        } elseif (preg_match('/Chrome/i', $u_agent)) {
            $bname = 'Google Chrome';
            $ub = "Chrome";
        } elseif (preg_match('/Safari/i', $u_agent)) {
            $bname = 'Apple Safari';
            $ub = "Safari";
        } elseif (preg_match('/Opera/i', $u_agent)) {
            $bname = 'Opera';
            $ub = "Opera";
        } elseif (preg_match('/Netscape/i', $u_agent)) {
            $bname = 'Netscape';
            $ub = "Netscape";
        }
        
        // finally get the correct version number
        $known = array(
            'Version',
            $ub,
            'other'
        );
        $pattern = '#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (! preg_match_all($pattern, $u_agent, $matches)) {
            // we have no matching number just continue
        }
        
        // see how many we have
        $i = count($matches['browser']);
        if ($i != 1) {
            // we will have two since we are not using 'other' argument yet
            // see if version is before or after the name
            if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
                $version = $matches['version'][0];
            } else {
                $version = $matches['version'][1];
            }
        } else {
            $version = $matches['version'][0];
        }
        
        // check if we have a number
        if ($version == null || $version == "") {
            $version = "?";
        }
        
        return array(
            'userAgent' => $u_agent,
            'name' => $bname,
            'version' => $version,
            'platform' => $platform,
            'pattern' => $pattern
        );
    }
}

?>