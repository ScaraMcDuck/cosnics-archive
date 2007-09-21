<?php
/**
 * $Id$
 * @package repository
 */
class Installer {
	/**
	 * Constructor
	 */
    function Installer() {
    }
    /**
     * Parses an XML file describing a storage unit.
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
		$result = array();
		$result['name'] = $name;
		$result['properties'] = $properties;
		$result['indexes'] = $indexes;
		return $result;
    }
}
?>