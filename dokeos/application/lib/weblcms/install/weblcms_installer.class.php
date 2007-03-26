<?php
/**
 * @package application.weblcms
 */
require_once dirname(__FILE__).'/../weblcmsdatamanager.class.php';
/**
 *	This installer can be used to create the storage structure for the
 * weblcms application.
 */
class WeblcmsInstaller {
	/**
	 * Constructor
	 */
    function WeblcmsInstaller() {
    }
	/**
	 * Runs the install-script.
	 * @todo This function now uses the function of the RepositoryInstaller
	 * class. These shared functions should be available in a common base class.
	 */
	function install()
	{
		//$repository_installer = new RepositoryInstaller();
		$this->parse_xml_file(dirname(__FILE__).'/learning_object_publication.xml');
		$this->parse_xml_file(dirname(__FILE__).'/learning_object_publication_category.xml');
		$this->parse_xml_file(dirname(__FILE__).'/learning_object_publication_group.xml');
		$this->parse_xml_file(dirname(__FILE__).'/learning_object_publication_user.xml');
		$this->parse_xml_file(dirname(__FILE__).'/course_module.xml');
		$this->parse_xml_file(dirname(__FILE__).'/course_module_last_access.xml');

	}
	
	function parse_xml_file($path)
	{
		$doc = new DOMDocument();
		$doc->load($path);
		$object = $doc->getElementsByTagname('object')->item(0);
		$name = $object->getAttribute('name');
		$xml_properties = $doc->getElementsByTagname('property');
		foreach($xml_properties as $index => $property)
		{
			 $property_info = array();
			 $property_info['type'] = $property->getAttribute('type');
			 $property_info['length'] = $property->getAttribute('length');
			 $property_info['unsigned'] = $property->getAttribute('unsigned');
			 $property_info['notnull'] = $property->getAttribute('notnull');
			 $properties[$property->getAttribute('name')] = $property_info;
		}
		$xml_indexes = $doc->getElementsByTagname('index');
		foreach($xml_indexes as $key => $index)
		{
			 $index_info = array();
			 $index_info['type'] = $index->getAttribute('type');
			 $index_properties = $index->getElementsByTagname('indexproperty');
			 foreach($index_properties as $subkey => $index_property)
			 {
			 	$index_info['fields'][$index_property->getAttribute('name')] = array();
			 }
			 $indexes[$index->getAttribute('name')] = $index_info;
		}
		$dm = WeblcmsDataManager :: get_instance();
		echo '<pre>Creating WebLCMS Storage Unit: '.$name.'</pre>';flush();
		$dm->create_storage_unit($name,$properties,$indexes);
	}
}
?>