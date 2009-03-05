<?php
require_once dirname(__FILE__) . '/../../user/lib/user_data_manager.class.php';
require_once dirname(__FILE__).'/../../common/configuration/configuration.class.php';

class WebserviceSecurityManager
{
	private static $instance;
	private $dbhash;
	
	static function get_instance()
	{
		if (!isset (self :: $instance))
		{
			$type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
			require_once dirname(__FILE__).'/data_manager/'.strtolower($type).'.class.php';
			$class = $type.'WebserviceSecurityManager';
			self :: $instance = new $class ();
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
		//echo 'hash : ' . $hash;
		//naar de database schrijven
		return $dbhash;
	}
	
	function validate_hash($hash)
	{
		//$dbhash uit de dbhalen
		
		if($hash == $dbhash);
		{
			echo 'hash correct'
		}
		else
		{
			echo 'hash incorrect';
		}
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