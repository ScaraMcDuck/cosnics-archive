<?php
/**
 * @package application.lib.profiler.profiler_manager
 */
require_once dirname(__FILE__).'/../personal_calendar.class.php';
require_once dirname(__FILE__).'/../personal_calendar_component.class.php';
require_once dirname(__FILE__).'/../../renderer/personal_calendar_mini_month_renderer.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';
require_once Path :: get_repository_path(). 'lib/learning_object_display.class.php';
require_once Path :: get_library_path() . 'html/action_bar/action_bar_renderer.class.php';

class PersonalCalendarViewerComponent extends PersonalCalendarComponent
{	
	private $folder;
	private $publication;
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('ViewPersonalCalendarEvent')));
		
		$id = $_GET[PersonalCalendar :: PARAM_CALENDAR_EVENT_ID];
		
		if ($id)
		{
			$event = $this->retrieve_calendar_event_publication($id);
			
			$this->display_header($trail);
			echo '<br /><a name="top"></a>';
			echo $this->get_action_bar_html() . '<br />';
			echo '<div id="action_bar_browser">';
			echo $this->get_publication_as_html($event);
			echo '</div>';
			
			$this->display_footer();
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoProfileSelected')));
		}
	}
	
	function get_publication_as_html($event)
	{
		$learning_object = $event->get_publication_object();
		$display = LearningObjectDisplay :: factory($learning_object);
		$html = array();
		
		$time = isset ($_GET['time']) ? intval($_GET['time']) : time();
		$view = isset ($_GET['view']) ? $_GET['view'] : 'month';
		$this->set_parameter('time', $time);
		$this->set_parameter('view', $view);
		$this->set_parameter(PersonalCalendar :: PARAM_ACTION, PersonalCalendar :: ACTION_BROWSE_CALENDAR);
		
		$minimonthcalendar = new PersonalCalendarMiniMonthRenderer($this, $time);
		$html[] =   '<div style="float: left; width: 20%;">';
		$html[] =   $minimonthcalendar->render();
		$html[] =   '</div>';
		$html[] =   '<div style="float: left; width: 80%;">';
		
		$html[] = $display->get_full_html();

		$toolbar_data = array();
		$toolbar_data[] = array(
			'href' => $this->get_url(),
			'label' => Translation :: get('Back'),
			'img' => Theme :: get_common_img_path().'action_prev.png',
			'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
		);
		$toolbar_data[] = array(
			'href' => $this->get_publication_editing_url($event),
			'label' => Translation :: get('Edit'),
			'img' => Theme :: get_common_img_path().'action_edit.png',
			'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
		);		
		$toolbar_data[] = array(
			'href' => $this->get_publication_deleting_url($event),
			'label' => Translation :: get('Delete'),
			'img' => Theme :: get_common_img_path().'action_delete.png',
			'confirm' => true,
			'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
		);
		$html[] = DokeosUtilities :: build_toolbar($toolbar_data, array(), 'margin-top: 1em;');
		
		$html[] =   '</div>';		
		
		return implode("\n",$html);
	}
	
	function get_action_bar_html()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
		
		if (PlatformSetting :: get('allow_personal_agenda', 'user'))
		{
			$action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_img_path().'action_publish.png', $this->get_url(array(PersonalCalendar :: PARAM_ACTION => PersonalCalendar :: ACTION_CREATE_PUBLICATION), true)));
		}
		
		$action_bar->add_tool_action(new ToolbarItem(Translation :: get('ListView'), Theme :: get_img_path().'tool_calendar_down.png', $this->get_url(array (PersonalCalendar :: PARAM_ACTION => PersonalCalendar :: ACTION_BROWSE_CALENDAR, 'view' => 'list'))));
		$action_bar->add_tool_action(new ToolbarItem(Translation :: get('MonthView'), Theme :: get_img_path().'tool_calendar_month.png', $this->get_url(array (PersonalCalendar :: PARAM_ACTION => PersonalCalendar :: ACTION_BROWSE_CALENDAR, 'view' => 'month'))));
		$action_bar->add_tool_action(new ToolbarItem(Translation :: get('WeekView'), Theme :: get_img_path().'tool_calendar_week.png', $this->get_url(array (PersonalCalendar :: PARAM_ACTION => PersonalCalendar :: ACTION_BROWSE_CALENDAR, 'view' => 'week'))));
		$action_bar->add_tool_action(new ToolbarItem(Translation :: get('DayView'), Theme :: get_img_path().'tool_calendar_day.png', $this->get_url(array (PersonalCalendar :: PARAM_ACTION => PersonalCalendar :: ACTION_BROWSE_CALENDAR, 'view' => 'day'))));
		$action_bar->add_tool_action(new ToolbarItem(Translation :: get('Today'), Theme :: get_img_path().'tool_calendar_today.png', $this->get_url(array (PersonalCalendar :: PARAM_ACTION => PersonalCalendar :: ACTION_BROWSE_CALENDAR, 'view' => (isset ($_GET['view']) ? $_GET['view'] : 'month'), 'time' => time()))));
		
		return $action_bar->as_html();
	}
}
?>