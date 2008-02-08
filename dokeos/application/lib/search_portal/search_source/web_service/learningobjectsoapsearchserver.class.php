<?php
/**
 * @package application.searchportal
 * @subpackage webservice
 */
require_once dirname(__FILE__).'/soaplearningobject.class.php';
require_once dirname(__FILE__).'/learningobjectsoapsearchutilities.class.php';
require_once dirname(__FILE__).'/../../../../../repository/lib/repositorydatamanager.class.php';
require_once dirname(__FILE__).'/../../../../../repository/lib/repositoryutilities.class.php';

class LearningObjectSoapSearchServer
{
	const MAX_RESULTS = 100;
	
	private $server;

	function LearningObjectSoapSearchServer($encoding = 'iso-8859-1')
	{
		$wsdl_file = LearningObjectSoapSearchUtilities :: get_wsdl_file_path(api_get_path(WEB_PATH));
		try
		{
			$this->server = new SoapServer($wsdl_file, array ('encoding' => $encoding));
		}
		catch (SoapFault $ex)
		{
			throw LearningObjectSoapSearchUtilities :: soap_fault_to_exception($ex);
		}
		$this->server->setClass(get_class());
	}

	function is_initialized()
	{
		return !is_null($this->server);
	}

	function run()
	{
		$this->server->handle();
	}

	static function search($query)
	{
		$dm = RepositoryDataManager :: get_instance();
		$adm = AdminDataManager :: get_instance();
		$condition = RepositoryUtilities :: query_to_condition($query);
		$objects = $dm->retrieve_learning_objects(null, $condition, array (LearningObject :: PROPERTY_TITLE), array (SORT_ASC), 0, self :: MAX_RESULTS);
		$object_count = $dm->count_learning_objects(null, $condition);
		$soap_objects = array ();
		while ($lo = $objects->next_result())
		{
			$title = $lo->get_title();
			$description = $lo->get_description();
			$url = $lo->get_view_url();
			$soap_objects[] = new SoapLearningObject($lo->get_type(), $title, $description, $lo->get_creation_date(), $lo->get_modification_date(), $url);
		}
		
		$site_name_setting = $adm->retrieve_setting_from_variable_name('site_name');
		return array($site_name_setting->get_value(), api_get_path(WEB_PATH), $soap_objects, $object_count);
	}
}
?>