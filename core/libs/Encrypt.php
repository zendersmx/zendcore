<?php
namespace libs;
final class Encrypt
{

    private $securekey;
    private $key128;
    private $iv;

    /**
     * inicializa con la clave del AES_256
     * 
     * @param String $key            
     */
    function __construct($key)
    {
        $this->securekey = hash('sha256', $key, TRUE);
        $this->key128 = $key;
        $this->iv = mcrypt_create_iv(32);
    }

    /**
     *
     * @param unknown $text            
     * @return string
     */
    function encrypt($text)
    {
        return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->securekey, $text, MCRYPT_MODE_ECB, $this->iv));
    }

    /**
     *
     * @param unknown $textEncryt            
     * @return string
     */
    function decrypt($textEncryt)
    {
        return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->securekey, base64_decode($textEncryt), MCRYPT_MODE_ECB, $this->iv));
    }
    
    function decrypt128($str){
        $this->iv = mcrypt_create_iv(16);
        $str = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->key128, $str, MCRYPT_MODE_ECB,$this->iv);
        $str = str_replace("\0", "",$str);
        return $str;
    }
    
    function decrypt128_ebc($str){
        $this->iv = mcrypt_create_iv(16);
        $str = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->key128, $str, MCRYPT_MODE_ECB,$this->iv);
        $str = str_replace("\0", "",$str);
        return $str;
    }
    
    function encryptt128($str){
        $this->iv = mcrypt_create_iv(16);
        $str = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $this->key128, $str, MCRYPT_MODE_ECB,$this->iv);
        return $str;
    }
    
    function encryptt128_ECB($str){
        $this->iv = mcrypt_create_iv(16);
        $str = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $this->key128, $str, MCRYPT_MODE_ECB,$this->iv);
        return $str;
    }
  
    function encryptt128_CBC($str){
        $this->iv = mcrypt_create_iv(16);
        $str = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $this->key128, $str, MCRYPT_MODE_CBC,$this->iv);
        return $str;
    }
    
    function encryptt128_CFB($str){
        $this->iv = mcrypt_create_iv(16);
        $str = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $this->key128, $str, MCRYPT_MODE_CFB,$this->iv);
        return $str;
    }
    function encryptt128_NOFB($str){
        $this->iv = mcrypt_create_iv(16);
        $str = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $this->key128, $str, MCRYPT_MODE_NOFB,$this->iv);
        return $str;
    }
}

?>