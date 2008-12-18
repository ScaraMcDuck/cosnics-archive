<?php
require_once Path :: get_plugin_path() . 'nusoap/nusoap.php';

class NusoapWebservice
{
	private $webservice_handler;
	
	function NusoapWebservice($webservice_handler)
	{
		$this->webservice_handler = $webservice_handler;
	}
	
	function provide_webservice($functions)
	{
		$server = new soap_server();
		$server->configureWSDL('Dokeos', 'http://www.dokeos.com');
		
		foreach($functions as $name => $objects)
		{
			$in = $objects['input'];
			$input = array();
			
			foreach($in->get_default_property_names() as $property)
			{
				$input[$property] = 'xsd:string';
			}
			
			$out = $objects['output'];			
			$properties = array();
				
			foreach($out->get_default_property_names() as $property)
			{
				$properties[$property] = array('name' => $property, 'type' => 'xsd:string');
			}
			
			$server->wsdl->addComplexType(
			    get_class($out),
			    'complexType',
			    'struct',
			    'all',
			    '',
			    $properties
			);
			
			$server->register('NusoapWebservice.' . $name, $input, array('return' => 'tns:' . get_class($out)),
			       'http://www.dokeos.com', 'http://www.dokeos.com#' . $name, 'rpc', 'encoded', '');
			
			if (!isset($HTTP_RAW_POST_DATA)) $HTTP_RAW_POST_DATA = implode("\r\n", file('php://input'));
			$server->service($HTTP_RAW_POST_DATA);
			
		}
	}
	
	function handle_webservice($input_array)
	{
		//dump($input_array);
	}
	
	function get_user($input_array)
	{
		return array('name' => 'Sven', 'email' => 'a');
		//dump($input_array);
	}
	
	function call_webservice($wsdl, $functions)
	{
		$client = new nusoap_client($wsdl, 'wsdl');
		
		foreach($functions as $function)
		{
			$function_name = $function['name'];
			$function_parameters = $function['parameters'];
			$handler_function = $function['handler'];
			$result = $client->call($function_name, $function_parameters);
			$this->webservice_handler->{$handler_function}($result);
			
			$this->debug($result, $client);
		}
	}
	
	function debug($result, $client)
	{	
		echo '<h2>Request</h2><pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';
		echo '<h2>Response</h2><pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';
		echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';
	}
}

?>