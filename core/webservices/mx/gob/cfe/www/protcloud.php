<?php
namespace mx\gob\cfe\www;
require 'SoapApi.php';
ini_set('soap.wsdl_cache_enabled',0);
ini_set('soap.wsdl_cache_ttl',0);
header('Content-Type: application/soap+xml; charset=utf-8');
header("X-Powered-By: zendCore 1.0");
include '../../../../../libs/loader.php';
\Loader::classLoader();
try {
    $hdr = file_get_contents("php://input");
    if (strpos($hdr, '<s:Header>') === false) {
        $hdr = null;
    } else {
        $hdr = explode('<s:Header>', $hdr);
        $hdr = explode('</s:Header>', $hdr[1]);
        $hdr = $hdr[0];
    }
    if (true == file_exists('wsprotcloud.wsdl') && true == class_exists('mx\\gob\\cfe\\www\\SoapApi')) {
        $options = array(
            'soap_version' => SOAP_1_2,
            'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_DEFLATE | 5,
            'cache_wsdl' => WSDL_CACHE_NONE,
            'encoding' => 'UTF-8',
            'trace' => 1,
            'exceptions' => true,
            'cache_wsdl' => WSDL_CACHE_NONE
        );
        $server = new \SoapServer('wsprotcloud.wsdl', $options);
        $server->setClass("mx\\gob\\cfe\\www\\SoapApi", $hdr);
        $server->handle();
    } 
} catch (\SOAPFault $f) {
    var_dump($f);
}
?>