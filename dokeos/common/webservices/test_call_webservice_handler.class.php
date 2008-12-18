<?php
require_once(dirname(__FILE__) . '/../global.inc.php');
require_once dirname(__FILE__) . '/webservice.class.php';

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
		$wsdl = '';
		$off_code = '';
		$username = '';
		$password = '';
		
		$functions = array();
		$functions[] = array(
			'name' => 'getFoto',
			'parameters' => array('width' => '100%', 'username' => $username, 'password' => $password, 
								  'id' => $off_code, 'height' => '100%'),
			'handler' => 'handle_webservice'
		);
		
		$this->webservice->call_webservice($wsdl, $functions);
	}
	
	function handle_webservice($result)
	{
		if($result != ""){
		$data = base64_decode($result);
		$im = imagecreatefromstring($data);
		if ($im !== false) {
			header('Content-Type: image/jpeg');
			imagejpeg($im);
			imagedestroy($im);
		}
		}else{
			//no picture - show default unknown.jpg
		}
	}
}

?>