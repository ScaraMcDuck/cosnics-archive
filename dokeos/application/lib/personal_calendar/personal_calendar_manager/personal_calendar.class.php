<?php
/**
 * @package application.personal_calendar
 */
require_once dirname(__FILE__).'/../../webapplication.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/configuration.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/repositoryutilities.class.php';
require_once dirname(__FILE__).'/../renderer/personal_calendar_mini_month_renderer.class.php';
require_once dirname(__FILE__).'/../renderer/personal_calendar_list_renderer.class.php';
require_once dirname(__FILE__).'/../renderer/personal_calendar_month_renderer.class.php';
require_once dirname(__FILE__).'/../renderer/personal_calendar_week_renderer.class.php';
require_once dirname(__FILE__).'/../renderer/personal_calendar_day_renderer.class.php';
require_once dirname(__FILE__).'/../connector/personal_calendar_weblcms_connector.class.php';
require_once dirname(__FILE__).'/../publisher/personal_calendar_publisher.class.php';
/**
 * This application gives each user the possibility to maintain a personal
 * calendar.
 */
class PersonalCalendar extends WebApplication
{
	const APPLICATION_NAME = 'personal_calendar';

	/**
	 * The owner of this personal calendar
	 */
	private $user_id;
	/**
	 * Constructor
	 * @param int $user_id
	 */
	public function PersonalCalendar($user_id)
	{
		parent :: __construct();
		$this->user_id = $user_id;
	}
	/**
	 * Runs the personal calendar application
	 */
	public function run()
	{
		Display :: display_header(get_lang('MyAgenda'));
		api_display_tool_title(get_lang('MyAgenda'));
		if (isset ($_GET['publish']) && $_GET['publish'] == 1)
		{
			$_SESSION['personal_calendar_publish'] = true;
		}
		elseif (isset ($_GET['publish']) && $_GET['publish'] == 0)
		{
			$_SESSION['personal_calendar_publish'] = false;
		}
		if ($_SESSION['personal_calendar_publish'])
		{
			echo '<p><a href="'.$this->get_url(array ('publish' => 0), true).'"><img src="'.api_get_path(WEB_CODE_PATH).'/img/browser.gif" alt="'.get_lang('BrowserTitle').'" style="vertical-align:middle;"/> '.get_lang('BrowserTitle').'</a></p>';
			$publisher = new PersonalCalendarPublisher($this);
			echo $publisher->as_html();
		}
		else
		{
			echo '<p><a href="'.$this->get_url(array ('publish' => 1), true).'"><img src="'.api_get_path(WEB_CODE_PATH).'/img/publish.gif" alt="'.get_lang('Publish').'" style="vertical-align:middle;"/> '.get_lang('Publish').'</a></p>';
			$time = isset ($_GET['time']) ? intval($_GET['time']) : time();
			$view = isset ($_GET['view']) ? $_GET['view'] : 'month';
			$this->set_parameter('time', $time);
			$this->set_parameter('view', $view);
			$toolbar_data = array ();
			$toolbar_data[] = array ('href' => $this->get_url(array ('view' => 'list')), 'img' => api_get_path(WEB_CODE_PATH).'/img/calendar_down.gif', 'label' => get_lang('ListView'), 'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);
			$toolbar_data[] = array ('href' => $this->get_url(array ('view' => 'month')), 'img' => api_get_path(WEB_CODE_PATH).'/img/calendar_month.gif', 'label' => get_lang('MonthView'), 'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);
			$toolbar_data[] = array ('href' => $this->get_url(array ('view' => 'week')), 'img' => api_get_path(WEB_CODE_PATH).'/img/calendar_week.gif', 'label' => get_lang('WeekView'), 'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);
			$toolbar_data[] = array ('href' => $this->get_url(array ('view' => 'day')), 'img' => api_get_path(WEB_CODE_PATH).'/img/calendar_day.gif', 'label' => get_lang('DayView'), 'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);
			echo '<div style="margin-bottom: 1em;">'.RepositoryUtilities :: build_toolbar($toolbar_data).'</div>';
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
			$minimonthcalendar = new PersonalCalendarMiniMonthRenderer($this, $time);
			echo '<div style="float: left; width: 20%;">';
			echo $minimonthcalendar->render();
			echo '</div>';
			echo '<div style="float: left; width: 80%;">';
			echo $renderer->render();
			echo '</div>';
		}
		Display :: display_footer();
	}
	/**
	 * Gets the events
	 * @param int $from_date
	 * @param int $to_date
	 */
	public function get_events($from_date, $to_date)
	{
		$connector = new PersonalCalendarWeblcmsConnector();
		return $connector->get_events($this->user_id, $from_date, $to_date);
	}
	public function learning_object_is_published($object_id)
	{
		return false;
	}
	public function any_learning_object_is_published($object_ids)
	{
		return false;
	}
	public function get_learning_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return array ();
	}
	public function get_learning_object_publication_attribute($object_id)
	{
		return null;
	}
	public function count_publication_attributes($type = null, $condition = null)
	{
		return 0;
	}
	public function delete_learning_object_publications($object_id)
	{
		return 0;
	}
	public function update_learning_object_publication_id($publication_attr)
	{
		return 0;
	}

	public function get_application_platform_admin_links()
	{
		$links = array ();
		$links[] = array ('name' => get_lang('NoOptionsAvailable'), action => 'empty', 'url' => $this->get_link());
		return array ('application' => array ('name' => self :: APPLICATION_NAME, 'class' => self :: APPLICATION_NAME), 'links' => $links);
	}

	public function get_link($parameters = array (), $encode = false)
	{
		$link = 'index_'.self :: APPLICATION_NAME.'.php';
		if (count($parameters))
		{
			$link .= '?'.http_build_query($parameters);
		}
		if ($encode)
		{
			$link = htmlentities($link);
		}
		return $link;
	}
}
?>