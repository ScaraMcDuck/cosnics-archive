<?php
/**
 * $Id$
 * Tool
 * @package application.weblcms.tool
 */

require_once dirname(__FILE__).'/../../../../repository/lib/repositoryutilities.class.php';

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
	 * @see Application :: display_header()
	 */
	function display_header($breadcrumbs = array(), $append = array())
	{
		$breadcrumbs[] = array ('url' => $this->get_url(null, false, true, array('tool')), 'name' => $_GET[Weblcms :: PARAM_COURSE]);
		if(!is_null($this->parent->get_group()))
		{
			$group = $this->parent->get_group();
			$breadcrumbs[] = array( 'url' => $this->get_url(array(Weblcms::PARAM_GROUP=>null)), 'name' => Translation :: get_lang('Groups'));
			$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => $group->get_name());
		}
		// TODO: do this by overriding display_header in the group tool
		elseif($this->get_tool_id() == 'group')
		{
			$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => Translation :: get_lang(Tool :: type_to_class($this->get_tool_id()).'Title'));
		}
		// TODO: make this the default
		if($this->get_tool_id() != 'group')
		{
			$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => Translation :: get_lang(Tool :: type_to_class($this->get_tool_id()).'Title'));
		}
		if (count($append))
		{
			foreach ($append as $extra)
			{
				$breadcrumbs[] = $extra;
			}
		}
		$this->parent->display_header($breadcrumbs);
	}
	/**
	 * @see Application :: display_footer()
	 */
	function display_footer()
	{
		$this->parent->display_footer();
	}

	/**
	 * Informs the user that access to the page was denied.
	 */
	function disallow()
	{
		$this->display_header();
		api_not_allowed();
		$this->display_footer();
	}

	/**
	 * @see WebApplication :: get_user()
	 */
	function get_user()
	{
		return $this->parent->get_user();
	}

	/**
	 * @see WebApplication :: get_user_id()
	 */
	function get_user_id()
	{
		return $this->parent->get_user_id();
	}

	function get_user_info($user_id)
	{
		return $this->parent->get_user_info($user_id);
	}

	/**
	 * @see WebApplication :: get_course_id()
	 */
	function get_course()
	{
		return $this->parent->get_course();
	}

	/**
	 * @see WebApplication :: get_course_id()
	 */
	function get_course_id()
	{
		return $this->parent->get_course_id();
	}

	/**
	 * @see WebApplication :: get_groups()
	 */
	function get_groups()
	{
		return $this->parent->get_groups();
	}

	/**
	 * @see WebApplication :: get_parameters()
	 */
	function get_parameters()
	{
		return $this->parent->get_parameters();
	}

	/**
	 * @see WebApplication :: get_parameter()
	 */
	function get_parameter($name)
	{
		return $this->parent->get_parameter($name);
	}

	/**
	 * @see WebApplication :: set_parameter()
	 */
	function set_parameter($name, $value)
	{
		$this->parent->set_parameter($name, $value);
	}

	/**
	 * @see WebApplication :: get_url()
	 */
	function get_url($parameters = array(), $encode = false, $filter = false, $filterOn = array())
	{
		return $this->parent->get_url($parameters, $encode, $filter, $filterOn);
	}

	/**
	 * @see WebApplication :: get_url()
	 */
	function redirect($action = null, $message = null, $error_message = false, $extra_params = null)
	{
		return $this->parent->redirect($action, $message, $error_message, $extra_params);
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
		/**
		 * Here we set the rights depending on the user status in the course.
		 * This completely ignores the roles-rights library.
		 * TODO: WORK NEEDED FOR PROPPER ROLES-RIGHTS LIBRARY
		 */
		$user = $this->get_user();
		$course = $this->get_course();
		$relation = $this->parent->retrieve_course_user_relation($course->get_id(),$user->get_user_id());
		$this->rights[VIEW_RIGHT] = false;
		$this->rights[EDIT_RIGHT] = false;
		$this->rights[ADD_RIGHT] = false;
		$this->rights[DELETE_RIGHT] = false;
		if($relation->get_status() == 5)
		{
			$this->rights[VIEW_RIGHT] = true;
		}
		if($relation->get_status() == 1 || $user->is_admin())
		{
			$this->rights[VIEW_RIGHT] = true;
			$this->rights[EDIT_RIGHT] = true;
			$this->rights[ADD_RIGHT] = true;
			$this->rights[DELETE_RIGHT] = true;
		}
		return;
		$tool_id = $this->get_tool_id();
		//TODO: phase out, because hardcoding tool names is hardly modular
		switch ($tool_id) {
			case 'description': $tool_name = TOOL_COURSE_DESCRIPTION; break;
			case 'announcement': $tool_name = TOOL_ANNOUNCEMENT; break;
			case 'calendar': $tool_name = TOOL_CALENDAR_EVENT; break;
			case 'link': $tool_name = TOOL_LINK; break;
			case 'document': $tool_name = TOOL_DOCUMENT; break;
			case 'forum': $tool_name = TOOL_FORUM; break;
			case 'dropbox': $tool_name = TOOL_DROPBOX; break;
			case 'wiki': $tool_name = TOOL_WIKI; break;
			case 'chat': $tool_name = TOOL_CHAT; break;
			case 'learning_path': $tool_name = TOOL_LEARNING_PATH; break;
			case 'exercise': $tool_name = TOOL_EXERCISE; break;
			case 'group': $tool_name = TOOL_GROUP; break;
			// The proper way. We should only keep this, eventually.
			default: $tool_name = $tool_id;
		}
		// Roles and rights system
		$user_id = $this->get_user_id();
		$course_id = $this->get_course_id();
		
		// TODO: New Roles & Rights system
//		$role_id = RolesRights::get_local_user_role_id($user_id, $course_id);
//		$location_id = RolesRights::get_course_tool_location_id($course_id, $tool_name);
//		$this->rights = RolesRights::is_allowed_which_rights($role_id, $location_id);
	}

	/**
	 * Converts a tool name to the corresponding class name.
	 * @param string $tool The tool name.
	 * @return string The class name.
	 */
	static function type_to_class($tool)
	{
		return RepositoryUtilities :: underscores_to_camelcase($tool).'Tool';
	}

	/**
	 * Converts a tool class name to the corresponding tool name.
	 * @param string $class The class name.
	 * @return string The tool name.
	 */
	static function class_to_type($class)
	{
		return str_replace('/Tool$/', '', RepositoryUtilities :: camelcase_to_underscores($class));
	}
	/**
	 * @see WebLcms::get_last_visit_date()
	 */
	function get_last_visit_date()
	{
		return $this->parent->get_last_visit_date();
	}
	
	function get_path($path_type)
	{
		return $this->get_parent()->get_path($path_type);
	}
}
?>