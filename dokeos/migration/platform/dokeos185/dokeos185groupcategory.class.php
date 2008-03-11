<?php

/**
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__).'/../../lib/import/importgroup.class.php';

/**
 * This class represents an old Dokeos 1.8.5 Group category
 *
 * @author Sven Vanpoucke
 */
class Dokeos185GroupCategory extends ImportGroupCategory
{
	/**
	 * Migration data manager
	 */
	private static $mgdm;

	/**
	 * Group properties
	 */	 
	const PROPERTY_ID = 'id';
	const PROPERTY_TITLE = 'name';
	const PROPERTY_GROUPS_PER_USER = 'groups_per_user';
	const PROPERTY_DESCRIPTION = 'description';
	const PROPERTY_MAX_STUDENT = 'max_student';
	const PROPERTY_DOC_STATE = 'doc_state';
	const PROPERTY_CALENDAR_STATE = 'calendar_state';
	const PROPERTY_WORK_STATE = 'work_state';
	const PROPERTY_ANNOUNCEMENTS_STATE = 'groups_state';
	const PROPERTY_DISPLAY_ORDER = 'display_order';
	const PROPERTY_SELF_REG_ALLOWED = 'self_reg_allowed';
	const PROPERTY_SELF_UNREG_ALLOWED = 'self_unreg_allowed';
	
	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;
	
	/**
	 * Creates a new dokeos185 Announcement object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185Group($defaultProperties = array ())
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
		return array (self :: PROPERTY_ID, self :: PROPERTY_NAME, self :: PROPERTY_GROUPS_PER_USER,
					  self :: PROPERTY_DESCRIPTION, self :: PROPERTY_MAX_STUDENT, 
					  self :: PROPERTY_DOC_STATE, self :: PROPERTY_CALENDAR_STATE, 
					  self :: PROPERTY_WORK_STATE, self :: PROPERTY_ANNOUNCEMENTS_STATE, 
					  self :: PROPERTY_DISPLAY_ORDER, self :: PROPERTY_SELF_REG_ALLOWED,
					  self :: PROPERTY_SELF_UNREG_ALLOWED);
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
	 * Returns the id of this group.
	 * @return int The id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}
	 
	/**
	 * Returns the name of this group.
	 * @return string the name.
	 */
	function get_name()
	{
		return $this->get_default_property(self :: PROPERTY_NAME);
	}
	
	/**
	 * Returns the groups_per_user of this group.
	 * @return string the groups_per_user.
	 */
	function get_groups_per_user()
	{
		return $this->get_default_property(self :: PROPERTY_GROUPS_PER_USER);
	}
	
	/**
	 * Returns the description of this group.
	 * @return date the description.
	 */
	function get_description()
	{
		return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
	}
	
	/**
	 * Returns the max_student of this group.
	 * @return int the max_student.
	 */
	function get_max_student()
	{
		return $this->get_default_property(self :: PROPERTY_MAX_STUDENT);
	}
	
	/**
	 * Returns the doc_state of this group.
	 * @return int the doc_state.
	 */
	function get_doc_state()
	{
		return $this->get_default_property(self :: PROPERTY_DOC_STATE);
	}
	
	
	/**
	 * Returns the calendar_state of this groupcategory.
	 * @return int The calendar_state.
	 */
	function get_calendar_state()
	{
		return $this->get_default_property(self :: PROPERTY_CALENDAR_STATE);
	}
	 
	/**
	 * Returns the work_state of this groupcategory.
	 * @return string the work_state.
	 */
	function get_work_state()
	{
		return $this->get_default_property(self :: PROPERTY_WORK_STATE);
	}
	
	/**
	 * Returns the groupcategorys_state of this groupcategory.
	 * @return string the groupcategorys_state.
	 */
	function get_groupcategorys_state()
	{
		return $this->get_default_property(self :: PROPERTY_ANNOUNCEMENTS_STATE);
	}
	
	/**
	 * Returns the display_order of this groupcategory.
	 * @return date the display_order.
	 */
	function get_display_order()
	{
		return $this->get_default_property(self :: PROPERTY_DISPLAY_ORDER);
	}
	
	/**
	 * Returns the self_reg_allowed of this groupcategory.
	 * @return int the self_reg_allowed.
	 */
	function get_self_reg_allowed()
	{
		return $this->get_default_property(self :: PROPERTY_SELF_REG_ALLOWED);
	}
	
	/**
	 * Returns the self_unreg_allowed of this groupcategory.
	 * @return int the self_unreg_allowed.
	 */
	function get_self_unreg_allowed()
	{
		return $this->get_default_property(self :: PROPERTY_SELF_UNREG_ALLOWED);
	}
	
	/**
	 * Sets the id of this group.
	 * @param int $id The id.
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}
	
	/**
	 * Sets the name of this group.
	 * @param string $name The name
	 */
	function set_name($name)
	{
		$this->set_default_property(self :: PROPERTY_NAME, $name);
	}
	
	/**
	 * Sets the groups_per_user of this group.
	 * @param string $groups_per_user The groups_per_user
	 */
	function set_groups_per_user($groups_per_user)
	{
		$this->set_default_property(self :: PROPERTY_GROUPS_PER_USER, $groups_per_user);
	}
	
	/**
	 * Sets the description of this group.
	 * @param string $description The description
	 */
	function set_description($description)
	{
		$this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
	}
	
	/**
	 * Sets the max_student of this group.
	 * @param string $max_student The max_student
	 */
	function set_max_student($max_student)
	{
		$this->set_default_property(self :: PROPERTY_MAX_STUDENT, $max_student);
	}
	
	/**
	 * Sets the doc_state of this group.
	 * @param string $doc_state The doc_state
	 */
	function set_doc_state($doc_state)
	{
		$this->set_default_property(self :: PROPERTY_DOC_STATE, $doc_state);
	}
	
	
	/**
	 * Sets the calendar_state of this groupcategory.
	 * @param int $calendar_state The calendar_state.
	 */
	function set_calendar_state($calendar_state)
	{
		$this->set_default_property(self :: PROPERTY_CALENDAR_STATE, $calendar_state);
	}
	
	/**
	 * Sets the work_state of this groupcategory.
	 * @param string $work_state The work_state
	 */
	function set_work_state($work_state)
	{
		$this->set_default_property(self :: PROPERTY_WORK_STATE, $work_state);
	}
	
	/**
	 * Sets the groupcategorys_state of this groupcategory.
	 * @param string $groupcategorys_state The groupcategorys_state
	 */
	function set_groupcategorys_state($groupcategorys_state)
	{
		$this->set_default_property(self :: PROPERTY_ANNOUNCEMENTS_STATE, $groupcategorys_state);
	}
	
	/**
	 * Sets the display_order of this groupcategory.
	 * @param string $display_order The display_order
	 */
	function set_display_order($display_order)
	{
		$this->set_default_property(self :: PROPERTY_DISPLAY_ORDER, $display_order);
	}
	
	/**
	 * Sets the self_reg_allowed of this groupcategory.
	 * @param string $self_reg_allowed The self_reg_allowed
	 */
	function set_self_reg_allowed($self_reg_allowed)
	{
		$this->set_default_property(self :: PROPERTY_SELF_REG_ALLOWED, $self_reg_allowed);
	}
	
	/**
	 * Sets the self_unreg_allowed of this groupcategory.
	 * @param string $self_unreg_allowed The self_unreg_allowed
	 */
	function set_self_unreg_allowed($self_unreg_allowed)
	{
		$this->set_default_property(self :: PROPERTY_SELF_UNREG_ALLOWED, $self_unreg_allowed);
	}
	
	function is_valid_group_category($course)
	{
		
	}
	
	function convert_to_new_group_category($course)
	{
		
	}
	
	static function get_all_group_categories($mgdm,$db)
	{
		self :: $mgdm = $mgdm;
		return self :: $mgdm->get_all_groups($db);
	}
	
}
?>