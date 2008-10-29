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

class LearningObjectPublisher extends Publisher
{
	/**
	 * The default learning objects, which are used for form defaults.
	 */
	
	function LearningObjectPublisher($parent, $types, $mail_option = false)
	{
		parent :: __construct($parent, $types, $mail_option = false);
		$this->set_parameter(Tool :: PARAM_ACTION, Tool :: ACTION_PUBLISH);
		$this->set_publisher_actions(array ('creator','browser', 'finder'));
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
			$out .= ' href="'.$this->get_url(array (Publisher :: PARAM_ACTION => $action, Tool :: PARAM_ACTION => Tool :: ACTION_PUBLISH), true).'">'.htmlentities(Translation :: get(ucfirst($action).'Title')).'</a></li>';
		}
		$out .= '</ul><div class="tabbed-pane-content">';
		$action = $this->get_action();
		
		require_once dirname(__FILE__).'/publisher/learning_object_'.$action.'.class.php';
		$class = 'LearningObjectPublisher'.ucfirst($action).'Component';
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