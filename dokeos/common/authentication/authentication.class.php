<?php
/**
 * $Id$
 * @package authentication
 */
/**
 * An abstract class for handling authentication. Impement new authentication
 * methods by creating a class which extends this abstract class.
 */
abstract class Authentication {
	/**
	 * Constructor
	 */
    function Authentication() {
    }
    /**
     * Checks if the given username and password are valid
     * @param string $username
     * @param string $password
     * @return true
     */
    abstract function check_login($user,$username,$password = null);
    /**
     * Checks if this authentication method allows the password to be changed.
     * @return boolean
     */
    abstract function is_password_changeable();
    /**
     * Checks if this authentication method allows the username to be changed.
     */
    abstract function is_username_changeable();
    /**
     * Checks if this authenticaion method is able to register new users based
     * on a given username and password
     */
    public function can_register_new_user()
    {
    	return false;
    }
    /**
     * Registers a new user
     * @param string $username
     * @param string $password
     * @return boolean True on success, false if not
     */
    public function register_new_user($username,$password = null)
    {
    	return false;
    }
    /**
     * Logs the current user out of the platform. The different authentication
     * methods can overwrite this function if additional operations are needed
     * before a user can be logged out.
     * @param User $user The user which is logging out
     */
    function logout($user)
    {
    	Session :: destroy();
    }
    
    function is_valid()
    {
    	// TODO: Add system here to allow authentication via encrypted user key ?
    	if (!Session :: get_user_id())
    	{
    		return false;
    	}
    	else
    	{
			return true;
    	}
    }
    
    /**
     * Creates an instance of an authentication class
     * @param string $authentication_method
     * @return Authentication An object of a class implementing this abstract
     * class.
     */
    function factory($authentication_method)
    {
		$authentication_class_file = dirname(__FILE__).'/'.$authentication_method.'/'.$authentication_method.'_authentication.class.php';
		$authentication_class = DokeosUtilities :: underscores_to_camelcase($authentication_method).'Authentication';
		require_once $authentication_class_file;
		return new $authentication_class;
    }
}
?>