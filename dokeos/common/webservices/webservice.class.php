<?php
require_once(dirname(__FILE__) . '/../global.inc.php');

abstract class Webservice
{
	public static function factory($webservice_handler)
	{
		//$type = Configuration :: get_instance()->get_parameter('general', 'webservice');
		$type = 'Nusoap';
		require_once dirname(__FILE__) . '/' . strtolower($type) . '/' . strtolower($type) . 
					 '_webservice.class.php';
		$class = $type.'Webservice';
		return new $class($webservice_handler);
	}
	
	abstract function provide_webservice();
	
	/**
	 * Call a webservice
	 * @param $wsdl - the location of the webservice
	 * @param $functions - array of functionnames, parameters and handler function
	 * ex :: array(0 => (array('name' => functionname, 'parameters' => array of parameters, 'handler' => handler function)))
	 */
	abstract function call_webservice($wsdl, $functions);
}	
	
?>