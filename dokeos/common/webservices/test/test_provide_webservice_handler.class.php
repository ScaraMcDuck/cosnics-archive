<?php
require_once(dirname(__FILE__) . '/../../global.inc.php');
require_once dirname(__FILE__) . '/../webservice.class.php';

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
			$input_object = new InputUser(),
			$output_object = new OutputUser()
		);
		
		$this->webservice->provide_webservice($functions);
	}
	
	function get_user($input_user)
	{
		
	}
	
	function get_input_object($function_name)
	{
		
	}
	
	function get_output_object($function_name)
	{
		
	}
}