<?php
/**
 * $Id$
 * @package authentication
 */
require_once dirname(__FILE__).'/../authentication.class.php';
/**
 * This authentication class implements the default authentication method for
 * the platform using md5-encrypted passwords.
 */
class PlatformAuthentication extends Authentication
{
    function PlatformAuthentication()
    {
    }
    public function check_login($user, $username, $password = null)
    {
    	$user_expiration_date = $user->get_expiration_date();
    	
    	if ($user_expiration_date != '0' && $user_expiration_date < time())
    	{
    		return false;
    	}
    	else
    	{
    		return ($user->get_username() == $username && $user->get_password() == md5($password));
    	}
    }
    public function is_password_changeable()
    {
    	return true;
    }
    public function is_username_changeable()
    {
    	return true;
    }
}
?>