<?php
/**
 * $Id:$
 * @package application.personal_calendar
 */
class PersonalCalendarPublisher
{
	const PARAM_ACTION = 'publish_action';
	/**
	 * The personal calendar in which this publisher runs
	 */
	private $personal_calendar;
	/**
	 * Creates a new personal calendar publisher
	 * @param PersonalCalendar $personal_calendar
	 */
	function PersonalCalendarPublisher($personal_calendar)
	{
		$this->personal_calendar = $personal_calendar;
	}
	/**
	 * Gets the HTML-representation of this publisher
	 * @return string
	 */
	public function as_html()
	{
		$out = '<div class="tabbed-pane"><ul class="tabbed-pane-tabs">';
		foreach (array ('publicationcreator', 'browser', 'finder') as $action)
		{
			$out .= '<li><a';
			if ($this->get_action() == $action)
				$out .= ' class="current"';
			$out .= ' href="'.$this->get_url(array (PersonalCalendarPublisher :: PARAM_ACTION => $action), true).'">'.htmlentities(get_lang(ucfirst($action).'Title')).'</a></li>';
		}
		$out .= '</ul><div class="tabbed-pane-content">';

		$action = $this->get_action();
		require_once dirname(__FILE__).'/component/personalcalendar'.$action.'.class.php';
		$class = 'PersonalCalendar'.ucfirst($action);
		$component = new $class ($this);
		$out .= $component->as_html();
		$out .= '</div></div>';
		return $out;
	}
	/**
	 * @see PersonalCalendar::get_url()
	 */
	function get_url($parameters = array(), $encode = false)
	{
		return $this->personal_calendar->get_url($parameters, $encode);
	}
	/**
	 * Returns the action that the user selected, or "publicationcreator" if none.
	 * @return string The action.
	 */
	function get_action()
	{
		return ($_GET[PersonalCalendarPublisher :: PARAM_ACTION] ? $_GET[PersonalCalendarPublisher :: PARAM_ACTION] : 'publicationcreator');
	}
	/**
	 * @see PersonalCalendar::get_user_id()
	 */
	function get_user_id()
	{
		return $this->personal_calendar->get_user_id();
	}
}
?>