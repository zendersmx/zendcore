<?php
namespace restful\v1;

use libs\API;

include '../../libs/API.php';
define("KEY_FILE", "file://" . SSL_FILE_PATH . "ca.key");
define("PUB_KEY_FILE", "file://" . SSL_FILE_PATH . "pub_key.pub");

const SSL_PASSWORD = "test";

/**
 *
 * @author victor
 *        
 */
class ResfulCore extends API
{

    /**
     *
     * @var version of API
     */
    private $version_api = "1.0.1";

    function __construct($request, $origin)
    {
        parent::setDebug(true);
        parent::setSecureDataActive(true);
        parent::setType_encryption("aes-128");
        parent::setReceivedDataIsEncrypted(false);
        parent::setType_auth(AOUTH_HTTP_HEAD);
        parent::__construct($request, $origin);
    }

    public function version()
    {
        parent::setRequiredAuh(false);
        $data = array(
            "VERSION" => $this->version_api,
            "ENGINE" => self::getEngine()
        );
       return $data;
    }

    public function login()
    {
        
    }
}
?>