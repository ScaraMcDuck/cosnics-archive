<?php
/**
 * @package application.searchportal
 * @subpackage webservice
 */
require_once dirname(__FILE__).'/soap_content_object.class.php';
require_once Path :: get_library_path(). 'database/array_result_set.class.php';

class ContentObjectSoapSearchResultSet extends ArrayResultSet {
	function next_result()
	{
		$object = parent :: next_result();
		if ($object)
		{
			return SoapContentObject :: from_standard_object($object);
		}
		return null;
	}
}
?>