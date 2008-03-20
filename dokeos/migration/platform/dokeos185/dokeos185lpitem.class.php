<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a Dokeos185 lp_item
 *
 * @author Sven Vanpoucke
 */
class Dokeos185LpItem
{
	/**
	 * Dokeos185LpItem properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_LP_ID = 'lp_id';
	const PROPERTY_ITEM_TYPE = 'item_type';
	const PROPERTY_REF = 'ref';
	const PROPERTY_TITLE = 'title';
	const PROPERTY_DESCRIPTION = 'description';
	const PROPERTY_PATH = 'path';
	const PROPERTY_MIN_SCORE = 'min_score';
	const PROPERTY_MAX_SCORE = 'max_score';
	const PROPERTY_MASTERY_SCORE = 'mastery_score';
	const PROPERTY_PARENT_ITEM_ID = 'parent_item_id';
	const PROPERTY_PREVIOUS_ITEM_ID = 'previous_item_id';
	const PROPERTY_NEXT_ITEM_ID = 'next_item_id';
	const PROPERTY_DISPLAY_ORDER = 'display_order';
	const PROPERTY_PREREQUISITE = 'prerequisite';
	const PROPERTY_PARAMETERS = 'parameters';
	const PROPERTY_LAUNCH_DATA = 'launch_data';
	const PROPERTY_MAX_TIME_ALLOWED = 'max_time_allowed';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185LpItem object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185LpItem($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_ID, SELF :: PROPERTY_LP_ID, SELF :: PROPERTY_ITEM_TYPE, SELF :: PROPERTY_REF, SELF :: PROPERTY_TITLE, SELF :: PROPERTY_DESCRIPTION, SELF :: PROPERTY_PATH, SELF :: PROPERTY_MIN_SCORE, SELF :: PROPERTY_MAX_SCORE, SELF :: PROPERTY_MASTERY_SCORE, SELF :: PROPERTY_PARENT_ITEM_ID, SELF :: PROPERTY_PREVIOUS_ITEM_ID, SELF :: PROPERTY_NEXT_ITEM_ID, SELF :: PROPERTY_DISPLAY_ORDER, SELF :: PROPERTY_PREREQUISITE, SELF :: PROPERTY_PARAMETERS, SELF :: PROPERTY_LAUNCH_DATA, SELF :: PROPERTY_MAX_TIME_ALLOWED);
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
	 * Returns the id of this Dokeos185LpItem.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Returns the lp_id of this Dokeos185LpItem.
	 * @return the lp_id.
	 */
	function get_lp_id()
	{
		return $this->get_default_property(self :: PROPERTY_LP_ID);
	}

	/**
	 * Returns the item_type of this Dokeos185LpItem.
	 * @return the item_type.
	 */
	function get_item_type()
	{
		return $this->get_default_property(self :: PROPERTY_ITEM_TYPE);
	}

	/**
	 * Returns the ref of this Dokeos185LpItem.
	 * @return the ref.
	 */
	function get_ref()
	{
		return $this->get_default_property(self :: PROPERTY_REF);
	}

	/**
	 * Returns the title of this Dokeos185LpItem.
	 * @return the title.
	 */
	function get_title()
	{
		return $this->get_default_property(self :: PROPERTY_TITLE);
	}

	/**
	 * Returns the description of this Dokeos185LpItem.
	 * @return the description.
	 */
	function get_description()
	{
		return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
	}

	/**
	 * Returns the path of this Dokeos185LpItem.
	 * @return the path.
	 */
	function get_path()
	{
		return $this->get_default_property(self :: PROPERTY_PATH);
	}

	/**
	 * Returns the min_score of this Dokeos185LpItem.
	 * @return the min_score.
	 */
	function get_min_score()
	{
		return $this->get_default_property(self :: PROPERTY_MIN_SCORE);
	}

	/**
	 * Returns the max_score of this Dokeos185LpItem.
	 * @return the max_score.
	 */
	function get_max_score()
	{
		return $this->get_default_property(self :: PROPERTY_MAX_SCORE);
	}

	/**
	 * Returns the mastery_score of this Dokeos185LpItem.
	 * @return the mastery_score.
	 */
	function get_mastery_score()
	{
		return $this->get_default_property(self :: PROPERTY_MASTERY_SCORE);
	}

	/**
	 * Returns the parent_item_id of this Dokeos185LpItem.
	 * @return the parent_item_id.
	 */
	function get_parent_item_id()
	{
		return $this->get_default_property(self :: PROPERTY_PARENT_ITEM_ID);
	}

	/**
	 * Returns the previous_item_id of this Dokeos185LpItem.
	 * @return the previous_item_id.
	 */
	function get_previous_item_id()
	{
		return $this->get_default_property(self :: PROPERTY_PREVIOUS_ITEM_ID);
	}

	/**
	 * Returns the next_item_id of this Dokeos185LpItem.
	 * @return the next_item_id.
	 */
	function get_next_item_id()
	{
		return $this->get_default_property(self :: PROPERTY_NEXT_ITEM_ID);
	}

	/**
	 * Returns the display_order of this Dokeos185LpItem.
	 * @return the display_order.
	 */
	function get_display_order()
	{
		return $this->get_default_property(self :: PROPERTY_DISPLAY_ORDER);
	}

	/**
	 * Returns the prerequisite of this Dokeos185LpItem.
	 * @return the prerequisite.
	 */
	function get_prerequisite()
	{
		return $this->get_default_property(self :: PROPERTY_PREREQUISITE);
	}

	/**
	 * Returns the parameters of this Dokeos185LpItem.
	 * @return the parameters.
	 */
	function get_parameters()
	{
		return $this->get_default_property(self :: PROPERTY_PARAMETERS);
	}

	/**
	 * Returns the launch_data of this Dokeos185LpItem.
	 * @return the launch_data.
	 */
	function get_launch_data()
	{
		return $this->get_default_property(self :: PROPERTY_LAUNCH_DATA);
	}

	/**
	 * Returns the max_time_allowed of this Dokeos185LpItem.
	 * @return the max_time_allowed.
	 */
	function get_max_time_allowed()
	{
		return $this->get_default_property(self :: PROPERTY_MAX_TIME_ALLOWED);
	}
	
	static function get_all($parameters = array())
	{
		self :: $mgdm = $parameters['mgdm'];

		if($array['del_files'] =! 1)
			$tool_name = 'lp_item';
		
		$coursedb = $array['course'];
		$tablename = 'lp_item';
		$classname = 'Dokeos185LpItem';
			
		return self :: $mgdm->get_all($coursedb, $tablename, $classname, $tool_name);	
	}


}

?>