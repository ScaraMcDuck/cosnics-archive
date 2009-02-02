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

class SystemAnnouncementRepoViewer extends RepoViewer
{
	function SystemAnnouncer($parent, $types, $mail_option = false)
	{
		parent :: __construct($parent, $types, $mail_option = false);
		$this->set_repo_viewer_actions(array ('creator','browser', 'finder'));
		$this->parse_input_from_table();
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
			if ($action == $repo_viewer_action)
			{
				$out .= ' class="current"';
			}
			elseif(($action == 'publicationcreator' || $action == 'multirepo_viewer') && $repo_viewer_action == 'creator')
			{
				$out .= ' class="current"';
			}
			$out .= ' href="'.$this->get_url(array (RepoViewer :: PARAM_ACTION => $repo_viewer_action), true).'">'.htmlentities(Translation :: get(ucfirst($repo_viewer_action).'Title')).'</a></li>';
		}
		$out .= '</ul><div class="tabbed-pane-content">';
		
		require_once dirname(__FILE__).'/announcer/system_announcement_'.$action.'.class.php';
		$class = 'SystemAnnouncer'.ucfirst($action).'Component';
		$component = new $class ($this);
		$out .= $component->as_html().'</div></div>';
		return $out;
	}
}
?>