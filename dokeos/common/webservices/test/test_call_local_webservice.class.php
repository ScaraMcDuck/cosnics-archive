<?php
require_once(dirname(__FILE__) . '/../../global.inc.php');
require_once dirname(__FILE__) . '/../webservice.class.php';

$res = '';

$handler = new TestCallLocalWebservice();
$file = fopen(dirname(__FILE__) . 'test.txt', 'w');
for($i=0;$i<10;$i++)
{
	$start = microtime(true);
	$handler->run();
	$stop = microtime(true);
	
	$time = $stop - $start;
	
	fwrite($file, date('[H:m]') . 'Called webservice (' . $time .' s) :' . "\n" . var_export($res, true) . "\n");
}
fclose($file);

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
			'name' => 'TestProvideWebserviceHandler.get_user',
			'parameters' => array('id' => 1),
			'handler' => 'handle_webservice'
		);
		
		$this->webservice->call_webservice($wsdl, $functions);
	}
	
	function handle_webservice($result)
	{
		global $res;
		$res = $result;
	}
}

?>