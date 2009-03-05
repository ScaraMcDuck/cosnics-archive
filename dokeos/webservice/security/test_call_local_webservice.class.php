<?php
require_once(dirname(__FILE__) . '/../../common/global.inc.php');
require_once dirname(__FILE__) . '/../../common/webservices/webservice.class.php';

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
		$wsdl = 'http://localhost/webservice/security/webservice_login.class.php?wsdl';
		$functions = array();
		$user = new User()
		$functions[] = array(
				'name' => 'WebServiceLogin.validate',
				'parameters' => array('username' => 'Soliber',
									  'password' => '4a0091108fb271e05f34da7cf77c975f'),
				'handler' => 'handle_webservice'
		);
		$this->webservice->call_webservice($wsdl, $functions);
	}
	
	function handle_webservice($result)
	{
		/*global $file;
		fwrite($file, date('[H:i]') . 'Called webservice :' . "\n" . var_export($result, true) . "\n");*/
		dump($result);
	}
}

?>