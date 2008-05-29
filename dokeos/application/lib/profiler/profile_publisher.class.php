<?php
/**
 * @package application.lib.profiler
 */
require_once Path :: get_application_library_path(). 'publisher/publisher.class.php';
require_once Path :: get_repository_path(). 'lib/abstract_learning_object.class.php';

/**
==============================================================================
 *	This class provides the means to publish a learning object.
 *
 *	@author Tim De Pauw
==============================================================================
 */

class ProfilePublisher extends Publisher
{
	function ProfilePublisher($parent, $types, $mail_option = false)
	{
		parent :: __construct($parent, $types, $mail_option = false);
		$this->set_publisher_actions(array ('publicationcreator','browser', 'finder'));
	}

	/**
	 * Returns the publisher's output in HTML format.
	 * @return string The output.
	 */
	function as_html()
	{
		$out = '<div class="tabbed-pane"><ul class="tabbed-pane-tabs">';
		$publisher_actions = $this->get_publisher_actions();
		foreach ($publisher_actions as $action)
		{
			$out .= '<li><a';
			if ($this->get_action() == $action) $out .= ' class="current"';
			$out .= ' href="'.$this->get_url(array (Publisher :: PARAM_ACTION => $action), true).'">'.htmlentities(Translation :: get(ucfirst($action).'Title')).'</a></li>';
		}
		$out .= '</ul><div class="tabbed-pane-content">';
		$action = $this->get_action();
		
		require_once dirname(__FILE__).'/publisher/profile_'.$action.'.class.php';
		$class = 'ProfilePublisher'.ucfirst($action).'Component';
		$component = new $class ($this);
		$out .= $component->as_html().'</div></div>';
		return $out;
	}
}
?>