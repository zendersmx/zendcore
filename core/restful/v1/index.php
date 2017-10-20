<?php
/** START DO NOT TOUCH */
error_reporting(E_ALL);
set_time_limit(0);
ini_set("display_errors",1);
ini_set('memory_limit', '-1');
$res_init = ini_set('max_execution_time', 400);
$realPath = explode(DIRECTORY_SEPARATOR . "core", realpath(dirname(__DIR__)));
if (isset($realPath[0])) {
    $realPath = $realPath[0];
}
$strFunc = $realPath.DIRECTORY_SEPARATOR . 'core'.DIRECTORY_SEPARATOR.'libs'.DIRECTORY_SEPARATOR.'Functions.php' ;
$setting = $realPath.DIRECTORY_SEPARATOR . 'core'.DIRECTORY_SEPARATOR.'settings'.DIRECTORY_SEPARATOR.'config.json' ;
if (true == file_exists($strFunc)) {
    include $strFunc;
}
$realPath .= DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'restful' . DIRECTORY_SEPARATOR."v1". DIRECTORY_SEPARATOR;
define("PATH_API_VENDOR", $realPath);

if (! array_key_exists('HTTP_ORIGIN', $_SERVER)) {
    $_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
}
try {
    $object = NULL;
    foreach (scandir(PATH_API_VENDOR, 1) as $key => $value) {
        if ('..' != $value && '.' != $value && 'error_log' != $value && 'index.php' != $value && 'endpoints' != $value) {
            include PATH_API_VENDOR . $value;
            $value = explode('.php', $value);
            if (true == is_array($value)) {
                $value = $value[0];
            }
            $value ="restful\\v1\\".$value;
            if (true == class_exists($value )) {
                if (true == is_subclass_of($value , 'libs\\API')) {
                    $object = new $value($_REQUEST['request'], $_SERVER['HTTP_ORIGIN']);
                    break;
                }
            }
        }
    }
    if (NULL != $object) {
        ob_start("ob_gzhandler");
        $content = $object->processAPI();
        echo $content;
        ob_end_flush();
    }
} catch (Exception $e) {
    echo json_encode(Array(
        'error' => $e->getMessage()
    ));
}
/**
 * END DO NOT TOUCH
 */