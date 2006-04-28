<?php
require_once dirname(__FILE__).'/remotelearningobject.class.php';
require_once dirname(__FILE__).'/learningobjectsearchutilities.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/repositorydatamanager.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/repositoryutilities.class.php';

ini_set('soap.wsdl_cache_enabled', '0');

class LearningObjectSearchServer
{
	private $server;

	private $soap_fault;

	function LearningObjectSearchServer($encoding = 'iso-8859-1')
	{
		$wsdl_file = LearningObjectSearchUtilities :: get_wsdl_file_path(api_get_path(WEB_PATH));
		try
		{
			$this->server = new SoapServer($wsdl_file, array ('encoding' => $encoding));
		}
		catch (SoapFault $ex)
		{
			$this->server = null;
			$this->soap_fault = $ex;
		}
		$this->server->setClass(get_class());
	}

	function is_initialized()
	{
		return !is_null($this->server);
	}

	function get_soap_fault()
	{
		return $this->soap_fault;
	}

	function run()
	{
		$this->server->handle();
	}

	static function search($query)
	{
		$dm = RepositoryDataManager :: get_instance();
		$condition = RepositoryUtilities :: query_to_condition($query);
		$objects = $dm->retrieve_learning_objects(null, $condition, array (LearningObject :: PROPERTY_TITLE), array (SORT_ASC));
		$soap_vars = array ();
		while ($lo = $objects->next_result())
		{
			$title = $lo->get_title();
			$description = $lo->get_description();
			$url = $lo->get_view_url();
			$soap_vars[] = new RemoteLearningObject($lo->get_type(), $title, $description, $lo->get_creation_date(), $lo->get_modification_date(), $url);
		}
		return $soap_vars;
	}
}
?>