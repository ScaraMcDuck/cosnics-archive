<?php
/**
 * $Id$
 * @package application.personal_calendar
 */
require_once dirname(__FILE__).'/../../webapplication.class.php';
require_once Path :: get_library_path().'configuration/configuration.class.php';
require_once Path :: get_repository_path(). 'lib/repositoryutilities.class.php';
require_once dirname(__FILE__).'/personalcalendarcomponent.class.php';
//require_once dirname(__FILE__).'/../renderer/personal_calendar_mini_month_renderer.class.php';
//require_once dirname(__FILE__).'/../renderer/personal_calendar_list_renderer.class.php';
//require_once dirname(__FILE__).'/../renderer/personal_calendar_month_renderer.class.php';
//require_once dirname(__FILE__).'/../renderer/personal_calendar_week_renderer.class.php';
//require_once dirname(__FILE__).'/../renderer/personal_calendar_day_renderer.class.php';
require_once dirname(__FILE__).'/../connector/personal_calendar_weblcms_connector.class.php';
//require_once dirname(__FILE__).'/../publisher/personalcalendarpublisher.class.php';
require_once dirname(__FILE__).'/../personalcalendarevent.class.php';
require_once dirname(__FILE__).'/../personalcalendardatamanager.class.php';
/**
 * This application gives each user the possibility to maintain a personal
 * calendar.
 */
class PersonalCalendar extends WebApplication
{
	// TODO: Rewrite Personal Calendar app to standard application layout
	const APPLICATION_NAME = 'personal_calendar';
	
	const PARAM_ACTION = 'go';
	const PARAM_CALENDAR_EVENT_ID = 'calendar_event';
	
	const ACTION_BROWSE_CALENDAR = 'browse';
	const ACTION_VIEW_PUBLICATION = 'view';
	const ACTION_CREATE_PUBLICATION = 'publish';
	const ACTION_DELETE_PUBLICATION = 'delete';
	
	const ACTION_RENDER_BLOCK = 'block';

	/**
	 * The owner of this personal calendar
	 */
	private $user;
	
	private $parameters;
	
	/**
	 * Constructor
	 * @param int $user_id
	 */
	public function PersonalCalendar($user)
	{
    	$this->user = $user;
		$this->parameters = array ();
		$this->set_action($_GET[self :: PARAM_ACTION]);
	}
	/**
	 * Runs the personal calendar application
	 */
	public function run()
	{
		/*
		 * Only setting breadcrumbs here. Some stuff still calls
		 * forceCurrentUrl(), but that should not affect the breadcrumbs.
		 */
		//$this->breadcrumbs = $this->get_category_menu()->get_breadcrumbs();
		$action = $this->get_action();
		$component = null;
		switch ($action)
		{
			case self :: ACTION_BROWSE_CALENDAR :
				$component = PersonalCalendarComponent :: factory('Browser', $this);
				break;
			case self :: ACTION_VIEW_PUBLICATION :
				$component = PersonalCalendarComponent :: factory('Viewer', $this);
				break;
			case self :: ACTION_CREATE_PUBLICATION :
				$component = PersonalCalendarComponent :: factory('Publisher', $this);
				break;
			case self :: ACTION_DELETE_PUBLICATION :
				$component = PersonalCalendarComponent :: factory('Deleter', $this);
				break;
			default :
				$this->set_action(self :: ACTION_BROWSE_CALENDAR);
				$component = PersonalCalendarComponent :: factory('Browser', $this);
		}
		$component->run();
		
//		if (isset ($_GET['publish']) && $_GET['publish'] == 1)
//		{
//			$_SESSION['personal_calendar_publish'] = true;
//		}
//		elseif (isset ($_GET['publish']) && $_GET['publish'] == 0)
//		{
//			$_SESSION['personal_calendar_publish'] = false;
//		}
//		if ($_SESSION['personal_calendar_publish'])
//		{
//			$out = '<p><a href="'.$this->get_url(array ('publish' => 0), true).'"><img src="'.$this->get_path(WEB_IMG_PATH).'browser.gif" alt="'.Translation :: get('BrowserTitle').'" style="vertical-align:middle;"/> '.Translation :: get('BrowserTitle').'</a></p>';
//			$publisher = new PersonalCalendarPublisher($this);
//			$out .=  $publisher->as_html();
//		}
//		else
//		{
//			$out =  '<p><a href="'.$this->get_url(array ('publish' => 1), true).'"><img src="'.$this->get_path(WEB_IMG_PATH).'publish.gif" alt="'.Translation :: get('Publish').'" style="vertical-align:middle;"/> '.Translation :: get('Publish').'</a></p>';
//			$time = isset ($_GET['time']) ? intval($_GET['time']) : time();
//			$view = isset ($_GET['view']) ? $_GET['view'] : 'month';
//			$this->set_parameter('time', $time);
//			$this->set_parameter('view', $view);
//			$toolbar_data = array ();
//			$toolbar_data[] = array ('href' => $this->get_url(array ('view' => 'list')), 'img' => $this->get_path(WEB_IMG_PATH).'calendar_down.gif', 'label' => Translation :: get('ListView'), 'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);
//			$toolbar_data[] = array ('href' => $this->get_url(array ('view' => 'month')), 'img' => $this->get_path(WEB_IMG_PATH).'calendar_month.gif', 'label' => Translation :: get('MonthView'), 'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);
//			$toolbar_data[] = array ('href' => $this->get_url(array ('view' => 'week')), 'img' => $this->get_path(WEB_IMG_PATH).'calendar_week.gif', 'label' => Translation :: get('WeekView'), 'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);
//			$toolbar_data[] = array ('href' => $this->get_url(array ('view' => 'day')), 'img' => $this->get_path(WEB_IMG_PATH).'calendar_day.gif', 'label' => Translation :: get('DayView'), 'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);
//			$out .=  '<div style="margin-bottom: 1em;">'.RepositoryUtilities :: build_toolbar($toolbar_data).'</div>';
//			$minimonthcalendar = new PersonalCalendarMiniMonthRenderer($this, $time);
//			$out .=   '<div style="float: left; width: 20%;">';
//			$out .=   $minimonthcalendar->render();
//			$out .=   '</div>';
//			$out .=   '<div style="float: left; width: 80%;">';
//			$show_calendar = true;
//			if(isset($_GET['pid']))
//			{
//				$pid = $_GET['pid'];
//				$event = PersonalCalendarEvent::load($pid);
//				if(isset($_GET['action']) && $_GET['action'] == 'delete')
//				{
//					$event->delete();
//					$out .= Display::display_normal_message(Translation :: get('LearningObjectPublicationDeleted'),true);
//				}
//				else
//				{
//					$show_calendar = false;
//					$learning_object = $event->get_event();
//					$display = LearningObjectDisplay :: factory($learning_object);
//					$out .= '<h3>'.$learning_object->get_title().'</h3>';
//					$out  .= $display->get_full_html();
//					$toolbar_data = array();
//					$toolbar_data[] = array(
//						'href' => $this->get_url(),
//						'label' => Translation :: get('Back'),
//						'img' => $this->get_path(WEB_IMG_PATH).'prev.png',
//						'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
//					);
//					$toolbar_data[] = array(
//						'href' => $this->get_url(array('action'=>'delete','pid'=>$pid)),
//						'label' => Translation :: get('Delete'),
//						'img' => $this->get_path(WEB_IMG_PATH).'delete.gif',
//						'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
//					);
//					$out .= RepositoryUtilities :: build_toolbar($toolbar_data, array(), 'margin-top: 1em;');
//				}
//			}
//			if($show_calendar)
//			{
//				switch ($view)
//				{
//					case 'list' :
//						$renderer = new PersonalCalendarListRenderer($this, $time);
//						break;
//					case 'day' :
//						$renderer = new PersonalCalendarDayRenderer($this, $time);
//						break;
//					case 'week' :
//						$renderer = new PersonalCalendarWeekRenderer($this, $time);
//						break;
//					default :
//						$renderer = new PersonalCalendarMonthRenderer($this, $time);
//						break;
//				}
//				$out .=   $renderer->render();
//			}
//			$out .=   '</div>';
//		}
//		$trail = new BreadcrumbTrail();
//		$trail->add(new Breadcrumb(null, Translation :: get('MyAgenda')));
//		
//		Display :: display_header($trail);
//		Display :: display_tool_title(Translation :: get('MyAgenda'));
//		echo $out;
//		Display :: display_footer();
	}
	
    /**
	 * Renders the calendar block and returns it. 
	 */
	function render_block($type, $block_info)
	{
		/*
		 * Only setting breadcrumbs here. Some stuff still calls
		 * forceCurrentUrl(), but that should not affect the breadcrumbs.
		 */
		//$this->breadcrumbs = $this->get_category_menu()->get_breadcrumbs();
		$action = $this->get_action();
		$component = null;
		switch ($action)
		{
			case self :: ACTION_RENDER_BLOCK :
				$component = PersonalCalendarComponent :: factory('Blocker', $this);
				break;
			default :
				$this->set_action(self :: ACTION_RENDER_BLOCK);
				$component = PersonalCalendarComponent :: factory('Blocker', $this);
		}
		return $component->render_block($type, $block_info);
	}
	
	/**
	 * Gets the events
	 * @param int $from_date
	 * @param int $to_date
	 */
	public function get_events($from_date, $to_date)
	{
		$events = array();
		
		$dm = PersonalCalendarDatamanager::get_instance();
		$condition = new EqualityCondition(CalendarEventPublication :: PROPERTY_PUBLISHER, $this->get_user_id());
		$publications = $dm->retrieve_calendar_event_publications($condition);
		while($publication = $publications->next_result())
		{
			$object = $publication->get_publication_object();
			
			if($object->get_start_date() >= $from_date && $object->get_start_date() <= $to_date)
			{
				$event = new PersonalCalendarEvent();
				$event->set_start_date($object->get_start_date());
				$event->set_end_date($object->get_end_date());
				$event->set_url($this->get_publication_viewing_url($publication));
				$event->set_title($object->get_title());
				$event->set_content($object->get_description());
				$event->set_source(self :: APPLICATION_NAME);
				
				$events[] = $event;
			}
		}
		$connector = new PersonalCalendarWeblcmsConnector();
		$events = array_merge($events,$connector->get_events($this->user, $from_date, $to_date));
		return $events;
	}
	/**
	 * @see Application::learning_object_is_published()
	 */
	public function learning_object_is_published($object_id)
	{
		$dm = PersonalCalendarDatamanager::get_instance();
		return $dm->learning_object_is_published($object_id);
	}
	/**
	 * @see Application::any_learning_object_is_published()
	 */
	public function any_learning_object_is_published($object_ids)
	{
		$dm = PersonalCalendarDatamanager::get_instance();
		return $dm->any_learning_object_is_published($object_ids);
	}
	/**
	 * @see Application::get_learning_object_publication_attributes()
	 */
	public function get_learning_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		$dm = PersonalCalendarDatamanager::get_instance();
		return $dm->get_learning_object_publication_attributes($object_id, $type , $offset , $count , $order_property , $order_direction );
	}
	/**
	 * @see Application::get_learning_object_publication_attribute()
	 */
	public function get_learning_object_publication_attribute($publication_id)
	{
		$dm = PersonalCalendarDatamanager::get_instance();
		return $dm->get_learning_object_publication_attribute($publication_id);
	}
	/**
	 * @see Application::count_publication_attributes()
	 */
	public function count_publication_attributes($type = null, $condition = null)
	{
		$dm = PersonalCalendarDatamanager::get_instance();
		return $dm->count_publication_attributes($type, $condition );
	}
	/**
	 * @see Application::delete_learning_object_publications()
	 */
	public function delete_learning_object_publications($object_id)
	{
		$dm = PersonalCalendarDatamanager::get_instance();
		return $dm->delete_learning_object_publications($object_id);
	}
	/**
	 * @see Application::update_learning_object_publication_id()
	 */
	public function update_learning_object_publication_id($publication_attr)
	{
		return PersonalCalendarDatamanager :: get_instance()->update_learning_object_publication_id($publication_attr);
	}
	/**
	 * @see Application::get_application_platform_admin_links()
	 */
	public function get_application_platform_admin_links()
	{
		$links = array ();
		$links[] = array ('name' => Translation :: get('NoOptionsAvailable'), action => 'empty', 'url' => $this->get_link());
		return array ('application' => array ('name' => self :: APPLICATION_NAME, 'class' => self :: APPLICATION_NAME), 'links' => $links);
	}
	/**
	 * Gets a link to the personal calendar application
	 * @param array $parameters
	 * @param boolean $encode
	 */
	public function get_link($parameters = array (), $encode = false)
	{
		$link = 'run.php';
		$parameters['application'] = self::APPLICATION_NAME;
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
	
	/**
	 * Gets an URL.
	 * @param array $additional_parameters Additional parameters to add in the
	 * query string (default = no additional parameters).
	 * @param boolean $include_search Include the search parameters in the
	 * query string of the URL? (default = false).
	 * @param boolean $encode_entities Apply php function htmlentities to the
	 * resulting URL ? (default = false).
	 * @return string The requested URL.
	 */
	function get_url($additional_parameters = array (), $include_search = false, $encode_entities = false, $x = null)
	{
		$eventual_parameters = array_merge($this->get_parameters($include_search), $additional_parameters);
		$url = $_SERVER['PHP_SELF'].'?'.http_build_query($eventual_parameters);
		if ($encode_entities)
		{
			$url = htmlentities($url);
		}

		return $url;
	}
	
	/**
	 * Gets the user id of this personal calendars owner
	 * @return int
	 */
	function get_user_id()
	{
		return $this->user->get_user_id();
	}
	
	/**
	 * Gets the user.
	 * @return int The requested user.
	 */
	function get_user()
	{
		return $this->user;
	}
	
	function get_platform_setting($variable, $application = self :: APPLICATION_NAME)
	{
		$adm = AdminDataManager :: get_instance();
		return $adm->retrieve_setting_from_variable_name($variable, $application);
	}
	
	function get_path($path_type)
	{
		return Path :: get($path_type);
	}
	
	function get_action()
	{
		return $this->get_parameter(self :: PARAM_ACTION);
	}
	/**
	 * Sets the current action.
	 * @param string $action The new action.
	 */
	function set_action($action)
	{
		return $this->set_parameter(self :: PARAM_ACTION, $action);
	}
	
	/**
	 * Displays the header.
	 * @param array $breadcrumbs Breadcrumbs to show in the header.
	 * @param boolean $display_search Should the header include a search form or
	 * not?
	 */
	function display_header($breadcrumbtrail)
	{
		if (is_null($breadcrumbtrail))
		{
			$breadcrumbtrail = new BreadcrumbTrail();
		}
		
		$categories = $this->breadcrumbs;
		if (count($categories) > 0)
		{
			foreach($categories as $category)
			{
				$breadcrumbtrail->add(new Breadcrumb($category['url'], $category['title']));
			}
		}
		
		$title = $breadcrumbtrail->get_last()->get_name();
		$title_short = $title;
		if (strlen($title_short) > 53)
		{
			$title_short = substr($title_short, 0, 50).'&hellip;';
		}
		Display :: display_header($breadcrumbtrail);
		echo '<h3 style="float: left;" title="'.$title.'">'.$title_short.'</h3>';
		echo '<div class="clear">&nbsp;</div>';

		if ($msg = $_GET[self :: PARAM_MESSAGE])
		{
			$this->display_message($msg);
		}
		if($msg = $_GET[self::PARAM_ERROR_MESSAGE])
		{
			$this->display_error_message($msg);
		}
	}
	
	/**
	 * Displays the footer.
	 */
	function display_footer()
	{
		echo '<div class="clear">&nbsp;</div>';
		Display :: display_footer();
	}

	/**
	 * Displays a normal message.
	 * @param string $message The message.
	 */
	function display_message($message)
	{
		Display :: display_normal_message($message);
	}
	/**
	 * Displays an error message.
	 * @param string $message The message.
	 */
	function display_error_message($message)
	{
		Display :: display_error_message($message);
	}
	/**
	 * Displays a warning message.
	 * @param string $message The message.
	 */
	function display_warning_message($message)
	{
		Display :: display_warning_message($message);
	}
	/**
	 * Displays an error page.
	 * @param string $message The message.
	 */
	function display_error_page($message)
	{
		$this->display_header();
		$this->display_error_message($message);
		$this->display_footer();
	}

	/**
	 * Displays a warning page.
	 * @param string $message The message.
	 */
	function display_warning_page($message)
	{
		$this->display_header();
		$this->display_warning_message($message);
		$this->display_footer();
	}

	/**
	 * Displays a popup form.
	 * @param string $message The message.
	 */
	function display_popup_form($form_html)
	{
		Display :: display_normal_message($form_html);
	}
	
	/**
	 * Gets the parameter list
	 * @param boolean $include_search Include the search parameters in the
	 * returned list?
	 * @return array The list of parameters.
	 */
	function get_parameters($include_search = false)
	{
		if ($include_search && isset ($this->search_parameters))
		{
			return array_merge($this->search_parameters, $this->parameters);
		}
		
		return $this->parameters;
	}
	/**
	 * Gets the value of a parameter.
	 * @param string $name The parameter name.
	 * @return string The parameter value.
	 */
	function get_parameter($name)
	{
		return $this->parameters[$name];
	}
	/**
	 * Sets the value of a parameter.
	 * @param string $name The parameter name.
	 * @param mixed $value The parameter value.
	 */
	function set_parameter($name, $value)
	{
		$this->parameters[$name] = $value;
	}
	
	/**
	 * Redirect the end user to another location.
	 * @param string $action The action to take (default = browse learning
	 * objects).
	 * @param string $message The message to show (default = no message).
	 * @param int $new_category_id The category to show (default = root
	 * category).
	 * @param boolean $error_message Is the passed message an error message?
	 */
	function redirect($action = null, $message = null, $error_message = false, $extra_params = array())
	{
		return parent :: redirect($action, $message, $error_message, $extra_params);
	}
	
	function retrieve_calendar_event_publication($publication_id)
	{
		$pcdm = PersonalCalendarDataManager :: get_instance();
		return $pcdm->retrieve_calendar_event_publication($publication_id);
	}
	
	function get_publication_deleting_url($publication)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_DELETE_PUBLICATION, self :: PARAM_CALENDAR_EVENT_ID => $publication->get_id()));
	}
	
	function get_publication_viewing_url($publication)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_VIEW_PUBLICATION, self :: PARAM_CALENDAR_EVENT_ID => $publication->get_id()));
	}
}
?>