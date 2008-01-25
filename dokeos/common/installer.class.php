<?php
/**
 * $Id$
 * @package repository
 * @todo Some more common install-functions can be added here. Example: A
 * function which returns the list of xml-files from a given directory.
 */
class Installer
{
	private $message;
	/**
	 * Constructor
	 */
    function Installer()
    {
    	$this->message = array();
    }
    /**
     * Parses an XML file describing a storage unit.
     * For defining the 'type' of the field, the same definition is used as the
     * PEAR::MDB2 package. See http://pear.php.net/manual/en/package.database.
     * mdb2.datatypes.php
     * @param string $file The complete path to the XML-file from which the
     * storage unit definition should be read.
     * @return array An with values for the keys 'name','properties' and
     * 'indexes'
     */
    public static function parse_xml_file($file)
    {
		$doc = new DOMDocument();
		$doc->load($file);
		$object = $doc->getElementsByTagname('object')->item(0);
		$name = $object->getAttribute('name');
		$xml_properties = $doc->getElementsByTagname('property');
		$attributes = array('type','length','unsigned','notnull','default','autoincrement','fixed');
		foreach($xml_properties as $index => $property)
		{
			 $property_info = array();
			 foreach($attributes as $index => $attribute)
			 {
			 	if($property->hasAttribute($attribute))
			 	{
			 		$property_info[$attribute] = $property->getAttribute($attribute);
			 	}
			 }
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
		$result = array();
		$result['name'] = $name;
		$result['properties'] = $properties;
		$result['indexes'] = $indexes;
		return $result;
    }
    
    function add_message($message)
    {
    	$this->message[] = $message;
    }
    
    function set_message($message)
    {
    	$this->message = $message;
    }
    
    function get_message()
    {
    	return $this->message;
    }
    
    function retrieve_message()
    {
    	return implode('<br />'."\n", $this->get_message());
    }
}
?>