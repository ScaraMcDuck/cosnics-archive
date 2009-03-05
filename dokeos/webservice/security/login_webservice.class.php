<?php
require_once(dirname(__FILE__) . '/../../common/global.inc.php');
require_once dirname(__FILE__) . '/../../common/webservices/webservice.class.php';
require_once dirname(__FILE__) . '/provider/input_user.class.php';
require_once dirname(__FILE__) . '/../lib/data_manager/database.class.php';
require_once dirname(__FILE__) . '/../lib/user.class.php';
require_once dirname(__FILE__) . '/../../common/webservices/action_success.class.php';

$handler = new WebServiceLogin();
$handler->run();

class WebServiceLogin
{
	private $webservice;
	private $functions;	
	
	function WebServiceLogin()
	{
		$this->webservice = Webservice :: factory($this);
	}
	
	function run()
	{	
		$functions = array();
		
		$functions['validate'] = array(
			'input' => new User(),
			'output' => new ActionSuccess()
		);

		$this->webservice->provide_webservice($functions);
	}
	
	
	
	function validate($input_user)
	{

		dump($this->validate($input_user));
	}
	
	
}