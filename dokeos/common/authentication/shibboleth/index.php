<?php
require_once dirname(__FILE__) . '/shibboleth_authentication.class.php';

$shibAuth = new ShibbolethAuthentication();
$shibAuth->check_login();

?>