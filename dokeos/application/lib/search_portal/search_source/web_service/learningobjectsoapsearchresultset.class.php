<?php
/**
 * @package application.searchportal
 * @subpackage webservice
 */
require_once dirname(__FILE__).'/soaplearningobject.class.php';
require_once Path :: get_repository_path(). 'lib/arrayresultset.class.php';

class LearningObjectSoapSearchResultSet extends ArrayResultSet {
	function next_result()
	{
		$object = parent :: next_result();
		if ($object)
		{
			return SoapLearningObject :: from_standard_object($object);
		}
		return null;
	}
}
?>