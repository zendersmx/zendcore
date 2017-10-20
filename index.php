<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
$tiempo_inicio = microtime(true);
include 'core/optimizer/Optimizador.php';
include 'core/libs/Functions.php';
include 'core/libs/loader.php';
$data_json = Functions::convertObjToArray(json_decode(file_get_contents('core/settings/config.json')));
foreach ($data_json as $key => $object) {
    if ($key != 'VIEWS_SET' && $key != 'LANGUAGE_SET') {
        foreach ($object as $object_key => $value_object) {
            if (true!=empty($value_object)) {
                define($object_key, $value_object);
            }
        }     
    }
}
if (true == isset($data_json['VIEWS_SET']) && true == ! empty($data_json['VIEWS_SET'])) {
    foreach ($data_json['VIEWS_SET'] as $lang) {
        $GLOBALS['VIEWS'][] = strtolower($lang);
    }
}
if (true == isset($data_json['LANGUAGE_SET']) && true == ! empty($data_json['LANGUAGE_SET'])) {
    foreach ($data_json['LANGUAGE_SET'] as $lang) {
        $GLOBALS['LANGUAGE'][] = strtolower($lang);
    }
}
$optimizador = new Optimizador();
$loader = new Loader();
ob_start('Optimizador::compresorPagina');
$loader->handler();
$optimizador->imprimir_paginazip();
$tiempo_final = microtime(true);
$tiempo = $tiempo_final - $tiempo_inicio;
if (true == $data_json['SYSTEM_SET']['DEBUG']) {
    echo "<br><p class='lead'>Time request :", $tiempo, " seconds</p>";
}

?>

