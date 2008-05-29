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

class PersonalCalendarBrowserComponent extends PersonalCalendarComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{		
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('MyAgenda')));
		
		$this->display_header($trail);
		echo $this->get_calendar_html();
		$this->display_footer();
	}
	
	function get_calendar_html()
	{
		$out =  '<p><a href="'.$this->get_url(array(PersonalCalendar :: PARAM_ACTION => PersonalCalendar :: ACTION_CREATE_PUBLICATION), true).'"><img src="'.Theme :: get_common_img_path().'action_publish.png" alt="'.Translation :: get('Publish').'" style="vertical-align:middle;"/> '.Translation :: get('Publish').'</a></p>';
		$time = isset ($_GET['time']) ? intval($_GET['time']) : time();
		$view = isset ($_GET['view']) ? $_GET['view'] : 'month';
		$this->set_parameter('time', $time);
		$this->set_parameter('view', $view);
		$toolbar_data = array ();
		$toolbar_data[] = array ('href' => $this->get_url(array (PersonalCalendar :: PARAM_ACTION => PersonalCalendar :: ACTION_BROWSE_CALENDAR, 'view' => 'list')), 'img' => Theme :: get_img_path().'tool_calendar_down.png', 'label' => Translation :: get('ListView'), 'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);
		$toolbar_data[] = array ('href' => $this->get_url(array (PersonalCalendar :: PARAM_ACTION => PersonalCalendar :: ACTION_BROWSE_CALENDAR, 'view' => 'month')), 'img' => Theme :: get_img_path().'tool_calendar_month.png', 'label' => Translation :: get('MonthView'), 'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);
		$toolbar_data[] = array ('href' => $this->get_url(array (PersonalCalendar :: PARAM_ACTION => PersonalCalendar :: ACTION_BROWSE_CALENDAR, 'view' => 'week')), 'img' => Theme :: get_img_path().'tool_calendar_week.png', 'label' => Translation :: get('WeekView'), 'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);
		$toolbar_data[] = array ('href' => $this->get_url(array (PersonalCalendar :: PARAM_ACTION => PersonalCalendar :: ACTION_BROWSE_CALENDAR, 'view' => 'day')), 'img' => Theme :: get_img_path().'tool_calendar_day.png', 'label' => Translation :: get('DayView'), 'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);
		$out .=  '<div style="margin-bottom: 1em;">'.DokeosUtilities :: build_toolbar($toolbar_data).'</div>';
		$minimonthcalendar = new PersonalCalendarMiniMonthRenderer($this, $time);
		$out .=   '<div style="float: left; width: 20%;">';
		$out .=   $minimonthcalendar->render();
		$out .=   '</div>';
		$out .=   '<div style="float: left; width: 80%;">';
		$show_calendar = true;
		if(isset($_GET['pid']))
		{
			$pid = $_GET['pid'];
			$event = $this->retrieve_calendar_event_publication($pid);
			if(isset($_GET['action']) && $_GET['action'] == 'delete')
			{
				$event->delete();
				$out .= Display::display_normal_message(Translation :: get('LearningObjectPublicationDeleted'),true);
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
					'img' => Theme :: get_common_img_path().'action_prev.png',
					'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
				);
				$toolbar_data[] = array(
					'href' => $this->get_publication_deleting_url($event),
					'label' => Translation :: get('Delete'),
					'img' => Theme :: get_common_img_path().'action_delete.png',
					'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
				);
				$out .= DokeosUtilities :: build_toolbar($toolbar_data, array(), 'margin-top: 1em;');
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
			$out .=   $renderer->render();
		}
		$out .=   '</div>';
		
		return $out;
	}
}
?>