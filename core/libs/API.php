<?php
namespace libs;
use libs\Filter;
use libs\Encrypt;
use libs\JSONH;
include '../../libs/loader.php';
define("AOUTH_COOKIE", 1);
define("AOUTH_HTTP_HEAD", 2);
define("AOUTH_DB", 3);
define("GET", '_get');
define("POST", '_post');
define("PUT", '_put');
define("DELETE", '_delete');
$realPath = explode(DIRECTORY_SEPARATOR ."core", realpath(dirname(__DIR__)));
if (isset($realPath[0])) {
    $realPath = $realPath[0];
}
define("SSL_FILE_PATH", $realPath.DIRECTORY_SEPARATOR."core".DIRECTORY_SEPARATOR."libs".DIRECTORY_SEPARATOR."open_ssl".DIRECTORY_SEPARATOR);
abstract class API
{

    /**
     * Property: method
     * The HTTP method this request was made in, either GET, POST, PUT or DELETE
     */
    protected $method = '';

    /**
     * Property: endpoint
     * The Model requested in the URI.
     * eg: /files
     */
    protected $endpoint = '';

    /**
     * Property: verb
     * An optional additional descriptor about the endpoint, used for things that can
     * not be handled by the basic methods.
     * eg: /files/process
     */
    protected $verb = '';

    /**
     * Property: args
     * Any additional URI components after the endpoint and verb have been removed, in our
     * case, an integer ID for the resource.
     * eg: /<endpoint>/<verb>/<arg0>/<arg1>
     * or /<endpoint>/<arg0>
     */
    protected $args = Array();

    /**
     * Property: file
     * Stores the input of the PUT request
     */
    protected $file = Null;
    
    /**
     * 
     * @var Array
     */
    protected $request=array();
    
    /**
     * 
     * @var Integer
     */
    private $type_auth = AOUTH_DB;

    /**
     *
     * @var Integer
     */
    private $_code = 403;
    
    /**
     * 
     * @var unknown
     */
    protected $less_content = false;

    /**
     *
     * @var String
     */
    private $_content_type = "application/json";
    
    /**
     * 
     * @var Boolean
     */
    private $compress = false;
    
    /**
     * 
     * @var Array
     */
    protected  $data = array();
    
    /**
     * 
     * @var String
     */
    private $token = "";
    
    /**
     * 
     * @var Boolean
     */
    private $debug = false;
    
    /**
     * 
     * @var Boolean
     */
    private $secureDataActive = false;
    
    /**
     * 
     * @var String
     */
    private $type_encryption = "aes-128-ecb";
    
    /**
     * 
     * @var Boolean  
     */
    private $receivedDataIsEncrypted = false;
    
    /**
     * 
     * @var String
     */
    private $uidApp = "";
    
    /**
     * 
     * @var boolean
     */
    private $requiredAuh=true;
    
    /**
     *
     * @var String
     */
    private $keyApi='';
    
    /**
     * @var version of engine
     */
    private $engine = "ZendCore 2.0.2";

    /**
     * Constructor: __construct
     * Allow for CORS, assemble and pre-process the data
     */
    public function __construct($request, $origin)
    { 
        \Loader::classLoader();
        $this->args = explode('/', rtrim($request, '/'));
        $this->endpoint = array_shift($this->args);
        if (array_key_exists(0, $this->args) && ! is_numeric($this->args[0])) {
            $this->verb = array_shift($this->args);
        }
        
        $this->method = $_SERVER['REQUEST_METHOD'];
        if ($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
                $this->method = 'DELETE';
            } else 
                if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
                    $this->method = 'PUT';
                } else {
                    throw new \Exception("Unexpected Header");
                }
        }
        $filter = new Filter();
        $i = 0;
        foreach ($this->args as $arg) {
            $this->args[$i] = $filter->process($this->args[$i]);
            $i++;
        }
        switch ($this->method) {
            case 'DELETE':
                $filter = new Filter();
                $this->request = $filter->process($_POST);
                $this->file = $_FILES;
                $this->method = DELETE;
                break;
            case 'POST':
                $filter = new Filter();
                $this->request = $filter->process($_POST);
                $this->file = $_FILES;
                $this->method = POST;
                break;
            case 'GET':
                $filter = new Filter();
                $this->request = $filter->process($_GET);
                $this->method = GET;
                break;
            case 'PUT':
                $_PUT = array();
                $filter = new Filter();
                parse_str(file_get_contents('php://input'), $_PUT);
                $this->request = $filter->process($_PUT);
                $this->file = file_get_contents("php://input");
                $this->method = PUT;
                break;
            default:
                self::setCode(405);
                $this->_response('Invalid Method');
                break;
        }
    }
    
    
    /**
     * @param number $_code
     */
    public function setCode($_code)
    {
        $this->_code = $_code;
    }
      
    
	/**
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }
    

	/**
     * @param number $type_auth
     */
    public function setType_auth($type_auth)
    {
        $this->type_auth = $type_auth;
    }
    

	/**
     * @param boolean $secureDataActive
     */
    public function setSecureDataActive($mode)
    {
        $this->secureDataActive = $mode;
    }

    /**
     *
     * @param string $type_encryption            
     */
    public function setType_encryption($type_encryption)
    {
        switch ($type_encryption) {
            case "aes-128-ecb":
                $this->type_encryption = "aes-128-ecb";
                break;
            case "aes-128-cbc":
                $this->type_encryption = "aes-128-cbc";
                break;
            case "aes-128-cfb":
                $this->type_encryption = "aes-128-cfb";
                break;
            case "aes-128":
                $this->type_encryption = "aes-128";
                break;
            case "aes-256":
                $this->type_encryption = "aes-256";
                break;
            case "aes-256-native":
                $this->type_encryption = "aes-256-native";
                break;
            default:
                $this->type_encryption = "aes-128-ecb";
                break;
        }
    }

	/**
     * @param boolean $debug
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;
    }

    public function setUidApp($UID){
        $this->uidApp = $UID;
    }
    
    /**
     * 
     * @return string
     */
    public function getUidApp(){
        return $this->uidApp;
    }

	/**
     * @return the $keyApi
     */
    public function getKeyApi()
    {
        return $this->keyApi;
    }

	/**
     * @param String $keyApi
     */
    public function setKeyApi($keyApi)
    {
        $this->keyApi = $keyApi;
    }

	/**
     * @return the $receivedDataIsEncrypted
     */
    public function IsDataEncrypted()
    {
        return $this->receivedDataIsEncrypted;
    }

	/**
     * @param boolean $receivedDataIsEncrypted
     */
    public function setReceivedDataIsEncrypted($isCrypted)
    {
        $this->receivedDataIsEncrypted = $isCrypted;
    }

	/**
     * 
     */
    abstract function login();
    
    /**
     * 
     */
    abstract function version();

    /**
     * @param unknown $compress
     */
    public function setCompress($compress)
    {
        $this->compress = $compress;
    }

	/**
     * @return the $requiredAuh
     */
    public function getRequiredAuh()
    {
        return $this->requiredAuh;
    }

	/**
     * @param boolean $requiredAuh
     */
    public function setRequiredAuh($requiredAuh)
    {
        $this->requiredAuh = $requiredAuh;
    }
    

	/**
     * @return the $engine
     */
    public function getEngine()
    {
        return $this->engine;
    }

	public function processAPI()
    {
        if (true == method_exists($this, $this->endpoint)) {
            $data_info =  array(
                "RESULT" => false,
                'ERROR_CODE' => 104,
                "msg" => ""
            );
            $data = array();
            $payload = array();
            switch ($this->type_auth) {
                case AOUTH_HTTP_HEAD:
                    if(true == self::getRequiredAuh()){
                        if (true == isset($_SERVER['HTTP_AUTHORIZATION'])) {
                            if (true == ! empty($_SERVER['HTTP_AUTHORIZATION'])) {
                                $jws = \SimpleJWS::load($_SERVER['HTTP_AUTHORIZATION']);
                                $public_key = openssl_pkey_get_public(PUB_KEY_FILE);
                                if ($jws->isValid($public_key, 'RS256')) {
                                    $payload = $jws->getPayload();
                                    if (true == isset( $payload['uid'] ) ) {
                                        if (true == ! empty($payload['uid'])) {
                                            
                                            if (true ==isset($payload['encrypted'])) {
                                                self::setSecureDataActive($payload['encrypted']);
                                            }
                                            if (true ==isset($payload['alg'])) {
                                                self::setType_encryption($payload['alg']);
                                            }
                                            if (true ==isset($payload['uid'])) {
                                                self::setUidApp($payload['uid']);
                                            }
                                            if (true ==isset($payload['pk'])) {
                                                $Gibberish = new GibberishAES();
                                                $payload['pk'] = $Gibberish->decrypt($payload['pk'],GENERAL_KEY_256_2);
                                                self::setKeyApi($payload['pk']);
                                            }
                                            self::setCode(200);
                                            $data = $this->{$this->endpoint}($this->args);
                                        } else {
                                            self::setCode(403);
                                            $data_info['RESULT'] = false;
                                            $data_info['ERROR_CODE'] = 2;
                                            $data_info['msg'] = "API EMPTY";
                                        }
                                    }else {
                                        self::setCode(403);
                                        $data_info['RESULT'] = false;
                                        $data_info['ERROR_CODE'] = 3;
                                        $data_info['msg'] = "API HEAD NOT FOUND";
                                    }
                                } else {
                                    self::setCode(403);
                                    $data_info['RESULT'] = false;
                                    $data_info['ERROR_CODE'] = 4;
                                    $data_info['msg'] = "SIGNATURE ERROR";
                                }
                            }else{
                                $data = $this->{$this->endpoint}($this->args);
                                if (true == isset($data['JWT'])) {
                                    if (true == ! empty($data['JWT'])) {
                                        self::setToken($data['JWT']);
                                        self::setCode(200);
                                        $data_info['status'] = true;
                                        unset($data_info['status']);
                                        $data_info['RESULT'] = true;
                                        unset($data_info['ERROR_CODE']);
                                        $data_info['TOKEN'] = $data['JWT'];
                                    } else {
                                        self::setCode(403);
                                        $data_info['RESULT'] = false;
                                        $data_info['ERROR_CODE'] = 5;
                                        $data_info['msg'] = "NOT AUTHORIZED";
                                    }
                                    unset($data);
                                } else {
                                    if(false == $this->requiredAuh){
                                        self::setCode(200);
                                    }else{
                                        self::setCode(403);
                                        $data_info['RESULT'] = false;
                                        $data_info['ERROR_CODE'] = 6;
                                        $data_info['msg'] = "TOKEN INCORRECT";
                                        unset($data);
                                    }
                                }
                            }
                        } else {
                            $data = $this->{$this->endpoint}($this->args);
                            if (true == isset($data['JWT'])) {
                                if (true == ! empty($data['JWT'])) {
                                    self::setToken($data['JWT']);
                                    self::setCode(200);
                                    $data_info['RESULT'] = true;
                                    unset($data_info['ERROR_CODE']);
                                    $data_info['token'] = $data['JWT'];
                                } else {
                                    self::setCode(403);
                                    $data_info['RESULT'] = false;
                                    $data_info['ERROR_CODE'] = 7;
                                    $data_info['msg'] = "TOKEN NOT FOUND";
                                }
                            } else {
		     if(false == $this->requiredAuh){
			self::setCode(200);
                                    }else{
                                        self::setCode(403);
                                        $data_info['RESULT'] = false;
                                        $data_info['ERROR_CODE'] = 6;
                                        $data_info['msg'] = "TOKEN INCORRECT";
                                        unset($data);
                                    }
                            }
                        }    
                    }else{
                        
                    }
                    break;
            }
            if (true == empty($data)) {
                if (isset($data_info['token'])) {
                    if (true ==!empty($data_info['token'])) {
                        unset($data_info['ERROR_CODE']);
                    }else{
                        if (false == isset($data_info['msg']) && true == empty($data_info['msg'])) {
                            $data_info['ERROR_CODE'] = 9;
                            $data_info['msg'] = 'Empty Data';
                        }
                        
                    }
                }else{
                    if (false == isset($data_info['msg']) && true == empty($data_info['msg'])) {
                        $data_info['ERROR_CODE'] = 10;
                        $data_info['msg'] = 'Empty Data';
                    }
                }
                $data = $data_info;
                if (false == $this->debug) {
                    unset($data['msg']);
                }
            }
            if (true == isset($data['msg'])) {
                if (true == !empty($data['msg'])) {
                    $data['ERROR'] = $data['msg'];
                }
                unset($data['msg']);
            }
            $data = \Functions::setLabelsArray($data,CASE_UPPER,true);
            $content = self::_response($data);
            $decryp ="";
            if (true == $this->secureDataActive) {
                if (isset($payload['uid']) ) {
                    if (true == !empty($payload['uid'])) {
                        $tmpo = array();
                        switch ($this->type_encryption) {
                            case "aes-128-ecb":
                                if (true == extension_loaded("aes_ecb")) {
                                    $st = substr(base64_decode($payload['uid']), 0,16);
                                    $tmpo = aes_ecb_encrypt($st,$content);
                                } 
                                else if (true == extension_loaded("mcrypt")) {
                                    $st = substr(base64_decode($payload['uid']), 0,16);
                                    $aes128 = new Encrypt($st);
                                    $tmpo = base64_encode($aes128->encryptt128_ECB($content));
                                } 
                                else {
                                    $aes = new \libs\AES(base64_decode($payload['uid']));
                                    $tmpo = base64_encode($aes->encrypt($content));
                                }
                                break;
                            case "aes-128-cbc":
                                if (true == extension_loaded("mcrypt")) {
                                    $st = substr(base64_decode($payload['uid']), 0,16);
                                    $aes128 = new Encrypt($st);
                                    $tmpo = base64_encode($aes128->encryptt128_CBC($content));
                                } 
                                else {
                                    $aes = new \libs\AES(base64_decode($payload['uid']));
                                    $tmpo = base64_encode($aes->encrypt($content));
                                }
                                break;
                            case "aes-128-cfb":
                                if (true == extension_loaded("mcrypt")) {
                                    $st = substr(base64_decode($payload['uid']), 0,16);
                                    $aes128 = new Encrypt($st);
                                    $tmpo = base64_encode($aes128->encryptt128_CFB($content));
                                }
                                else {
                                    $aes = new \libs\AES(base64_decode($payload['uid']));
                                    $tmpo = base64_encode($aes->encrypt($content));
                                }
                                break;
                            case "aes-256":
                                if(strlen($this->keyApi) == 32 ){
                                    $aes = new GibberishAES();
                                    $tmpo = $aes->encrypt($content,$this->keyApi);
                                }else {
                                    $tmpo = array("error"=>"KEY TOO SHORT");
                                }
                                break;
                            case "aes-256-native":
                                if (true == extension_loaded("aes_ecb")) {
                                    $tmpo = aes_256_encrypt($payload['uid'],$content);
                                }else {
                                    $aes = new \libs\AES(base64_decode($payload['uid']));
                                    $tmpo = base64_encode($aes->encrypt($content));
                                }
                                break;
                            default:
                                if (true == extension_loaded("aes_ecb")) {
                                    $st = substr(base64_decode($payload['uid']), 0,16);
                                    $tmpo = aes_ecb_encrypt($st,$content);
                                } 
                                else if (true == extension_loaded("mcrypt")) {
                                    if(strlen($this->keyApi) == 16 ){
                                        $aes = new Encrypter($this->keyApi);
                                        $tmpo = $aes->encrypt($content);
                                    }else {
                                        $tmpo = array("error"=>"KEY TOO SHORT");
                                    }
                                } 
                                else {
                                    $aes = new \libs\AES(base64_decode($payload['uid']));
                                    $tmpo = base64_encode($aes->encrypt($content));
                                }
                                break;
                        }
                        $content = json_encode(array("PAYLOAD"=>$tmpo)); 
                    }
                }
            }
            self::setHeaders();
            return $content;
        } else {
            if (true == empty($this->endpoint)) {
                self::setCode(403);
                self::setHeaders();
                
            }else{
                self::setCode(403);
                self::setHeaders();
                if (true == $this->debug) {
                    return self::_response(unserialize(ENTRY_NOT_FOUND));
                }else{
                    return self::_response(unserialize(ENTRY_NOT_FOUND));
                }
            }
        }
    }
    
    private function _response($data)
    {
        if (true == is_array($data)) {
            if (true == $this->compress) {
                $data = JSONH::pack($data);
            } 
            return json_encode($data);
        } else {
           if (true == $this->compress) {
                $data = JSONH::pack($data);
            }
            return json_encode($data);
        }
    }

    private function _cleanInputs($data)
    {
        $clean_input = Array();
        if (true == is_array($data)) {
            foreach ($data as $k => $v) {
                $clean_input[$k] = $this->_cleanInputs($v);
            }
        } else {
            $clean_input = trim(strip_tags($data));
        }
        return $clean_input;
    }

    private function getStatus()
    {
        $status = array(
            100 => 'Continue',
            101 => 'Switching Protocols',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => '(Unused)',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported'
        );
        return ($status[$this->_code]) ? $status[$this->_code] : $status[500];
    }

    private function setHeaders()
    {
        if (false == headers_sent()) {
            header("HTTP/1.1 " . $this->_code . " " . $this->getStatus());
            header("Access-Control-Allow-Origin: *");
            header("Access-Control-Allow-Methods: *");
            header("Access-Control-Allow-Headers: *");
            /*  if (true == isset($this->token)) {
             if (true == !empty($this->token)) {
             header("Authorization:" . $this->token);
             }
            } */
            header("Content-Type:" . $this->_content_type);
            header_remove('Server');
            header_remove('X-Powered-By');
            header_remove('Cache-Control');
        }
    }
}
?>
