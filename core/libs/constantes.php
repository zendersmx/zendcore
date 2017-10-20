<?php
#raiz
if (false == defined('MODULO')) {
    define( 'MODULO','/');
}
if (false == defined('PATH')) {
    define( 'PATH',basename("core"));
}
if (false == defined('SLASH')) {
    define( 'SLASH',DIRECTORY_SEPARATOR);
}
if (false == defined('EXT')) {
    define( 'EXT','.php');
}
if (false == defined('CONTROLLER')) {
    define("CONTROLLER", PATH.SLASH."controller".SLASH);
}
if (false == defined('VIEW')) {
    define("VIEW", PATH.SLASH."view".SLASH);
}
if (false == defined('HELPER')) {
    define("HELPER", PATH.SLASH."helpers".SLASH);
}
if (false == defined('VIEW_INICIO')) {
    define("VIEW_INICIO", "home");
}
if (false == defined('GENERAL_KEY')) {
    define("GENERAL_KEY", "hfg95Fh2Arnf9_rM");
}
if (false == defined('GENERAL_KEY_256')) {
    define("GENERAL_KEY_256", "RHEk?@CJBp5J*NLsQ4#M9kMbUN*YV77q");
}
if (false == defined('GENERAL_KEY_256_2')) {
    define("GENERAL_KEY_256_2", "kyapWHV93NRzN4XdWh28GGbbhbmuVKA5");
}

if (false == defined('SYNC')) {
    define("SYNC", "1000");
}
if (false == defined('MAX_TRIES')) {
    define("MAX_TRIES", 5);
}
if (false == defined('TIME_USER_OFFLINE')) {
    define("TIME_USER_OFFLINE", 5);
}

if (false == defined('M_CBC')) {
    define( 'M_CBC','cbc');
}
if (false == defined('M_CFB')) {
    define( 'M_CFB','cfb');
}
if (false == defined('M_ECB')) {
    define( 'M_ECB','ecb');
}
if (false == defined('M_NOFB')) {
    define( 'M_NOFB','nofb');
}
if (false == defined('M_OFB')) {
    define( 'M_OFB','ofb');
}
if (false == defined('M_STREAM')) {
    define( 'M_STREAM','ofb');
}

if (false == defined('CORRECT')) {
    define ("CORRECT", serialize (array('RESULT' => true)));
}

if (false == defined('NOT_AUTORIZATION')) {
    define ("NOT_AUTORIZATION", serialize (array('RESULT' => false,'ERROR_CODE' => 5,'ERROR' => 'NOT AUTHORIZED')));
}
if (false == defined('ERROR_DATABASE')) {
    define ("ERROR_DATABASE", serialize (array('RESULT' => false,'ERROR_CODE' => 101,'ERROR' => 'ERROR ON DATABASE')));
}
if (false == defined('DATA_EMPTY')) {
    define ("DATA_EMPTY", serialize (array('RESULT' => false,'ERROR_CODE' => 102,'ERROR' => 'DATA EMPTY')));
}
if (false == defined('SYNTAXIS_WRONG')) {
    define ("SYNTAXIS_WRONG", serialize (array('RESULT' => false,'ERROR_CODE' => 103,'ERROR' => 'SYNTAXIS WRONG')));
}
if (false == defined('RESULT_NOT_FOUND')) {
    define ("RESULT_NOT_FOUND", serialize (array('RESULT' => false,'ERROR_CODE' => 104,'ERROR' => 'RESULT NOT IMPLEMENTED')));
}
if (false == defined('ENTRY_NOT_FOUND')) {
    define ("ENTRY_NOT_FOUND", serialize (array('RESULT' => false,'ERROR_CODE' => 105,'ERROR' => 'ENTRY NOT FOUND')));
}

if (false == defined('ONLYREAD')) {
    define ("ONLYREAD", 'ONLYREAD');
}
if (false == defined('ONLYWRITE')) {
    define ("ONLYWRITE", 'ONLYWRITE');
}
if (false == defined('READWRITE')) {
    define ("READWRITE", 'READWRITE');
}


$realPath = explode(DIRECTORY_SEPARATOR ."core", realpath(dirname(__DIR__)));
if (isset($realPath[0])) {
    $realPath = $realPath[0];
}
if (false == defined('REAL_PATH')) {
    define("REAL_PATH", $realPath);
}