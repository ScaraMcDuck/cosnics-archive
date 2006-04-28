<?php
require_once dirname(__FILE__).'/learningobjectsearchutilities.class.php';

class LearningObjectSearchClient
{
	const KEY_RESULTS = 'Results';
	const KEY_LIMIT_REACHED = 'LimitReached';
	
	private $client;

	private $soap_fault;

	function LearningObjectSearchClient($definition_file, $encoding = 'iso-8859-1')
	{
		try
		{
			$this->client = new SoapClient($definition_file, array ('encoding' => $encoding));
		}
		catch (SoapFault $ex)
		{
			$this->client = null;
			$this->soap_fault = $ex;
		}
	}

	function is_initialized()
	{
		return !is_null($this->client);
	}

	function get_soap_fault()
	{
		return $this->soap_fault;
	}

	function search($query)
	{
		try
		{
			return $this->client->search($query);
		}
		catch (SoapFault $ex)
		{
			return $ex;
		}
	}
	
	static function is_supported()
	{
		return LearningObjectSearchUtilities :: soap_enabled();
	}
}
?>