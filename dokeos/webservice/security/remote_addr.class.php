<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
echo remote_addr ::getIP();
/**
 * Description of remote_addr
 *
 * @author Soliber
 */
class remote_addr
{
    static function getIP()
    {
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if (isset($_SERVER['REMOTE_ADDR'])) $ip = $_SERVER['REMOTE_ADDR'];
    else $ip = "UNKNOWN";
    return $ip;
    }
}
?>
