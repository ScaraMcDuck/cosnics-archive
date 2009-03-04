<?php
require_once(dirname(__FILE__) . '/../../common/global.inc.php');
require_once dirname(__FILE__) . '/../../common/webservices/webservice.class.php';
require_once dirname(__FILE__) . '/../lib/user.class.php';

$handler = new TestCallLocalWebservice();
$handler->run();

class TestCallLocalWebservice
{
	private $webservice;
	
	function TestCallLocalWebservice()
	{
		$this->webservice = Webservice :: factory($this);
	}
	
	function run()
	{	
		$wsdl = 'http://localhost/user/webservices/webservices_user.class.php?wsdl';
		$functions = array();
		
		{
			$functions[] = array(
				'name' => 'WebServicesUser.validate',
				'parameters' => array('username' => 'admin', 'password' => '4a0091108fb271e05f34da7cf77c975f'),
				'handler' => 'handle_webservice'
			);
		}		
	
		$this->webservice->call_webservice($wsdl, $functions);
	}
	
	function handle_webservice($result)
	{		
		dump($result);
	}
}

?>