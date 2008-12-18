<?php
require_once Path :: get_plugin_path() . 'nusoap/nusoap.php';

class NusoapWebservice
{
	private $webservice_handler;
	
	function NusoapWebservice($webservice_handler)
	{
		$this->webservice_handler = $webservice_handler;
	}
	
	function provide_webservice()
	{
		
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
			//call_user_func(array($this->webservice_handler, $handler_function), $result);
			
			//$this->debug($result, $client);
		}
	}
	
	function debug($result, $client)
	{
		dump($result);
		
		echo '<h2>Request</h2><pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';
		echo '<h2>Response</h2><pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';
		echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';
	}
}
?>