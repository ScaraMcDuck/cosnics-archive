<?php
require_once dirname(__FILE__) . '/../lib/user_data_manager.class.php';

class WebserviceSecurityManager
{
	private static $instance;
	private $dbhash;
	
	static function get_instance()
	{
		if (!isset (self :: $instance))
		{			
			require_once Path :: get_library_path() . 'webservices/webservice_security_manager.class.php';
			$class = 'WebserviceSecurityManager';
			self :: $instance = new $class ();
		}
		return self :: $instance;
	}
	
	function create_hash($username, $password) //methode to create the hash, and store it in the db
	{	
		$parameters = $username.''.$password;
		$hash = md5($parameters);
		$hash = hash('whirlpool',$hash);
		echo 'hash : ' . $hash;
		//naar de database schrijven
		return $hash;
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
	
	function validate($input_user)
	{
		
		$udm = DatabaseUserDataManager :: get_instance();		
		$user = $udm->retrieve_user_by_username($input_user[username]);		
		if(isset($user)) //user exists
		{	
			$username = $input_user[username];		
			$db_password = $user->get_password();			
			$password = $input_user[password];
			if($db_password == $password) //check passwords
			{				
				return $this->create_hash($username, $password); //create hash
			}
			else
			{
				return false;
			}			
		}
		else
		{
			return 'input is not an object';
		}
	}	
	
	
}

?>