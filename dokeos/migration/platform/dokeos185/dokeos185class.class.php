<?php

/**
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__).'/../../lib/import/importclass.class.php';
require_once dirname(__FILE__).'/../../../classgroup/lib/classgroup.class.php';

/**
 * This class represents an old Dokeos 1.8.5 class
 *
 * @author David Van Wayenberghµ
 * @author Sven Vanpoucke
 */

class Dokeos185Class extends Import
{
	/**
	 * Migration data manager
	 */
	private static $mgdm;

	/**
	 * course relation user properties
	 */
	 
	const PROPERTY_ID = 'id';
	const PROPERTY_CODE = 'code';
	const PROPERTY_NAME = 'name';
	
	
	/**
	 * Alfanumeric identifier of the class object.
	 */
	private $code;
	
	/**
	 * Default properties of the class object, stored in an associative
	 * array.
	 */
	private $defaultProperties;
	
	/**
	 * Creates a new class object.
	 * @param array $defaultProperties The default properties of the class
	 *                                 object. Associative array.
	 */
	function Dokeos185Class($defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Gets a default property of this class object by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}
	
	/**
	 * Gets the default properties of this class.
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}
	
	/**
	 * Get the default properties of all classes.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self::PROPERTY_ID, self :: PROPERTY_CODE, 
		self::PROPERTY_NAME);
	}
	
	/**
	 * Sets a default property of this class by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}
	
	/**
	 * Checks if the given identifier is the name of a default class
	 * property.
	 * @param string $name The identifier.
	 * @return boolean True if the identifier is a property name, false
	 *                 otherwise.
	 */
	static function is_default_property_name($name)
	{
		return in_array($name, self :: get_default_property_names());
	}
	
	 
	/**
	 * Returns the id of this class.
	 * @return int The id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}
	 
	/**
	 * Returns the code of this class.
	 * @return String The code.
	 */
	function get_code()
	{
		return $this->get_default_property(self :: PROPERTY_CODE);
	}
	
	/**
	 * Returns the name of this class.
	 * @return int The name.
	 */
	function get_name()
	{
		return $this->get_default_property(self :: PROPERTY_Name);
	}
	
	/**
	 * Sets the id of this class.
	 * @param int $id The id.
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}
	
	/**
	 * Sets the code of this class.
	 * @param String $code The code.
	 */
	function set_code($code)
	{
		$this->set_default_property(self :: PROPERTY_CODE, $code);
	}
	
	/**
	 * Sets the name of this class
	 * @param String $name The name of the class
	 */
	function set_name($name)
	{
		$this->set_default_property(self :: PROPERTY_NAME, $name);
	}
	
	function is_valid_class()
	{
		if(!$this->get_id() || !$this->get_name())
		{
			self :: $mgdm->add_failed_element($this->get_id(),
				'dokeos_main.class');
			return false;
		}
		return true;
	}
	
	function convert_to_new_class()
	{
		//class parameters
		$lcms_class = new ClassGroup();
		
		$lcms_class->set_name($this->get_name());
		$lcms_class->set_description($this->get_name());
		$lcms_class->set_sort(self :: $mgdm->get_next_position('classgroup_classgroup', 'sort'));
		
		//create course in database
		$lcms_class->create();
		
		//Add id references to temp table
		self :: $mgdm->add_id_reference($this->get_id(), $lcms_class->get_id(), 'classgroup_classgroup');
		
		return $lcms_class;
	}
	function get_all_classes($mgdm)
	{
		self :: $mgdm = $mgdm;
		return self :: $mgdm->get_all_classes();
	}
}
?>
