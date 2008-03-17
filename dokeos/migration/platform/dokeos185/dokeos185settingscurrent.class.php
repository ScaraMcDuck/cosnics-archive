<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a dokeos185 settings_current
 *
 * @author Sven Vanpoucke
 */
class Dokeos185SettingsCurrent
{
	/**
	 * Dokeos185SettingsCurrent properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_VARIABLE = 'variable';
	const PROPERTY_SUBKEY = 'subkey';
	const PROPERTY_TYPE = 'type';
	const PROPERTY_CATEGORY = 'category';
	const PROPERTY_SELECTED_VALUE = 'selected_value';
	const PROPERTY_TITLE = 'title';
	const PROPERTY_COMMENT = 'comment';
	const PROPERTY_SCOPE = 'scope';
	const PROPERTY_SUBKEYTEXT = 'subkeytext';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185SettingsCurrent object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185SettingsCurrent($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_ID, SELF :: PROPERTY_VARIABLE, SELF :: PROPERTY_SUBKEY, SELF :: PROPERTY_TYPE, SELF :: PROPERTY_CATEGORY, SELF :: PROPERTY_SELECTED_VALUE, SELF :: PROPERTY_TITLE, SELF :: PROPERTY_COMMENT, SELF :: PROPERTY_SCOPE, SELF :: PROPERTY_SUBKEYTEXT);
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
	 * Returns the id of this Dokeos185SettingsCurrent.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Sets the id of this Dokeos185SettingsCurrent.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}
	/**
	 * Returns the variable of this Dokeos185SettingsCurrent.
	 * @return the variable.
	 */
	function get_variable()
	{
		return $this->get_default_property(self :: PROPERTY_VARIABLE);
	}

	/**
	 * Sets the variable of this Dokeos185SettingsCurrent.
	 * @param variable
	 */
	function set_variable($variable)
	{
		$this->set_default_property(self :: PROPERTY_VARIABLE, $variable);
	}
	/**
	 * Returns the subkey of this Dokeos185SettingsCurrent.
	 * @return the subkey.
	 */
	function get_subkey()
	{
		return $this->get_default_property(self :: PROPERTY_SUBKEY);
	}

	/**
	 * Sets the subkey of this Dokeos185SettingsCurrent.
	 * @param subkey
	 */
	function set_subkey($subkey)
	{
		$this->set_default_property(self :: PROPERTY_SUBKEY, $subkey);
	}
	/**
	 * Returns the type of this Dokeos185SettingsCurrent.
	 * @return the type.
	 */
	function get_type()
	{
		return $this->get_default_property(self :: PROPERTY_TYPE);
	}

	/**
	 * Sets the type of this Dokeos185SettingsCurrent.
	 * @param type
	 */
	function set_type($type)
	{
		$this->set_default_property(self :: PROPERTY_TYPE, $type);
	}
	/**
	 * Returns the category of this Dokeos185SettingsCurrent.
	 * @return the category.
	 */
	function get_category()
	{
		return $this->get_default_property(self :: PROPERTY_CATEGORY);
	}

	/**
	 * Sets the category of this Dokeos185SettingsCurrent.
	 * @param category
	 */
	function set_category($category)
	{
		$this->set_default_property(self :: PROPERTY_CATEGORY, $category);
	}
	/**
	 * Returns the selected_value of this Dokeos185SettingsCurrent.
	 * @return the selected_value.
	 */
	function get_selected_value()
	{
		return $this->get_default_property(self :: PROPERTY_SELECTED_VALUE);
	}

	/**
	 * Sets the selected_value of this Dokeos185SettingsCurrent.
	 * @param selected_value
	 */
	function set_selected_value($selected_value)
	{
		$this->set_default_property(self :: PROPERTY_SELECTED_VALUE, $selected_value);
	}
	/**
	 * Returns the title of this Dokeos185SettingsCurrent.
	 * @return the title.
	 */
	function get_title()
	{
		return $this->get_default_property(self :: PROPERTY_TITLE);
	}

	/**
	 * Sets the title of this Dokeos185SettingsCurrent.
	 * @param title
	 */
	function set_title($title)
	{
		$this->set_default_property(self :: PROPERTY_TITLE, $title);
	}
	/**
	 * Returns the comment of this Dokeos185SettingsCurrent.
	 * @return the comment.
	 */
	function get_comment()
	{
		return $this->get_default_property(self :: PROPERTY_COMMENT);
	}

	/**
	 * Sets the comment of this Dokeos185SettingsCurrent.
	 * @param comment
	 */
	function set_comment($comment)
	{
		$this->set_default_property(self :: PROPERTY_COMMENT, $comment);
	}
	/**
	 * Returns the scope of this Dokeos185SettingsCurrent.
	 * @return the scope.
	 */
	function get_scope()
	{
		return $this->get_default_property(self :: PROPERTY_SCOPE);
	}

	/**
	 * Sets the scope of this Dokeos185SettingsCurrent.
	 * @param scope
	 */
	function set_scope($scope)
	{
		$this->set_default_property(self :: PROPERTY_SCOPE, $scope);
	}
	/**
	 * Returns the subkeytext of this Dokeos185SettingsCurrent.
	 * @return the subkeytext.
	 */
	function get_subkeytext()
	{
		return $this->get_default_property(self :: PROPERTY_SUBKEYTEXT);
	}

	/**
	 * Sets the subkeytext of this Dokeos185SettingsCurrent.
	 * @param subkeytext
	 */
	function set_subkeytext($subkeytext)
	{
		$this->set_default_property(self :: PROPERTY_SUBKEYTEXT, $subkeytext);
	}

}

?>