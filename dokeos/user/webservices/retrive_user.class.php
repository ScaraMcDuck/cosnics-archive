<?php
require_once(dirname(__FILE__) . '/../../common/global.inc.php');
require_once dirname(__FILE__) . '/../../common/webservices/webservice.class.php';
require_once dirname(__FILE__) . '/../../user/lib/user.class.php';
require_once dirname(__FILE__) . '/../../common/webservices/action_success.class.php';
require_once Path :: get_webservice_path() . '/security/webservice_security_manager.class.php';
require_once Path :: get_webservice_path() . '/lib/webservice_credential.class.php';

$handler = new RetriveUser();
$handler->run();

class RetriveUser
{
	private $webservice;
	private $functions;	
	
	function RetriveUser()
	{
		$this->webservice = Webservice :: factory($this);
		$this->wsm = WebserviceSecurityManager :: get_instance();
	}
	
	function run()
	{	
		
		$functions = array();
		
		$functions['retrive'] = array(
			'input' => new User(),
			'output' => new WebserviceCredential(),
			'require_hash' => false
		);
		
		$this->webservice->provide_webservice($functions); //function and no hash
	}	
	
	function retrive($user)
	{		
		if(is_array($user))
		{			
			$input_password = hash('md5',$user[password]);
			$input_string = $input_password .''.$_SERVER['REMOTE_ADDR'];						
			$input_hash = hash('sha1',$input_string);			
			$hash =  $this->wsm->validate_login($user[username],$input_hash); //no password will be sent, only the username//hash
			if(!empty($hash) && gettype($hash)=='array')
			{
				return $hash;
			}
			else
			{
				return $this->webservice->raise_message($hash);
			}
		}
		else
		{
			return $this->webservice->raise_message('Validation unsuccessful. Wrong hash value.');
		}
			
	}
}