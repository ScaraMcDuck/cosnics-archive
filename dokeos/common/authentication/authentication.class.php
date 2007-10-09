<?php
/**
 * $Id$
 * @package authentication
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
    abstract function can_register_new_user();
    /**
     * Logs the current user out of the platform. The different authentication
     * methods can overwrite this function if additional operations are needed
     * before a user can be logged out.
     * @param User $user The user which is logging out
     */
    function logout($user)
    {
    	api_session_destroy();
    }
}
?>