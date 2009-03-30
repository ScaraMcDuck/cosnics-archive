<?php
require_once Path :: get_library_path() . 'webservices/webservice.class.php';
require_once Path :: get_plugin_path() . 'nusoap/nusoap.php';
require_once Path :: get_webservice_path() .'lib/webservice_data_manager.class.php';

class SoapNusoapWebservice extends Webservice
{
	private $webservice_handler;    
	
	function SoapNusoapWebservice($webservice_handler)
	{
		$this->webservice_handler = $webservice_handler;
        parent :: Webservice();
	}
	
	function provide_webservice($functions)
	{
		$server = new soap_server();
		$server->configureWSDL('Dokeos', 'http://www.dokeos.com');
        
		foreach($functions as $name => $objects) //doorloopt alle functies
		{
            //input field
            if(isset($objects['input'])) 
			{
                if($objects['array_input'])
				{
					$in = $objects['input'][0];
				}
				else
				{
					$in = $objects['input'];
				}
                
                $input = array();

                foreach($in->get_default_property_names() as $property)
                {
                    $input[$property] = array('name' => $property, 'type' => 'xsd:string');
                }

                $server->wsdl->addComplexType(
				    get_class($in),
				    'complexType',
				    'struct',
				    'all',
				    '',
				    $input
				);


				if($objects['array_input'])
				{
					$server->wsdl->addComplexType(
					  get_class($in).'s',
					  'complexType',
					  'array',
					  '',
					  'SOAP-ENC:Array',
					  array(),
					  array(
					    array('ref' => 'SOAP-ENC:arrayType',
					         'wsdl:arrayType' => 'tns:'.get_class($in).'[]')
					  ),
					  'tns:'.get_class($in)
					);
				}
			}

            //output
			if(isset($objects['output']))
			{
				if($objects['array'])
				{
					$out = $objects['output'][0];                    
				}
				else
				{
					$out = $objects['output'];
				}
                
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
				
				
				if($objects['array'])
				{
					$server->wsdl->addComplexType(
					  get_class($out).'s',
					  'complexType', 
					  'array', 
					  '', 
					  'SOAP-ENC:Array', 
					  array(),
					  array(
					    array('ref' => 'SOAP-ENC:arrayType', 
					         'wsdl:arrayType' => 'tns:'.get_class($out).'[]')
					  ),
					  'tns:'.get_class($out)
					);
				}
			}
			// method name, input parameters, output parameters
			$server->register(get_class($this->webservice_handler) . '.' . $name, array('input' =>'tns:' . get_class($in).($objects['array_input']?'s':''),'hash' => 'xsd:string'), array('return' => 'tns:' . get_class($out).($objects['array']?'s':'')),
			       'http://www.dokeos.com', 'http://www.dokeos.com#' . $name, 'rpc', 'encoded', '', '', 'NusoapWebservice.handle_webservice');
			
		}
		
		if (!isset($HTTP_RAW_POST_DATA)) $HTTP_RAW_POST_DATA = implode("\r\n", file('php://input'));
			$server->service($HTTP_RAW_POST_DATA);
	}
	
	
	function provide_webservice_with_wsdl($wsdl)
	{
		$server = new soap_server($wsdl);
		if (!isset($HTTP_RAW_POST_DATA)) $HTTP_RAW_POST_DATA = implode("\r\n", file('php://input'));
			$server->service($HTTP_RAW_POST_DATA);
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
			
			$this->debug($client);
		}
		
	}	
	
	function raise_message($message)
	{
		return new soapval('return', 'xsd:'.gettype($message), $message);
	}
    
    function raise_error($faultstring = 'unknown error', $faultcode = 'Client', $faultactor = NULL, $detail = NULL, $mode = null, $options = null)
	{
		return new soap_fault($faultstring, $faultcode, $faultactor, $detail, $mode, $options);
	}
	
	function debug($client)
	{	
		echo '<h2>Request</h2><pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';
		echo '<h2>Response</h2><pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';
		echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';			
	}
    
}
?>