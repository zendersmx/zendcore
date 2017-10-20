<?php
namespace libs;


class Encrypter {
    protected $key;
    protected $cipher = MCRYPT_RIJNDAEL_256;
    protected $data;
    protected $mode = MCRYPT_MODE_CBC;
    protected $IV;
    /**
     *
     * @param type $data
     * @param type $key
     * @param type $blockSize
     * @param type $mode
     */
    function __construct($key = null, $blockSize = null, $mode = null) {
        $this->setKey($key);
        $this->setBlockSize($blockSize);
        $this->setMode($mode);
    }

   

    /**
     *
     * @param type $key
     */
    public function setKey($key) {
        $this->key = $key;
    }

    /**
     *
     * @param type $blockSize
     */
    public function setBlockSize($blockSize) {
        switch ($blockSize) {
            case 128:
                $this->cipher = MCRYPT_RIJNDAEL_128;
                break;

            case 192:
                $this->cipher = MCRYPT_RIJNDAEL_192;
                break;

            case 256:
                $this->cipher = MCRYPT_RIJNDAEL_256;
                break;
        }
    }

    /**
     *
     * @param type $mode
     */
    public function setMode($mode) {
        switch ($mode) {
            case M_CBC:
                $this->mode = MCRYPT_MODE_CBC;
                break;
            case M_CFB:
                $this->mode = MCRYPT_MODE_CFB;
                break;
            case M_ECB:
                $this->mode = MCRYPT_MODE_ECB;
                break;
            case M_NOFB:
                $this->mode = MCRYPT_MODE_NOFB;
                break;
            case M_OFB:
                $this->mode = MCRYPT_MODE_OFB;
                break;
            case M_STREAM:
                $this->mode = MCRYPT_MODE_STREAM;
                break;
            default:
                $this->mode = MCRYPT_MODE_ECB;
                break;
        }
    }
    
    private function pkcs5_pad ($text, $blocksize) {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }
    
    public function encrypt($input) {
        $size = mcrypt_get_block_size($this->cipher, $this->mode);
        $input = self::pkcs5_pad($input, $size);
        $td = mcrypt_module_open($this->cipher, '', $this->mode, '');
        $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, $this->key, $iv);
        $data = mcrypt_generic($td, $input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $data = base64_encode($data);
        return  $data;
    }
    
    public function decrypt($sStr) {
        $decrypted= mcrypt_decrypt($this->cipher,$this->key,base64_decode($sStr),$this->mode);
        preg_replace('/[\x{FFFF}-\x{FFFF}]+/u','',$decrypted);
        $decrypted = rtrim($decrypted,"\x00..\x1F");
        $decrypted = trim($decrypted);
        return $decrypted;
    }

}