<?php
require_once dirname(__FILE__) . '/../../user/lib/data_manager/database.class.php';
require_once dirname(__FILE__).'/../../common/configuration/configuration.class.php';
require_once dirname(__FILE__).'/../lib/webservice_credential.class.php';

class WebserviceSecurityManager
{
	private static $instance;
	private $dbhash;
	private $credential;
	
	function WebserviceSecurityManager()
	{}
	
	static function get_instance()
	{
		if (!isset(self :: $instance))
		{
			self :: $instance = new WebserviceSecurityManager();
		}
		return self :: $instance;
	}
	
	/*This method creates a hash from a hash,
	 *  based on the concatenation of a given username and password. 
	 */
	
	function create_hash($username, $password)
	{	
		return $this->dbhash = hash('whirlpool',md5($username.''.$password));
	}
	
	function check_hash($hash)
	{
		return strcmp($hash,$this->dbhash);
	}
	
	
	function check_time_left($time)
	{
		return date("Y-h-d",$time);
	}
	
	function validate_login($input_user)
	{
		$udm = DatabaseUserDataManager :: get_instance();		
		$user = $udm->retrieve_user_by_username($input_user[username]);		
		if(isset($user))
		{	
			if(strcmp($user->get_password(),$input_user[password])===0)
			{				
				$this->credential = new WebserviceCredential(array('user_id' => $user->get_id(), 'hash' =>$this->create_hash($username, $password), 'time_created' => time()));
				return $this->credential->get_default_properties();
			}
			else
			{
				return false;
			}			
		}
		else
		{
			return false;
		}
	}	
	
	function complete_login()
	{
		
	}
	
	
}

?>