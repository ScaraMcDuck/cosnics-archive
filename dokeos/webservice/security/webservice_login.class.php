<?php
require_once(dirname(__FILE__) . '/../../common/global.inc.php');
require_once dirname(__FILE__) . '/../../common/webservices/webservice.class.php';
require_once dirname(__FILE__) . '/../../user/lib/user.class.php';
require_once dirname(__FILE__) . '/../../common/webservices/action_success.class.php';
require_once dirname(__FILE__) . '/webservice_security_manager.class.php';
require_once dirname(__FILE__) . '/../lib/webservice_credential.class.php';

$handler = new WebServiceLogin();
$handler->run();

class WebServiceLogin
{
	private $webservice;	
	
	function WebServiceLogin()
	{
		$this->webservice = Webservice :: factory($this);		
	}
	
	function run()
	{	
		
		$functions = array();
		
		$functions['login'] = array(
			'input' => new User(),
			'output' => new WebserviceCredential(),
			'require_hash' => true
		);
		
		$this->webservice->provide_webservice($functions);
	}
	
	
	
	function login($user)
	{        
        $hash =  $this->webservice->validate_login($user,$input_hash);
		if(!empty($hash) && gettype($hash)=='array')
		{
			return $hash;
		}
		else
		{
			return $this->webservice->raise_message($hash);
		}
	}
}