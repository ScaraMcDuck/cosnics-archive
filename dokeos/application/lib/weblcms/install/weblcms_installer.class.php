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
		$sqlfiles = array();
		$dir = dirname(__FILE__);
		$handle = opendir($dir);		
		while (false !== ($type = readdir($handle)))
		{
			$path = $dir.'/'.$type;
			if (file_exists($path) && (substr($path, -3) == 'xml'))
			{
				$this->parse_xml_file($path);
			}
			elseif (file_exists($path) && (substr($path, -3) == 'sql'))
			{
				$sqlfiles[] = $type;
			}
		}
		for ($i = 0; $i < count($sqlfiles); $i++)
		{
			$this->parse_sql_file($dir , $sqlfiles[$i]);
		}
		closedir($handle);
	}
	
	/**
	 * Parses an sql file and sends the request to the database manager
	 * @param String $directory
	 * @param String $filename
	 */
	function parse_sql_file($directory, $sqlfilename)
	{
		$dm = WeblcmsDataManager :: get_instance();
		$path = $directory.'/'.$sqlfilename;
		$filecontent = fread(fopen($path, 'r'), filesize($path));
		$sqlstring = explode("\n", $filecontent);
		echo '<pre>Executing additional WebLCMS SQL statement(s)</pre>';flush();
		foreach($sqlstring as $sqlstatement)
		{
			$dm->ExecuteQuery($sqlstatement);
		}
		
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
			 $property_info['default'] = $property->getAttribute('default');
			 $property_info['autoincrement'] = $property->getAttribute('autoincrement');
			 $property_info['fixed'] = $property->getAttribute('fixed');
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