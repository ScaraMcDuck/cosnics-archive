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

class CalendarEventPublisher extends Publisher
{
	function CalendarEventPublisher($parent, $types, $mail_option = false)
	{
		parent :: __construct($parent, $types, $mail_option = false);
		$this->set_publisher_actions(array ('creator','browser', 'finder'));
	}

	/**
	 * Returns the publisher's output in HTML format.
	 * @return string The output.
	 */
	function as_html()
	{
		$action = $this->get_action();
		
		$out = '<div class="tabbed-pane"><ul class="tabbed-pane-tabs">';
		$publisher_actions = $this->get_publisher_actions();
		foreach ($publisher_actions as $publisher_action)
		{
			$out .= '<li><a';
			if ($this->get_action() == $publisher_action)
			{
				$out .= ' class="current"';
			}			
			elseif(($action == 'publicationcreator' || $action == 'multipublisher') && $publisher_action == 'creator')
			{
				$out .= ' class="current"';
			}
			$out .= ' href="'.$this->get_url(array (Publisher :: PARAM_ACTION => $publisher_action), true).'">'.htmlentities(Translation :: get(ucfirst($publisher_action).'Title')).'</a></li>';
		}
		$out .= '</ul><div class="tabbed-pane-content">';
		
		require_once dirname(__FILE__).'/publisher/calendar_event_'.$action.'.class.php';
		$class = 'CalendarEventPublisher'.ucfirst($action).'Component';
		$component = new $class ($this);
		$out .= $component->as_html().'</div></div>';
		return $out;
	}
}
?>