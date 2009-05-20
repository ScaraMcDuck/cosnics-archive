<?php
/**
 * @package application.lib.profiler.profiler_manager
 */
require_once dirname(__FILE__).'/../personal_calendar_manager.class.php';
require_once dirname(__FILE__).'/../personal_calendar_manager_component.class.php';
require_once dirname(__FILE__).'/../../renderer/personal_calendar_mini_month_renderer.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';
require_once Path :: get_repository_path(). 'lib/learning_object_display.class.php';
require_once Path :: get_library_path() . 'html/action_bar/action_bar_renderer.class.php';

class PersonalCalendarManagerViewerComponent extends PersonalCalendarManagerComponent
{	
    private $folder;
    private $publication;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $id = $_GET[PersonalCalendarManager :: PARAM_CALENDAR_EVENT_ID];

        if ($id)
        {
            $event = $this->retrieve_calendar_event_publication($id);

            $trail = new BreadcrumbTrail();
            $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => PersonalCalendarManager :: ACTION_BROWSE_CALENDAR)), Translation :: get('PersonalCalendar')));
            $trail->add(new Breadcrumb($this->get_url(array(PersonalCalendarManager :: PARAM_CALENDAR_EVENT_ID => $id)), $event->get_publication_object()->get_title()));

            $action_bar = $this->get_action_bar();
            $output = $this->get_publication_as_html($event, $action_bar);

            $this->display_header($trail);
            echo '<br /><a name="top"></a>';
            echo $action_bar->as_html() . '<br />';
            echo '<div id="action_bar_browser">';
            echo $output;
            echo '</div>';

            $this->display_footer();
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoProfileSelected')));
        }
    }

    function get_publication_as_html($event, $action_bar)
    {
        $learning_object = $event->get_publication_object();
        $display = LearningObjectDisplay :: factory($learning_object);
        $html = array();

        $time = isset ($_GET['time']) ? intval($_GET['time']) : time();
        $view = isset ($_GET['view']) ? $_GET['view'] : 'month';
        $this->set_parameter('time', $time);
        $this->set_parameter('view', $view);
        $this->set_parameter(Application :: PARAM_ACTION, PersonalCalendarManager :: ACTION_BROWSE_CALENDAR);

        $minimonthcalendar = new PersonalCalendarMiniMonthRenderer($this, $time);
        $html[] = '<div style="float: left; width: 19%; padding-right: 1%;">';
        $html[] =   $minimonthcalendar->render();
        $html[] =   '</div>';
        $html[] =   '<div style="float: left; width: 80%;">';

        $html[] = $display->get_full_html();

        $back = $this->get_url();
        $edit_url = $this->get_publication_editing_url($event);
        $delete_url = $this->get_publication_deleting_url($event);

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Back'), Theme :: get_common_image_path().'action_prev.png', $back));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path().'action_edit.png', $edit_url));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path().'action_delete.png', $delete_url));

        $html[] =   '</div>';

        return implode("\n",$html);
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        if (PlatformSetting :: get('allow_personal_agenda', 'user') && !Request :: get('calendar_event'))
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path().'action_publish.png', $this->get_url(array(Application :: PARAM_ACTION => PersonalCalendarManager :: ACTION_CREATE_PUBLICATION))));
        }

        $action_bar->add_tool_action(new ToolbarItem(Translation :: get('ListView'), Theme :: get_image_path().'tool_calendar_down.png', $this->get_url(array (Application :: PARAM_ACTION => PersonalCalendarManager :: ACTION_BROWSE_CALENDAR, 'view' => 'list'))));
        $action_bar->add_tool_action(new ToolbarItem(Translation :: get('MonthView'), Theme :: get_image_path().'tool_calendar_month.png', $this->get_url(array (Application :: PARAM_ACTION => PersonalCalendarManager :: ACTION_BROWSE_CALENDAR, 'view' => 'month'))));
        $action_bar->add_tool_action(new ToolbarItem(Translation :: get('WeekView'), Theme :: get_image_path().'tool_calendar_week.png', $this->get_url(array (Application :: PARAM_ACTION => PersonalCalendarManager :: ACTION_BROWSE_CALENDAR, 'view' => 'week'))));
        $action_bar->add_tool_action(new ToolbarItem(Translation :: get('DayView'), Theme :: get_image_path().'tool_calendar_day.png', $this->get_url(array (Application :: PARAM_ACTION => PersonalCalendarManager :: ACTION_BROWSE_CALENDAR, 'view' => 'day'))));
        $action_bar->add_tool_action(new ToolbarItem(Translation :: get('Today'), Theme :: get_image_path().'tool_calendar_today.png', $this->get_url(array (Application :: PARAM_ACTION => PersonalCalendarManager :: ACTION_BROWSE_CALENDAR, 'view' => (isset ($_GET['view']) ? $_GET['view'] : 'month'), 'time' => time()))));

        $action_bar->set_help_action(HelpManager :: get_tool_bar_help_item('personal calendar'));
        return $action_bar;
    }
}
?>