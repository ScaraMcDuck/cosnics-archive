<?php
require_once(dirname(__FILE__) . '/../../global.inc.php');
require_once dirname(__FILE__) . '/../webservice.class.php';

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
		$wsdl = 'http://localhost/lcms/common/webservices/test/test_provide_webservice_handler.class.php?wsdl';
		$functions = array();
		$functions[] = array(
			'name' => 'NusoapWebservice.get_user',
			'parameters' => array('id' => 1),
			'handler' => 'handle_webservice'
		);
		
		$this->webservice->call_webservice($wsdl, $functions);
	}
	
	function handle_webservice($result)
	{
		dump($result);
	}
}

?>