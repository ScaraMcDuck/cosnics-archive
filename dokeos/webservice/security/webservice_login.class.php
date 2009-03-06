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
	private $functions;	
	
	function WebServiceLogin()
	{
		$this->webservice = Webservice :: factory($this);
		$this->wsm = WebserviceSecurityManager :: get_instance();
	}
	
	function run()
	{	
		
		$functions = array();
		
		$functions['login'] = array(
			'input' => new User(),
			'output' => new WebserviceCredential(),
			//'require_hash' => true
		);
		
		$functions['complete_login'] = array(
			'input' => new WebserviceCredential()
		);

		$this->webservice->provide_webservice($functions);
	}
	
	
	
	function login($user)
	{
		$hash =  $this->wsm->validate_login($user);

		if(!empty($hash))
		{
			return $hash;
		}
		else
		{
			return $this->webservice->raise_message('Wrong login. Please check the submitted username and password, and try again.');
		}
	}
	
	function complete_login($hash)
	{
		return $this->webservice->raise_message($this->wsm->check_hash($hash));
	}
	
}