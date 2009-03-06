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
			'output' => new WebserviceCredential()
		);
		
		$functions['complete_login'] = array(
			'input' => new WebserviceCredential(),
			'output' => new ActionSuccess()
		);

		$this->webservice->provide_webservice($functions);
	}
	
	
	
	function login($user)
	{
		/*$c = new WebserviceCredential(array('hash' => $this->wsm->validate_login($user)));
		return $c->get_default_properties();*/
		return $this->wsm->validate_login($user);
	}
	
	function complete_login($hash)
	{
		return array('success' => $this->wsm->check_hash($hash));
	}
	
}