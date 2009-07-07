<?php
/** 
 * This file allows to see the current Shibboleth user attribute values 
 * that are sent to Dokeos. It can be useful to debug the Shibboleth authentication. 
 */

require_once dirname(__FILE__) . '/shibboleth_authentication.class.php';

/*
 * Set this security code to whatever you want if you 
 * want to disable users to see their own Shibboleth infos by accessing the URL
 * of this file
 */
$security_code = '';

if (strlen($security_code) > 0)
{
    $request = new Request();
    if ($request->get('code') != $security_code)
    {
        die();
    }
}

$shibAuth = new ShibbolethAuthentication();
$shibAuth->print_shibboleth_attributes();

?>