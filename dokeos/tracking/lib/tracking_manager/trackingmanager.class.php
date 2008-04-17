<?php
/**
 * @package tracking.lib.trackingmanager
 */
require_once dirname(__FILE__).'/trackingmanagercomponent.class.php';
require_once dirname(__FILE__).'/../trackingdatamanager.class.php';
require_once dirname(__FILE__).'/../trackerregistration.class.php';
require_once dirname(__FILE__).'/../event.class.php';
require_once dirname(__FILE__).'/../eventreltracker.class.php';
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
require_once Path :: get_library_path().'condition/orcondition.class.php';
require_once Path :: get_library_path().'condition/andcondition.class.php';
require_once Path :: get_library_path().'condition/equalitycondition.class.php';
require_once Path :: get_library_path().'condition/likecondition.class.php';
require_once Path :: get_user_path().'lib/usersdatamanager.class.php';

/**
 * A tracking manager provides some functionalities to the admin to manage
 * his trackers and events. For each functionality a component is available.
 */
 class TrackingManager 
 {
	const APPLICATION_NAME = 'tracking';
	
	const PARAM_ACTION = 'go';
	const PARAM_MESSAGE = 'message';
	const PARAM_ERROR_MESSAGE = 'error_message';
	
	const PARAM_EVENT_ID = 'event_id';
	const PARAM_TRACKER_ID = 'track_id';
	const PARAM_REF_ID = 'ref_id';
	const PARAM_TYPE = 'type';
	
	const ACTION_BROWSE_EVENTS = 'browse_events';
	const ACTION_VIEW_EVENT = 'view_event';
	const ACTION_CHANGE_ACTIVE = 'changeactive';
	
	private $user;
	private $tdm;
	
	/**
	 * Constructor
	 * @param User $user The active user
	 */
    function TrackingManager($user) 
    {
		$this->set_action($_GET[self :: PARAM_ACTION]);
		$this->user = $user;
		$this->tdm = TrackingDataManager :: get_instance();
    }

    /**
	 * Run this tracking manager
	 */
	function run()
	{
		$action = $this->get_action();
		$component = null;
		switch ($action)
		{
			case self :: ACTION_BROWSE_EVENTS :
				$component = TrackingManagerComponent :: factory('AdminEventsBrowser', $this);
				break;
			case self :: ACTION_VIEW_EVENT :
				$component = TrackingManagerComponent :: factory('AdminEventViewer', $this);
				break;
			case self :: ACTION_CHANGE_ACTIVE :
				$component = TrackingManagerComponent :: factory('ActivityChanger', $this);
				break;
			default :
				$component = TrackingManagerComponent :: factory('AdminEventsBrowser', $this);
				break;
		}
		
		if($component)
			$component->run();
	}
	/**
	 * Gets the current action.
	 * @see get_parameter()
	 * @return string The current action.
	 */
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
	 * Displays the header.
	 * @param array $breadcrumbs Breadcrumbs to show in the header.
	 * @param boolean $display_search Should the header include a search form or
	 * not?
	 */
	function display_header($breadcrumbtrail, $display_search = false)
	{
		if (is_null($breadcrumbtrail))
		{ 
			$breadcrumbtrail = new BreadcrumbTrail();
		}
		
		$title = $breadcrumbtrail->get_last()->get_name();
		$title_short = $title;
		if (strlen($title_short) > 53)
		{
			$title_short = substr($title_short, 0, 50).'&hellip;';
		}
		Display :: display_header($breadcrumbtrail);
		echo '<h3 style="float: left;" title="'.$title.'">'.$title_short.'</h3>';
		if ($display_search)
		{
			$this->display_search_form();
		}
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
	 * Builds a link with the given parameters
	 * @param Array $parameters An array of parameters
	 * @param Bool $encode to encode the link (default false)
	 */
	public function get_link($parameters = array (), $encode = false)
	{
		$link = 'index_'. self :: APPLICATION_NAME .'.php';
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
	 * Builds an url with the given parameters and the existing parameters
	 * @param Array $additional_parameters Additional parameters that have to be added
	 * @param Bool $encode_entities to encode the url (default false)
	 */
	function get_url($additional_parameters = array (), $encode_entities = false)
	{
		$url = parse_url(Path :: get(WEB_PATH));
		$url = $url['scheme'].'://'.$url['host'];
		$eventual_parameters = array_merge($this->get_parameters(), $additional_parameters);
		$url .= $_SERVER['PHP_SELF'].'?'.http_build_query($eventual_parameters);
		if ($encode_entities)
		{
			$url .= htmlentities($url);
		}

		return $url;
	}
	
	/**
	 * Redirect the user to a given url
	 * @param String $type the type of redirection link (url or link - default url)
	 * @param String $message the message added to the url (default null)
	 * @param Bool $error_message wether to view the message as error message (default false);
	 * @param Array $extra_params Extra parameters to add to the url
	 */
	function redirect($type = 'url', $message = null, $error_message = false, $extra_params = null)
	{
		$params = array (); 
		if (isset ($message))
		{
			$params[$error_message ? self :: PARAM_ERROR_MESSAGE :  self :: PARAM_MESSAGE] = $message;
		}
		if (isset($extra_params))
		{
			foreach($extra_params as $key => $extra)
			{
				$params[$key] = $extra;
			}
		}
		if ($type == 'url')
		{
			$url = $this->get_url($params);
		}
		elseif ($type == 'link')
		{
			$url = 'index.php';
		}
		header('Location: '.$url);
	}
	
	/**
	 * Method used by the administrator module to get the application links
	 */
	public function get_application_platform_admin_links()
	{
		$links = array();
		$links[] = array('name' => Translation :: get('TrackersList'), 'action' => 'list', 'url' => $this->get_link(array(TrackingManager :: PARAM_ACTION => TrackingManager :: ACTION_BROWSE_EVENTS)));
		return array('application' => array('name' => Translation :: get('Tracking'), 'class' => 'tracking'), 'links' => $links);
	}
	
	/**
	 * Gets the parameters
	 * @return Array the parameters list
	 */
	function get_parameters()
	{
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
	 * Gets the active user
	 * @return User Active user
	 */
	function get_user()
	{
		return $this->user;
	}
	
	/**
	 * Gets the url for the event browser
	 * @return String URL for event browser
	 */
	function get_browser_url()
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_BROWSE_EVENTS));
	}
	
	/**
	 * Retrieves the change active url
	 * @param string $type event or tracker
	 * @param Object $object Event or Tracker Object
	 * @return the change active component url
	 */
	function get_change_active_url($type, $event_id, $tracker_id = null)
	{
		$parameters = array();
		$parameters[self :: PARAM_ACTION] = self :: ACTION_CHANGE_ACTIVE;
		$parameters[self :: PARAM_TYPE] = $type;
		$parameters[self :: PARAM_EVENT_ID] = $event_id;
		if($tracker_id)
			$parameters[self :: PARAM_TRACKER_ID] = $tracker_id;
		
		return $this->get_url($parameters);
	}
	
	/** 
	 * Retrieves the event viewer url
	 * @param Event $event
	 * @return the event viewer url for the given event
	 */
	function get_event_viewer_url($event)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_VIEW_EVENT, self :: PARAM_EVENT_ID => $event->get_id()));
	}
	
	/**
	 * Retrieves the events 
	 * @return the events
	 */
	function retrieve_events()
	{
		return $this->tdm->retrieve_events();
	}
	
	/**
	 * Retrieves an event by the given id
	 * @param int $event_id
	 * @return Event event
	 */
	function retrieve_event($event_id)
	{
		return $this->tdm->retrieve_event($event_id);
	}
	
	/**
	 * Retrieves the trackers from a given event
	 * @param int $event_id the event id
	 * @return array of trackers
	 */
	function retrieve_trackers_from_event($event_id)
	{
		return $this->tdm->retrieve_trackers_from_event($event_id, $false);
	}
	
	/**
	 * Retrieves the event tracker relation by given id's
	 * @param int $event_id the event id
	 * @param int $tracker_id the tracker id
	 * @return EventTrackerRelation
	 */
	function retrieve_event_tracker_relation($event_id, $tracker_id)
	{
		return $this->tdm->retrieve_event_tracker_relation($event_id, $tracker_id);
	}
	
}
?>