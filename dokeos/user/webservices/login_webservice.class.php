<?php
require_once(dirname(__FILE__) . '/../../common/global.inc.php');
require_once dirname(__FILE__) . '/../../common/webservices/webservice.class.php';
require_once dirname(__FILE__) . '/../../user/lib/user.class.php';
require_once dirname(__FILE__) . '/../../common/webservices/action_success.class.php';
require_once Path :: get_webservice_path() . '/lib/webservice_credential.class.php';

$handler = new LoginWebservice();
$handler->run();

class LoginWebservice
{
	private $webservice;		
	
	function LoginWebservice()
	{
		$this->webservice = Webservice :: factory($this);		
	}
	
	function run()
	{	
		
		$functions = array();
		
		$functions['login'] = array(
			'input' => new User(),
			'output' => new WebserviceCredential(),
			'require_hash' => false
		);
		
		$this->webservice->provide_webservice($functions); 
	}	
	
	function login($user)
	{
        if(is_array($user))
		{			
            $hash =  $this->webservice->validate_login($user[username],$user[password]);//no password will be sent, only the username//hash
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