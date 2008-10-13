<?php

//require_once dirname(__FILE__).'/reservations_data_manager.class.php';

/**
 * @package category
 */
/**
 *	@author Sven Vanpoucke
 */

abstract class PlatformCategory
{
	const PROPERTY_ID = 'id';
	const PROPERTY_NAME = 'name';
	const PROPERTY_PARENT = 'parent';
	const PROPERTY_DISPLAY_ORDER = 'display_order';
	
	/**
	 * Default properties of the contribution object, stored in an associative
	 * array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new contribution object.
	 * @param int $id The numeric ID of the contribution object. May be omitted
	 *                if creating a new object.
	 * @param array $defaultProperties The default properties of the contribution
	 *                                 object. Associative array.
	 */
	 
	function PlatformCategory($defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Gets a default property of this contribution object by title.
	 * @param string $title The title of the property.
	 */
	function get_default_property($title)
	{
		return $this->defaultProperties[$title];
	}
	
	/**
	 * Gets the default properties of this contribution.
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
	 * Get the default properties of all contributions.
	 * @return array The property titles.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_NAME, self :: PROPERTY_PARENT, self :: PROPERTY_DISPLAY_ORDER);
	}
		
	/**
	 * Sets a default property of this contribution by title.
	 * @param string $title The title of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($title, $value)
	{
		$this->defaultProperties[$title] = $value;
	}
	
	/**
	 * Checks if the given identifier is the title of a default contribution
	 * property.
	 * @param string $title The identifier.
	 * @return boolean True if the identifier is a property title, false
	 *                 otherwise.
	 */
	static function is_default_property_name($title)
	{
		return in_array($title, self :: get_default_property_names());
	}

	/**
	 * Returns the id of this contribution.
	 * @return int The id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}
	
	/**
	 * Sets the name of this contribution.
	 * @param int $name The name.
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}	
	
	function get_name()
	{
		return $this->get_default_property(self :: PROPERTY_NAME);
	}

	function set_name($name)
	{
		$this->set_default_property(self :: PROPERTY_NAME, $name);
	}	
	
	function get_parent()
	{
		return $this->get_default_property(self :: PROPERTY_PARENT);
	}

	function set_parent($parent)
	{
		$this->set_default_property(self :: PROPERTY_PARENT, $parent);
	}
	
	function get_display_order()
	{
		return $this->get_default_property(self :: PROPERTY_DISPLAY_ORDER);
	}

	function set_display_order($display_order)
	{
		$this->set_default_property(self :: PROPERTY_DISPLAY_ORDER, $display_order);
	}
	
	abstract function create();
	abstract function update();
	abstract function delete();
	/*function create()
	{
		$rdm = ReservationsDataManager :: get_instance();
		$this->set_id($rdm->get_next_category_id());
		$this->set_display_order($rdm->select_next_display_order($this->get_parent()));
		return $rdm->create_category($this);
	}
	
	function update()
	{
		return ReservationsDataManager :: get_instance()->update_category($this);
	}
	
	function delete()
	{
		return ReservationsDataManager :: get_instance()->delete_category($this);
	}*/
}