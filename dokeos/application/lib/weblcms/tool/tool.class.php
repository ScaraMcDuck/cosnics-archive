<?php
/**
 * Tool
 * @package application.weblcms.tool
 */
/**
==============================================================================
 *	This is the base class for all tools used in applications.
 *
 *	@author Tim De Pauw
==============================================================================
 */

abstract class Tool
{
	/**
	 * The application that the tool is associated with.
	 */
	private $parent;

	/**
	 * The rights of the current user in this tool
	 */
	private $rights;

	/**
	 * Constructor.
	 * @param Application $parent The application that the tool is associated
	 *                            with.
	 */
	function Tool($parent)
	{
		$this->parent = $parent;
		$this->load_rights();
	}

	/**
	 * Runs the tool, performing whatever actions are necessary.
	 */
	abstract function run();

	/**
	 * Returns the application that this tool is associated with.
	 * @return Application The application.
	 */
	function get_parent()
	{
		return $this->parent;
	}

	/**
	 * @see Application :: get_tool_id()
	 */
	function get_tool_id()
	{
		return $this->parent->get_tool_id();
	}

	/**
	 * @see Application :: get_parameters()
	 */
	function get_parameters()
	{
		return $this->parent->get_parameters();
	}

	/**
	 * @see Application :: get_parameter()
	 */
	function get_parameter($name)
	{
		return $this->parent->get_parameter($name);
	}

	/**
	 * @see Application :: set_parameter()
	 */
	function set_parameter($name, $value)
	{
		$this->parent->set_parameter($name, $value);
	}

	/**
	 * @see Application :: get_url()
	 */
	function get_url($parameters = array(), $encode = false)
	{
		return $this->parent->get_url($parameters, $encode);
	}

	/**
	 * Check if the current user has a given right in this tool
	 * @param int $right
	 * @return boolean True if the current user has the right
	 */
	function is_allowed($right)
	{
		return $this->rights[$right];
	}

	/**
	 * Load the rights for the current user in this tool
	 */
	private function load_rights()
	{
		$tool_id = $this->get_tool_id();
		//TODO: next lines map the tool-id to the Dokeos defined tool constants. The same values should be used everywhere.
		$dokeos_tools['description'] = TOOL_COURSE_DESCRIPTION;
		$dokeos_tools['announcement'] = TOOL_ANNOUNCEMENT;
		$dokeos_tools['calendar'] = TOOL_CALENDAR_EVENT;
		// Roles and rights system
		$user_id = api_get_user_id();
		$course_id = api_get_course_id();
		$role_id = RolesRights::get_local_user_role_id($user_id, $course_id);
		$location_id = RolesRights::get_course_tool_location_id($course_id, $dokeos_tools[$tool_id]);
		$this->rights = RolesRights::is_allowed_which_rights($role_id, $location_id);
	}
}
?>