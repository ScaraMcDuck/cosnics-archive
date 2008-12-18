<?php
require_once(dirname(__FILE__) . '/../../global.inc.php');
require_once dirname(__FILE__) . '/../webservice.class.php';
require_once dirname(__FILE__) . '/provider/input_user.class.php';
require_once dirname(__FILE__) . '/provider/output_user.class.php';

$handler = new TestProvideWebserviceHandler();
$handler->run();

class TestProvideWebserviceHandler
{
	private $webservice;
	private $functions;
	
	function TestProvideWebserviceHandler()
	{
		$this->webservice = Webservice :: factory($this);
	}
	
	function run()
	{	
		$functions = array();
		
		$functions['get_user'] = array(
			'input' => new InputUser(),
			'output' => new OutputUser()
		);
		
		$this->webservice->provide_webservice($functions);
	}
	
	function get_user($input_user)
	{
		$user = new OutputUser();
		$user->set_name('Developer');
		$user->set_email('developer@dokeos.com');
		
		return $user->to_array();
	}
}