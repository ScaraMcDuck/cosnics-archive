<?php
/**
 * $Id: announcement.class.php 15410 2008-05-26 13:41:21Z Scara84 $
 * @package repository.learningobject
 * @subpackage announcement
 */
require_once dirname(__FILE__) . '/../../learning_object.class.php';
/**
 * This class represents an announcement
 */
class Rdevent extends LearningObject
{

    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_EVENT_ID = 'event_id';
    
    private $defaultProperties;

	/**
	 * Creates a new link object.
	 * @param int $id The numeric ID of the link object. May be omitted
	 *                if creating a new object.
	 * @param array $defaultProperties The default properties of the link
	 *                                 object. Associative array.
	 */
	function Rdevent($defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}

    function get_event_id ()
	{
		return $this->get_additional_property(self :: PROPERTY_EVENT_ID);
	}
	function set_event_id ($event_id)
	{
		return $this->set_additional_property(self :: PROPERTY_EVENT_ID, $event_id);
	}

    static function get_additional_property_names()
	{
		return array (self :: PROPERTY_REF_ID,self :: PROPERTY_PUB_TYPE);
	}
    
	//Inherited
	function supports_attachments()
	{
		return true;
	}

    static function get_table_name()
	{
		return DokeosUtilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}

    function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}

	/**
	 * Gets the default properties of this link.
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}

	function set_default_properties($defaultProperties)
	{
		$this->defaultProperties = $defaultProperties;
	}

	/**
	 * Get the default properties of all links.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_EVENT_ID);
	}

	/**
	 * Sets a default property of this link by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}

	/**
	 * Checks if the given identifier is the name of a default link
	 * property.
	 * @param string $name The identifier.
	 * @return boolean True if the identifier is a property name, false
	 *                 otherwise.
	 */
	static function is_default_property_name($name)
	{
		return in_array($name, self :: get_default_property_names());
	}
}
?>