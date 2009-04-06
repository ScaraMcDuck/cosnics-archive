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

class ComplexRepoViewer extends RepoViewer
{
	/**
	 * The default learning objects, which are used for form defaults.
	 */
	
	function LearningObjectRepoViewer($parent, $types, $mail_option = false, $maximum_select = RepoViewer :: SELECT_MULTIPLE)
	{
		parent :: __construct($parent, $types, $mail_option, $maximum_select);
		$this->set_repo_viewer_actions(array ('creator','browser', 'finder'));
		$this->parse_input_from_table();
	}
	
	function redirect_complex($type)
	{
		return false;
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
			$out .= ' href="'.$this->get_url($params, true).'">'.htmlentities(Translation :: get(ucfirst($repo_viewer_action).'Title')).'</a></li>';
		}
		
		$out .= '</ul><div class="tabbed-pane-content">';
		
		require_once dirname(__FILE__).'/repo_viewer/complex_learning_object_'.$action.'.class.php';
		$class = 'ComplexLearningObjectRepoViewer'.ucfirst($action).'Component';
		$component = new $class ($this);
		$out .= $component->as_html().'</div></div>';
		return $out;
	}
}
?>