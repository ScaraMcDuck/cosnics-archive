<?php
/**
 * $Id$
 * @package authentication
 */
require_once dirname(__FILE__).'/../authentication.class.php';
/**
 * This authentication class uses LDAP to authenticate users.
 * When you want to use LDAP, you might want to change this implementation to
 * match your institutions LDAP structure. You may consider to copy the ldap-
 * directory to something like myldap and to rename the class files. Then you
 * can change your LDAP-implementation without changing this default. Please
 * note that the users in your database should have myldap as auth_source also
 * in that case.
 */
class LdapAuthentication extends Authentication
{
	/**
	 * Constructor
	 */
    function LdapAuthentication()
    {
    }
    public function check_login($user,$username,$password = null)
    {
		include dirname(__FILE__).'/ldap_authentication_config.inc.php';
		$ldap_connect=ldap_connect($ldap_host,$ldap_port);
		if ($ldap_connect){
			ldap_set_option($ldap_connect,LDAP_OPT_PROTOCOL_VERSION, 3);
			$filter="(uid=$username)";
			$result=ldap_bind($ldap_connect,$ldap_rdn,$ldap_password);
			$search_result=ldap_search($ldap_connect,$ldap_search_dn,$filter);
			$info = ldap_get_entries($ldap_connect, $search_result);
			$dn=($info[0]["dn"]);
  			ldap_close($ldap_connect);
		}
		if ($dn=='')
		{
			return false;
		}
		if ($password=='')
		{
			return false;
		}
		$ldap_connect=ldap_connect($ldap_host,$ldap_port);
		ldap_set_option($ldap_connect,LDAP_OPT_PROTOCOL_VERSION, 3);
	 	if (!(@ldap_bind( $ldap_connect, $dn , $password)) == true) {
	 		ldap_close($ldap_connect);
			return false;
		}
		else
		{
			ldap_close($ldap_connect);
			return true;
		}
    }
    public function is_password_changeable()
    {
    	return false;
    }
    public function is_username_changeable()
    {
    	return false;
    }
    public function can_register_new_user()
    {
    	return true;
    }
    public function register_new_user($username,$password = null)
    {
		if($this->check_login(null,$username,$password))
		{
			include dirname(__FILE__).'/ldapauthenticationconfig.inc.php';
			$ldap_connect = ldap_connect( $ldap_host, $ldap_port);
			if ($ldap_connect)
			{
				ldap_set_option($ldap_connect,LDAP_OPT_PROTOCOL_VERSION, 3);
		    	$ldap_bind = ldap_bind($ldap_connect,$ldap_rdn,$ldap_password);
				$filter="(uid=$username)";
		    	$search_result=ldap_search($ldap_connect, $ldap_search_dn, $filter);
		    	$info = ldap_get_entries($ldap_connect, $search_result);
				$user_properties[User:: PROPERTY_LASTNAME] = $info[0]['sn'][0];
				$user_properties[User:: PROPERTY_FIRSTNAME] = $info[0]['givenname'][0];
				$user_properties[User:: PROPERTY_USERNAME] = $username;
				$user_properties[User:: PROPERTY_PASSWORD] = md5('PLACEHOLDER');
				$user_properties[User:: PROPERTY_AUTH_SOURCE] = 'ldap';
				$user_properties[User:: PROPERTY_EMAIL] = $info[0]['mail'][0];;
				$user_properties[User:: PROPERTY_STATUS] = '5';
				$user_properties[User:: PROPERTY_PLATFORMADMIN] = '0';
				$user_properties[User:: PROPERTY_PHONE] = '';
				$user_properties[User:: PROPERTY_OFFICIAL_CODE] = '';
				$user_properties[User:: PROPERTY_PICTURE_URI] = '';
				$user_properties[User:: PROPERTY_CREATOR_ID] = '';
				// User language should be set to interface language selected on login
				$user_properties[User:: PROPERTY_LANGUAGE] = 'english';
				$user = new User(0,$user_properties);
				$user->create();
				return true;
			}
	    	ldap_close($ldap_connect);
		}
		return false;
    }
}
?>