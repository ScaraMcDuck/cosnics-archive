<?php
require_once dirname(__FILE__) . '/../../user/lib/data_manager/database.class.php';
require_once dirname(__FILE__).'/../../common/configuration/configuration.class.php';
require_once dirname(__FILE__).'/../lib/webservice_credential.class.php';
require_once Path :: get_library_path() . 'database/database.class.php';
require_once Path :: get_webservice_path() . 'lib/data_manager/database.class.php';

class WebserviceSecurityManager
{
	private static $instance;	
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
	
	function create_hash($ip, $hash)
	{	
		return $this->dbhash = Hashing :: hash($ip.$hash);
	}
   
	function validate_function($hash)
	{
		$wdm = WebserviceDataManager :: get_instance();
		$wdm->delete_expired_webservice_credentials();
		$credentials = $wdm->retrieve_webservice_credentials_by_ip($_SERVER['REMOTE_ADDR']);
        $credentials = $credentials->as_array();
        if(is_array($credentials))
		{
            foreach($credentials as $c)
            {
               $h = Hashing :: hash($c->get_hash().$SERVER['REMOTE_ADDR']);
               if(strcmp($h , $hash)===0)
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }
		}
		else
		{
			return false; 
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
	
	function validate_login($username,$input_hash)
	{			
		$udm = DatabaseUserDataManager :: get_instance();		
		$user = $udm->retrieve_user_by_username($username);
        $hash = Hashing :: hash ($user->get_password().$_SERVER['REMOTE_ADDR']);
		if(isset($user))
		{						
			if(strcmp($hash, $input_hash)==0) //loginservice validate succesful, credential needed to validate the other webservices
			{				
				$this->credential = new WebserviceCredential(
				array('user_id' => $user->get_id(), 'hash' =>$this->create_hash($_SERVER['REMOTE_ADDR'], $hash), 'time_created' =>time(), 'end_time'=>$this->set_end_time(time()), 'ip' =>$_SERVER['REMOTE_ADDR'])
				);				
				$this->credential->create();				
				return $this->credential->get_default_properties();
			}			
			else
			{
				return 'Wrong hash value submitted.';
			}			
		}
		else
		{
			return "User $username does not exist.";
		}
	}	
	
	
	
	
}

?>