<?php
/**
 * $Id$
 * @package authentication
 */
require_once dirname(__FILE__).'/../authentication.class.php';
class PlatformAuthentication extends Authentication
{
    function PlatformAuthentication()
    {
    }
    public function check_login($user,$username,$password = null)
    {
		return ($user->get_username() == $username && $user->get_password() == md5($password));
    }
    public function is_password_changeable()
    {
    	return true;
    }
    public function is_username_changeable()
    {
    	return true;
    }
    public function can_register_new_user()
    {
    	return false;
    }
}
?>