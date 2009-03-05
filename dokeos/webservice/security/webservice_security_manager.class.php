<?php
require_once dirname(__FILE__) . '/../../user/lib/data_manager/database.class.php';
require_once dirname(__FILE__).'/../../common/configuration/configuration.class.php';

class WebserviceSecurityManager
{
	private static $instance;
	private $dbhash;
	
	function WebserviceSecurityManager()
	{}
	
	static function get_instance()
	{
		dump('tetn');
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
		$dbhash = md5($username.''.$password);
		$dbhash = hash('whirlpool',$dbhash);
		//write to db
		return $dbhash;
	}
	
	
	function check_time_left($time)
	{
		$convertime = date("Y-h-d",$time);
	}
	
	function validate_login($input_user)
	{
		$udm = DatabaseUserDataManager :: get_instance();		
		$user = $udm->retrieve_user_by_username($input_user[username]);		
		if(isset($user))
		{	
			if(strcmp($user->get_password(),$input_user[password])===0)
			{				
				//return $this->create_hash($username, $password);
				return true;
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
	
	
}

?>