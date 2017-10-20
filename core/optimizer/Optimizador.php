<?php
/**
 *
 * @author victor
 *
 */
class Optimizador
{

    /**
     */
    function __construct()
    {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_secure', 1);
        header('Accept-Ranges: bytes');
        Header("Cache-Control: must-revalidate");
        $offset = 60 * 60 * 24 * 3;
        $ExpStr = "Expires: " . gmdate("D, d M Y H:i:s", time() + $offset) . " GMT";
        Header($ExpStr);
        header("Cache-Control: maxage=14400");
        header("Cache-Control: public, must-revalidate");
        header("Cache-Control: public");
        header("Accept-Encoding:Accept-Encoding Vary");
        header('Content-Type: text/html; charset=utf-8');
        setlocale(LC_TIME, 'es_MEX');
        header("Cache-Control: cache");
        header('X-Powered-By: Zender Core V1.0');
        header('Server: ZENDER server');
        header("pragma: public");
        header("Pragma: cache");
    }

    public static function compresorPagina($buffer)
    {
        $search = array(
            '>\n',
            '\n',
            '> ',
            '	<',
            '		<',
            '	<',
            '		<',
            '			<',
            '		<',
            '	<',
            '				<',
            '	<',
            '	<',
            '        <',
            '/\>[^\S ]+/s',
            '            ',
            '    <',
            '	<',
            '    <',
            '				  ',
            '			',
            '

				
		',
            '//Uis',
            '/[[:blank:]]+/' . '		',
            '	',
            '		',
            '    ',
            '   ',
            '[[:space:]]+',
            "\n"
        );
        
        $replace = array(
            '>',
            '',
            '>',
            '<',
            '<',
            '<',
            '<',
            '<',
            '<',
            '<',
            '<',
            '<',
            '<',
            '<',
            '>',
            '',
            '<',
            '<',
            '<',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            ''
        );
        return str_replace($search, $replace, $buffer);
    }

    public function imprimir_paginazip()
    {
        $HTTP_ACCEPT_ENCODING = '';
        if (headers_sent())
            $encoding = false;
        elseif (strpos($HTTP_ACCEPT_ENCODING, 'x-gzip') !== false)
            $encoding = 'x-gzip';
        elseif (strpos($HTTP_ACCEPT_ENCODING, 'gzip') !== false)
            $encoding = 'gzip';
        else
            $encoding = false;
        if ($encoding) {
            $contents = ob_get_contents();
            ob_end_clean();
            header('Content-Encoding: ' . $encoding);
            print("\x1f\x8b\x08\x00\x00\x00\x00\x00");
            $size = strlen($contents);
            $contents = gzcompress($contents, 9);
            $contents = substr($contents, 0, $size);
            print($contents);
        } else {
            @ob_end_flush();
        }
    }
}
?>