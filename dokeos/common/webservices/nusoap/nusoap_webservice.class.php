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
		
		foreach($functions as $name => $function)
		{
			$server->wsdl->addComplexType(
			    'User',
			    'complexType',
			    'struct',
			    'all',
			    '',
			    array(
			        'datasource' => array('name' => 'datasource', 'type' => 'xsd:string'),
			        'familyname' => array('name' => 'familyname', 'type' => 'xsd:string'),
			        'givenname' => array('name' => 'givenname', 'type' => 'xsd:string'),
			        'username' => array('name' => 'username', 'type' => 'xsd:string'),
			        'emailaddress' => array('name' => 'emailaddress', 'type' => 'xsd:string'),
			        'courseid' => array('name' => 'courseid', 'type' => 'xsd:string'),
			        'coursetitle' => array('name' => 'coursetitle', 'type' => 'xsd:string'),
			        'status' => array('name' => 'status', 'type' => 'xsd:string'),
			        'startdate' => array('name' => 'startdate', 'type' => 'xsd:string'),
			        'stopdate' => array('name' => 'stopdate', 'type' => 'xsd:string')
			    )
			);
		}
		
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