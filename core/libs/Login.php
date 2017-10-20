<?php
namespace libs;

interface Login
{

    /**
     * 
     * @param string $UID
     * @param string $password
     * @param string $token
     */
    public function login($UID, $password, $token = "");

    /**
     * 
     * @param string $UID
     */
    public function logout($UID = "");

    /**
     * 
     * @param string $UID
     */
    public function isLogin($UID = "");
}
?>