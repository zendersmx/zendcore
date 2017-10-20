<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
/**
 * This file contains inteface for deploying rest method
 *
 * PHP version 5.6
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt. If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @descrip this interface has full rest method to create a resful API
 * @category Rest
 * @package libs
 * @author Original Victor Eduardo Sierra Cano <victor.sierra.cano@zenders.mx>
 * @copyright 2012-2020 Zenders Technology S.A de C.V
 * @license http://www.debian.org/misc/bsd.license BSD License (3 Clause)
 * @version Release: 1.0.0
 * @link http://pear.php.net/package/PackageName
 * @example 
 * use libs\Rest;
 * class User_app implements Rest
 *  
 * @history:
 *  Date			Author			Comment			E-mail
 *  01-01-2016	Victor Sierra.		Create it.		<victor.sierra.cano@zenders.mx>
 */
namespace libs;

interface Rest
{
    /**
     * 
     * @param unknown $request
     * @param string $verb
     * @param string $arg
     */
    function _get($data_request = array(), $verb = '', $arg = '' ,$files=array());

    /**
     * 
     * @param unknown $request
     * @param string $verb
     * @param string $arg
     */
    function _post($data_request = array(), $verb = '', $arg = '' ,$file=array());

    /**
     * 
     * @param unknown $request
     * @param string $verb
     * @param string $arg
     */
    function _put($data_request = array(), $verb = '', $arg = '' ,$file=array());

    /**
     * 
     * @param unknown $request
     * @param string $verb
     * @param string $arg
     */
    function _delete($data_request = array(), $verb = '', $arg = '' ,$file=array());
}

?>