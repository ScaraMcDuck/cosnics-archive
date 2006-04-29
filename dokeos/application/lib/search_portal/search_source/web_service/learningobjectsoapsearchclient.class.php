<?php
class LearningObjectSoapSearchClient
{
	const KEY_REPOSITORY_TITLE = 'RepositoryTitle';
	const KEY_REPOSITORY_URL = 'RepositoryURL';
	const KEY_RETURNED_RESULTS = 'Results';
	const KEY_RESULT_COUNT = 'ActualResultCount';
	
	private $client;

	function LearningObjectSoapSearchClient($definition_file, $encoding = 'iso-8859-1')
	{
		try
		{
			$this->client = new SoapClient($definition_file, array ('encoding' => $encoding));
		}
		catch (SoapFault $ex)
		{
			throw LearningObjectSoapSearchUtilities :: soap_fault_to_exception($ex);
		}
	}

	function is_initialized()
	{
		return !is_null($this->client);
	}

	function search($query)
	{
		try
		{
			return $this->client->search($query);
		}
		catch (SoapFault $ex)
		{
			throw LearningObjectSoapSearchUtilities :: soap_fault_to_exception($ex);
		}
	}
}
?>