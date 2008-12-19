<?php

abstract class Webservice
{
	public static function factory($webservice_handler, $protocol = 'Soap', $implementation = 'Nusoap')
	{
		$file_protocol = DokeosUtilities :: camelcase_to_underscores($protocol);
		$file_implementation = DokeosUtilities :: camelcase_to_underscores($implementation); 
		
		require_once dirname(__FILE__) . '/' . $file_protocol . '/' . $file_implementation . '/' . $file_protocol . '_' . $file_implementation . '_webservice.class.php';
		$class = $protocol . $implementation . 'Webservice';
		return new $class($webservice_handler);
	}
	
	abstract function provide_webservice($functions);
	
	/**
	 * Call a webservice
	 * @param $wsdl - the location of the webservice
	 * @param $functions - array of functionnames, parameters and handler function
	 * ex :: array(0 => (array('name' => functionname, 'parameters' => array of parameters, 'handler' => handler function)))
	 */
	abstract function call_webservice($wsdl, $functions);
}	
	
?>