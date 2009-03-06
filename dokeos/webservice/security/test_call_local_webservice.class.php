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
		$user = new User();
		$user->set_username('Soliber');
		$user->set_password('4a0091108fb271e05f34da7cf77c975f');
		$functions[] = array(
				'name' => 'WebServiceLogin.login',
				'parameters' =>$user->get_default_properties(),
				'handler' => 'handle_webservice'
		);
		
		
		/*$wsdl = 'http://localhost/webservice/security/webservice_login.class.php?wsdl';
		$functions = array();
		$c = new WebserviceCredential();
		$c->set_hash('26001d6aea2b344c34d289bfa79e1be86c04559dbae1e374755841882fb7667ac2841e19f2bfb3ca67c6fc533f095ad7a40533153cd1f14aa7eb00ba01f0aa0b');
		$functions[] = array(
				'name' => 'WebServiceLogin.complete_login',
				'parameters' =>$c->get_default_properties(),
				'handler' => 'handle_webservice'
		);*/
		
		
		
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