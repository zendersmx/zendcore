<?php
namespace libs;

class Request
{
    const __DEFAULT = 0, __JSON = 1, __ARRAY = 2;
    
    private $host="";
    private $api="";
    private $headers=array();
    private $resultformat =Request::__ARRAY;
    
    /**
     * 
     * @param unknown $host
     * @param unknown $api
     */
    function __construct($host,$api){
        $this->host= $host;
        $this->api= $api;
    }
    
    /**
     * 
     * @param unknown $val
     */
    public function setResultFormat($val){
        if ($val == Request::__DEFAULT  || $val == Request::__JSON || $val == Request::__ARRAY) {
            $this->resultformat = $val;
        }
    }
    
    /**
     * 
     * @param unknown $header
     */
    public function addHeader($header){
        $this->headers[] = $header;
    }
    /**
     * 
     */
    public function cleanHeader(){
        $this->headers = array();
    }
    /**
     * 
     * @param unknown $endPoint
     * @param unknown $data
     * @return mixed
     */
    function post($endPoint,$data){
        $verb = "/api/v1/login";
        $fields_string = "";
        $i = 0;
        foreach ($data as $key => $value) {
            $fields_string .= $key . '=' . $value . '&';
            $i++;
        }
        rtrim($fields_string, '&');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->host .$this->api . $endPoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, $i);
        if(count($this->headers)>0)
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        $result = curl_exec($ch);
        curl_close($ch);
        unset($ch);
        if($result){
            if ($this->resultformat == Request::__JSON || $this->resultformat == Request::__ARRAY) {
                $result = json_decode($result);
                if ($this->resultformat == Request::__ARRAY) {
                    if($result){
                        $result = \Functions::convertObjToArray($result);
                    }    
                }
            }
        }
        return $result;
    }
    
    function get($endPoint){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->host .$this->api . $endPoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        if(count($this->headers)>0)
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        $result = curl_exec($ch);
        curl_close($ch);
        unset($ch);
        if($result){
            if ($this->resultformat == Request::__JSON || $this->resultformat == Request::__ARRAY) {
                $result = json_decode($result);
                if ($this->resultformat == Request::__ARRAY) {
                    if($result){
                        $result = \Functions::convertObjToArray($result);
                    }    
                }
            }
        }
        return $result;
    }
    
    function put($endPoint){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->host .$this->api . $endPoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        if(count($this->headers)>0)
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        $result = curl_exec($ch);
        curl_close($ch);
        unset($ch);
        if($result){
            if ($this->resultformat == Request::__JSON || $this->resultformat == Request::__ARRAY) {
                $result = json_decode($result);
                if ($this->resultformat == Request::__ARRAY) {
                    if($result){
                        $result = \Functions::convertObjToArray($result);
                    }
                }
            }
        }
        return $result;
    }
    
    function delete($endPoint){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->host .$this->api . $endPoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        if(count($this->headers)>0)
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        $result = curl_exec($ch);
        curl_close($ch);
        unset($ch);
        if($result){
            if ($this->resultformat == Request::__JSON || $this->resultformat == Request::__ARRAY) {
                $result = json_decode($result);
                if ($this->resultformat == Request::__ARRAY) {
                    if($result){
                        $result = \Functions::convertObjToArray($result);
                    }
                }
            }
        }
        return $result;
    }
    
}

?>