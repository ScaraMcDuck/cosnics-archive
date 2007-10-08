<?php
/**
 * $Id$
 * @package authentication
 */
require_once dirname(__FILE__).'/../authentication.class.php';
/**
 * This authentication class uses LDAP to authenticate users.
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
		require_once dirname(__FILE__).'/ldapauthenticationconfig.inc.php';
		$ds=ldap_connect($ldap_host,$ldap_port);
		if ($ds){
			ldap_set_option($ds,LDAP_OPT_PROTOCOL_VERSION, 3);
			$filter="(uid=$username)";
			$result=ldap_bind($ds,$ldap_rdn,$ldap_password);
			$sr=ldap_search($ds,$ldap_search_dn,$filter);
			$info = ldap_get_entries($ds, $sr);
			$dn=($info[0]["dn"]);
  			ldap_close($ds);
		}
		if ($dn=='')
		{
			return false;
		}
		if ($password=='')
		{
			return false;
		}
		$ds=ldap_connect($ldap_host,$ldap_port);
		ldap_set_option($ds,LDAP_OPT_PROTOCOL_VERSION, 3);
	 	if (!(@ldap_bind( $ds, $dn , $password)) == true) {
			return false;
		}
		else
		{
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
}
?>