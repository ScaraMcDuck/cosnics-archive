<?php
require_once dirname(__FILE__) . '/../../user/lib/data_manager/database.class.php';
require_once dirname(__FILE__).'/../../common/configuration/configuration.class.php';
require_once dirname(__FILE__).'/../lib/webservice_credential.class.php';
require_once dirname(__FILE__).'/../lib/data_manager/database.class.php';

class WebserviceSecurityManager
{
	private static $instance;
	private $dbhash;
	private $credential;
	
	function WebserviceSecurityManager()
	{
	}
	
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
		return $this->dbhash = md5($username.''.$password);
	}
	
	function check_hash($hash)
	{
		if(strcmp($hash,$this->credential->get_hash())===0)
		{
			return true;
		}
		else
		{
			return 'false';
		}
	}
	
	function check_ip($ip)
	{
		if(strcmp($ip,$this->credential->get_ip())===0)
		{
			return true;
		}
		else
		{
			return 'false';
		}
	}	
	
	function set_end_time($time)
	{
		return $endTime = $time + (10*60);  //timeframe 10 mins
		//return date("l, F d, Y h:i" ,$endTime);
	}
	
	function set_create_time($time)
	{		
		return date("l, F d, Y h:i" ,$time);
	}
	
	function check_time_left($endTime)
	{
		if(time() > $endtime)
		{
			return 'your available time has been used up.';
		}
		else
		{			
			$restTime = $endTime - time();
			return 'you have ' . $endTime . ' time left.';
		}
	}
	
	function validate_login($input_user,$ip)
	{
		
		$udm = DatabaseUserDataManager :: get_instance();		
		$user = $udm->retrieve_user_by_username($input_user[username]);	
		if(isset($user))
		{						
			if(strcmp($user->get_password(),$input_user[password])===0)
			{				
				$this->credential = new WebserviceCredential(
				array('user_id' => $user->get_id(), 'hash' =>$this->create_hash($username, $password), 'time_created' =>time(), 'end_time'=>$this->set_end_time(time()), 'ip' =>$ip)
				);
				$this->credential->create();
				return $this->credential->get_default_properties();
			}			
			else
			{
				return 'Wrong password submitted.';
			}			
		}
		else
		{
			return "User $input_user[username] does not exist.";
		}
	}	
	
	
	
	
}

?>