<?php
require_once(dirname(__FILE__) . '/../../global.inc.php');
require_once dirname(__FILE__) . '/../webservice.class.php';

$handler = new TestCallWebserviceHandler();
$handler->run();

class TestCallWebserviceHandler
{
	private $webservice;
	
	function TestCallWebserviceHandler()
	{
		$this->webservice = Webservice :: factory($this);
	}
	
	function run()
	{	
		$wsdl = 'http://www.nanonull.com/TimeService/TimeService.asmx?wsdl';
		$functions = array();
		$functions[] = array(
			'name' => 'getServerTime',
			'parameters' => array(),
			'handler' => 'handle_webservice'
		);
		
		$this->webservice->call_webservice($wsdl, $functions);
	}
	
	function handle_webservice($result)
	{
		echo $result['getServerTimeResult'];
	}
}

?>