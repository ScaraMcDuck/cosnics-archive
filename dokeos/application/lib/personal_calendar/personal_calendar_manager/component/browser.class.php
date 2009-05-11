<?php
/**
 * @package application.personal_messenger.personal_messenger_manager.component
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__).'/../personal_calendar.class.php';
require_once dirname(__FILE__).'/../personal_calendar_component.class.php';
require_once dirname(__FILE__).'/../../renderer/personal_calendar_mini_month_renderer.class.php';
require_once dirname(__FILE__).'/../../renderer/personal_calendar_list_renderer.class.php';
require_once dirname(__FILE__).'/../../renderer/personal_calendar_month_renderer.class.php';
require_once dirname(__FILE__).'/../../renderer/personal_calendar_week_renderer.class.php';
require_once dirname(__FILE__).'/../../renderer/personal_calendar_day_renderer.class.php';
require_once Path :: get_library_path() . 'html/action_bar/action_bar_renderer.class.php';

class PersonalCalendarBrowserComponent extends PersonalCalendarComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{		
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('PersonalCalendar')));
		
		$this->display_header($trail);
		echo '<br /><a name="top"></a>';
		echo $this->get_action_bar_html() . '<br />';
		echo '<div id="action_bar_browser">';
		echo $this->get_calendar_html();
		echo '</div>';
		$this->display_footer();
	}
	
	function get_calendar_html()
	{
		$html = array();
		
		$time = isset ($_GET['time']) ? intval($_GET['time']) : time();
		$view = isset ($_GET['view']) ? $_GET['view'] : 'month';
		$this->set_parameter('time', $time);
		$this->set_parameter('view', $view);
		
		$minimonthcalendar = new PersonalCalendarMiniMonthRenderer($this, $time);
		$html[] = '<div class="mini_calendar">';
		$html[] = $minimonthcalendar->render();
		$html[] = '</div>';
		$html[] = '<div class="normal_calendar">';
		$show_calendar = true;
		
		if(isset($_GET['pid']))
		{
			$pid = $_GET['pid'];
			$event = $this->retrieve_calendar_event_publication($pid);
			if(isset($_GET['action']) && $_GET['action'] == 'delete')
			{
				$event->delete();
				$html[] = Display :: normal_message(Translation :: get('LearningObjectPublicationDeleted'),true);
			}
			else
			{
				$show_calendar = false;
				$learning_object = $event->get_publication_object();
				$display = LearningObjectDisplay :: factory($learning_object);
				$out .= '<h3>'.$learning_object->get_title().'</h3>';
				$out  .= $display->get_full_html();
				$toolbar_data = array();
				$toolbar_data[] = array(
					'href' => $this->get_url(),
					'label' => Translation :: get('Back'),
					'img' => Theme :: get_common_image_path().'action_prev.png',
					'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
				);
				$toolbar_data[] = array(
					'href' => $this->get_publication_deleting_url($event),
					'label' => Translation :: get('Delete'),
					'img' => Theme :: get_common_image_path().'action_delete.png',
					'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
				);
				$html[] = DokeosUtilities :: build_toolbar($toolbar_data, array(), 'margin-top: 1em;');
			}
		}
		
		if($show_calendar)
		{
			switch ($view)
			{
				case 'list' :
					$renderer = new PersonalCalendarListRenderer($this, $time);
					break;
				case 'day' :
					$renderer = new PersonalCalendarDayRenderer($this, $time);
					break;
				case 'week' :
					$renderer = new PersonalCalendarWeekRenderer($this, $time);
					break;
				default :
					$renderer = new PersonalCalendarMonthRenderer($this, $time);
					break;
			}
			$html[] = $renderer->render();
		}
		
		$html[] = '</div>';
		
		return implode("\n", $html);
	}
	
	function get_action_bar_html()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
	
		if (PlatformSetting :: get('allow_personal_agenda', 'user'))
		{
			$action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path().'action_publish.png', $this->get_url(array(PersonalCalendar :: PARAM_ACTION => PersonalCalendar :: ACTION_CREATE_PUBLICATION), true)));
		}

		$view = isset ($_GET['view']) ? $_GET['view'] : 'month';
		$time = $_GET['time'];
		
		if($view == 'list')
		{
			$action_bar->set_search_url($this->get_url(array('view' => $view, 'time' => $time)));
		}
		
		$action_bar->add_tool_action(new ToolbarItem(Translation :: get('ListView'), Theme :: get_image_path().'tool_calendar_down.png', $this->get_url(array (PersonalCalendar :: PARAM_ACTION => PersonalCalendar :: ACTION_BROWSE_CALENDAR, 'view' => 'list'))));
		$action_bar->add_tool_action(new ToolbarItem(Translation :: get('MonthView'), Theme :: get_image_path().'tool_calendar_month.png', $this->get_url(array (PersonalCalendar :: PARAM_ACTION => PersonalCalendar :: ACTION_BROWSE_CALENDAR, 'view' => 'month'))));
		$action_bar->add_tool_action(new ToolbarItem(Translation :: get('WeekView'), Theme :: get_image_path().'tool_calendar_week.png', $this->get_url(array (PersonalCalendar :: PARAM_ACTION => PersonalCalendar :: ACTION_BROWSE_CALENDAR, 'view' => 'week'))));
		$action_bar->add_tool_action(new ToolbarItem(Translation :: get('DayView'), Theme :: get_image_path().'tool_calendar_day.png', $this->get_url(array (PersonalCalendar :: PARAM_ACTION => PersonalCalendar :: ACTION_BROWSE_CALENDAR, 'view' => 'day'))));
		$action_bar->add_tool_action(new ToolbarItem(Translation :: get('Today'), Theme :: get_image_path().'tool_calendar_today.png', $this->get_url(array (PersonalCalendar :: PARAM_ACTION => PersonalCalendar :: ACTION_BROWSE_CALENDAR, 'view' => (isset ($_GET['view']) ? $_GET['view'] : 'month'), 'time' => time()))));
		
		return $action_bar->as_html();
	}
}
?>