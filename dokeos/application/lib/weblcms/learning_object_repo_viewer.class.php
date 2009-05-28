<?php
/**
 * @package application.lib.profiler
 */
require_once Path :: get_application_library_path(). 'repo_viewer/repo_viewer.class.php';
require_once Path :: get_repository_path(). 'lib/abstract_learning_object.class.php';

/**
==============================================================================
 *	This class provides the means to publish a learning object.
 *
 *	@author Tim De Pauw
==============================================================================
 */

class LearningObjectRepoViewer extends RepoViewer
{
	/**
	 * The default learning objects, which are used for form defaults.
	 */

	function LearningObjectRepoViewer($parent, $types, $mail_option = false, $maximum_select = RepoViewer :: SELECT_MULTIPLE, $action = TOOL :: ACTION_PUBLISH)
	{
		parent :: __construct($parent, $types, $mail_option, $maximum_select, array(), false);
		$this->set_parameter(Tool :: PARAM_ACTION, $action);
        if(Request :: get('pid') != null)
        $this->set_parameter('pid',Request :: get('pid'));
		$this->set_repo_viewer_actions(array ('creator','browser', 'finder'));
		$this->parse_input_from_table();
	}

	function redirect_complex($type)
	{
		switch ($type)
		{
			case 'forum_topic':
				return false;
			default: return true;
		}
	}
	
	/**
	 * @see Tool::get_course()
	 */
	function get_course()
	{
		return $this->get_parent()->get_course();
	}

	/**
	 * @see Tool::get_course_id()
	 */
	function get_course_id()
	{
		return $this->get_parent()->get_course_id();
	}

	/**
	 * @see Tool::get_course()
	 */
	function get_user()
	{
		return $this->get_parent()->get_user();
	}

	/**
	 * @see Tool::get_categories()
	 */
	function get_categories()
	{
		return $this->get_parent()->get_categories();
	}

	/**
	 * @see Tool::get_tool()
	 */
	function get_tool()
	{
		return $this->get_parent();
	}

	
}
?>