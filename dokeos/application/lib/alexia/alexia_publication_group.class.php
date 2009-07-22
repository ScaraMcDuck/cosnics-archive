<?php 
/**
 * portfolio
 */

/**
 * This class describes a AlexiaPublicationGroup data object
 *
 * @author Sven Vanpoucke
 */
class AlexiaPublicationGroup
{
	const CLASS_NAME = __CLASS__;
	const TABLE_NAME = 'publication_group';

	/**
	 * AlexiaPublicationGroup properties
	 */
	const PROPERTY_PUBLICATION = 'publication';
	const PROPERTY_GROUP_ID = 'group_id';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new AlexiaPublicationGroup object
	 * @param array $defaultProperties The default properties
	 */
	function AlexiaPublicationGroup($defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}

	/**
	 * Gets a default property by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}

	/**
	 * Gets the default properties
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_PUBLICATION, self :: PROPERTY_GROUP_ID);
	}

	/**
	 * Sets a default property by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}

	/**
	 * Sets the default properties of this class
	 */
	function set_default_properties($defaultProperties)
	{
		$this->defaultProperties = $defaultProperties;
	}

	/**
	 * Returns the publication of this AlexiaPublicationGroup.
	 * @return the publication.
	 */
	function get_publication()
	{
		return $this->get_default_property(self :: PROPERTY_PUBLICATION);
	}

	/**
	 * Sets the publication of this AlexiaPublicationGroup.
	 * @param publication
	 */
	function set_publication($publication)
	{
		$this->set_default_property(self :: PROPERTY_PUBLICATION, $publication);
	}
	/**
	 * Returns the group_id of this AlexiaPublicationGroup.
	 * @return the group_id.
	 */
	function get_group_id()
	{
		return $this->get_default_property(self :: PROPERTY_GROUP_ID);
	}

	/**
	 * Sets the group_id of this AlexiaPublicationGroup.
	 * @param group_id
	 */
	function set_group_id($group_id)
	{
		$this->set_default_property(self :: PROPERTY_GROUP_ID, $group_id);
	}

	function delete()
	{
		$dm = AlexiaDataManager :: get_instance();
		return $dm->delete_alexia_publication_group($this);
	}

	function create()
	{
		$dm = AlexiaDataManager :: get_instance();
       	return $dm->create_alexia_publication_group($this);
	}

	function update()
	{
		$dm = AlexiaDataManager :: get_instance();
		return $dm->update_alexia_publication_group($this);
	}

	static function get_table_name()
	{
		return self :: TABLE_NAME;
	}
}
?>