<?php
use libs\Language;
class Init
{
    function __construct($info = NUll)
    {
        if (isset($info)) {
            foreach (scandir(getcwd().'/'.CONTROLLER, 1) as $key => $value) {
                if (strtolower($value) === strtolower($info['eventpage'] . EXT) || $value === $info['eventpage'] . EXT ) {
                    $namefile = $value;
                    break;
                }
            }
            if (true == empty($namefile)) {
                if (is_dir(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . CONTROLLER)) {
                    $fp = fopen(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . CONTROLLER . strtolower($info['eventpage'] . EXT), 'a+');
                    $namefile = substr($namefile, 0, - strlen(EXT));
                    $namefile = ucwords(strtolower(trim($info['eventpage'])));
                    $string = file_get_contents('skeletonController.php');
                    $string = str_repeat("NameClass", $namefile );
                    fwrite($fp, $string );
                    fclose($fp);
                    $namefile = strtolower($info['eventpage'] . EXT);
                }
            } 
            if ($namefile != "" && true == file_exists(CONTROLLER . $namefile)) {
                if($namefile=='default_view.php')
                    $namefile = "Home.php";
                include getcwd().'/'.CONTROLLER . $namefile;
                $namefile = substr($namefile, 0, - strlen(EXT));
                $obj = NULL;
               
               /*  var_dump($namefile);
                var_dump(ucfirst($namefile));
                var_dump(class_exists($namefile));
                var_dump(class_exists(ucfirst($namefile))); */
                
                if (true == class_exists(ucfirst($namefile))) {
                    $namefile = ucfirst($namefile);
                    $obj = new $namefile();
                } elseif (true == class_exists($namefile)) {
                    $obj = new $namefile($info);
                }
                if (NULL != $obj) {
                    $object_name = get_class($obj);
                    if (strtolower($object_name) === strtolower($namefile)) {
                        $lang = new Language();
                        $data = array();
                        if (true == method_exists($obj, "index")) {
                            $obj->index($info);
                            $data = $obj->data;
                            $data['SERVER'] = Loader::name_server_static() . "design";
                            $data['ROOT'] = Loader::name_server_static();
                            $data['LANG'] = $info['eventlang'];
                            echo  $lang->create_template($info['eventlang'], strtolower($info['eventpage']), $data);
                        }
                    }
                }
            }
        }
    }
} 