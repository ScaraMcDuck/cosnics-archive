<?php
require_once(dirname(__FILE__) . '/../../global.inc.php');
require_once dirname(__FILE__) . '/../webservice.class.php';
ini_set("memory_limit"		,"3500M"	);	// Geen php-beperkingen voor geheugengebruik
ini_set("max_execution_time"	,"72000");	// Twee uur moet voldoende zijn...

$handler = new TestCallWebserviceHandler();
$res = '';
$start_total = microtime(true);
$file = fopen(dirname(__FILE__) . 'test.txt', 'w');
for($i=0;$i<1000;$i++)
{
	$start = microtime(true);
	$handler->run();
	$stop = microtime(true);
	
	$time = $stop - $start;
	
	fwrite($file, date('[H:m]') . 'Called webservice (' . $time .' s) :' . "\n" . var_export($res, true) . "\n");
}
$stop_total = microtime(true);
$time = $stop_total - $start_total;
fwrite($file, 'Total: ' . $time . ' s');
fclose($file);

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
		global $res;
		$res = $result;
	}
}

?>