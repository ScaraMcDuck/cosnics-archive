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
		parent :: __construct($parent, $types, $mail_option, $maximum_select);
		$this->set_parameter(Tool :: PARAM_ACTION, $action);
		$this->set_repo_viewer_actions(array ('creator','browser', 'finder'));
		$this->parse_input_from_table();
	}
	
	private $creation_defaults;
	
	function set_creation_defaults($defaults)
	{
		$this->creation_defaults = $defaults;
	}
	
	function get_creation_defaults()
	{
		return $this->creation_defaults;
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
	 * Returns the repo_viewer's output in HTML format.
	 * @return string The output.
	 */
	function as_html()
	{
		$action = $this->get_action();
		
		$out = '<div class="tabbed-pane"><ul class="tabbed-pane-tabs">';
		$repo_viewer_actions = $this->get_repo_viewer_actions();
		foreach ($repo_viewer_actions as $repo_viewer_action)
		{
			$out .= '<li><a';
			if ($repo_viewer_action == $action)
			{
				$out .= ' class="current"';
			}
			elseif(($action == 'publicationcreator' || $action == 'multirepo_viewer') && $repo_viewer_action == 'creator')
			{
				$out .= ' class="current"';
			}
			$params = $this->get_parameters();
			$params[RepoViewer :: PARAM_ACTION] = $repo_viewer_action;
			$params[Tool :: PARAM_ACTION] = $this->get_parameter(Tool :: PARAM_ACTION);
			//$out .= ' href="'.$this->get_url(array (RepoViewer :: PARAM_ACTION => $repo_viewer_action, Tool :: PARAM_ACTION => $this->get_parameter(Tool :: PARAM_ACTION)), true).'">'.htmlentities(Translation :: get(ucfirst($repo_viewer_action).'Title')).'</a></li>';
			$out .= ' href="'.$this->get_url($params, true).'">'.htmlentities(Translation :: get(ucfirst($repo_viewer_action).'Title')).'</a></li>';
		}
		
		$out .= '</ul><div class="tabbed-pane-content">';
		
		require_once dirname(__FILE__).'/repo_viewer/learning_object_'.$action.'.class.php';
		$class = 'LearningObjectRepoViewer'.ucfirst($action).'Component';
		$component = new $class ($this);
		$out .= $component->as_html().'</div></div>';
		return $out;
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