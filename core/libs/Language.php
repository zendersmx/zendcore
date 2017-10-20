<?php
namespace libs;

class Language
{

    /**
     *
     * @var string $language
     */
    private $language;

    function __construct()
    {
        global $DEFAULT_LANGUAGE;
        if (true == isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            if ($_SERVER['HTTP_ACCEPT_LANGUAGE']) {
                $this->languages = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
                $this->language = substr($this->languages, 0, 2);
            } else {
                if (true ==isset($_SERVER['HTTP_USER_AGENT'])) {
                    if ($_SERVER['HTTP_USER_AGENT']) {
                        $this->user_agent = explode(";", $_SERVER['HTTP_USER_AGENT']);
                        for ($i = 0; $i < sizeof($this->user_agent); $i ++) {
                            $this->languages = explode("-", $this->user_agent[$i]);
                            if (sizeof($this->languages) == 2) {
                                if (strlen(trim($this->languages[0])) == 2) {
                                    $size = sizeof($this->language);
                                    $this->language[$size] = trim($this->languages[0]);
                                }
                            }
                        }
                        $this->language[0];
                    } else {
                        $this->language = $DEFAULT_LANGUAGE;
                    }
                }else{
                    $this->language = $DEFAULT_LANGUAGE;
                }
            }    
        }else{
            $this->language = $DEFAULT_LANGUAGE;
        }
    }

    /**
     *
     * @return string
     */
    public function __toString()
    {
        return "The language set is: $this->language";
    }

    function __destruct()
    {
        unset($this);
    }

    /**
     *
     * @return the $language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     *
     * @param string $language            
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     *
     * @param string $lang            
     * @return array $arrayDictionary
     */
    public function get_content_lang($lang = '')
    {
        $arrayDictionary = array();
        $realPath = explode(DIRECTORY_SEPARATOR . "core", realpath(dirname(__DIR__)));
        if (isset($realPath[0])) {
            $realPath = $realPath[0];
        }
        $nameDicc = '';
        if (true == ! empty($lang)) {
            $nameDicc = $realPath . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR . strtolower($lang) . ".json";
        } else 
            if (true == ! empty($this->language)) {
                
                $nameDicc = $realPath . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR . strtolower($this->language) . ".json";
            }

        if (true == file_exists($nameDicc)) {
            $nameDicc = json_decode(file_get_contents($nameDicc));
            if(null==$nameDicc){
                $arrayDictionary = array();
            }else{
                $arrayDictionary = \Functions::convertObjToArray($nameDicc);
            }
        }
        return $arrayDictionary;
    }

    /**
     *
     * @param unknown $lang            
     * @param unknown $templateName            
     * @param unknown $data            
     * @return string
     */
    public function create_template($lang, $templateName, $data = array())
    {
        $array = self::get_content_lang($lang);
        $realPath = getcwd();
        $nameFileHtml = "";
        $template = '';
        
        if (true == ! empty($lang) && true == ! empty($templateName)) {
            $nameFileHtml .= $realPath . DIRECTORY_SEPARATOR . 'design' . DIRECTORY_SEPARATOR;
            if (null != HTML_VIEWS) {
                $nameFileHtml .= HTML_VIEWS . DIRECTORY_SEPARATOR;
            } else {
                $nameFileHtml .= 'html' . DIRECTORY_SEPARATOR;
            }
            $nameFileHtml .= $templateName . '.php';
            $nameFileHtml = strtolower($nameFileHtml);
            if (true == file_exists($nameFileHtml)) {
                $template = file_get_contents($nameFileHtml);
                foreach ($data as $clave => $valor)
                    $template = str_replace('{' . $clave . '}', $valor, $template);
                foreach ($array as $clave => $valor)
                    $template = str_replace('{' . $clave . '}', $valor, $template);
                
            }else {
                if (true == file_exists($nameFileHtml)) {
                    $fp= fopen($nameFileHtml, 'a+');
                    fwrite($fp, '
<!DOCTYPE html>
<html lang="{#languague}">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>'.$templateName.'</title>
                    
<!-- BEGIN META -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="keywords" content="your,keywords">
<meta name="description" content="Template autogenerated with AIcore Framework">
<!-- END META -->
</head>
<body>
	<header id="header"></header>
	<div id="base">'.$templateName.'</div>
</body>
</html>');
                    fclose($fp);
                    $template = file_get_contents($nameFileHtml);
                    foreach ($data as $clave => $valor)
                        $template = str_replace('{#' . $clave . '}', $valor, $template);
                    foreach ($array as $clave => $valor)
                        $template = str_replace('{' . $clave . '}', $valor, $template);
                }
            }
        }
        return $template;
    }
}

?>