<?php
/**
 * @package user.usermanager
 */
require_once dirname(__FILE__).'/rights_manager_component.class.php';
require_once dirname(__FILE__).'/../rights_data_manager.class.php';
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/../../../common/condition/or_condition.class.php';
require_once dirname(__FILE__).'/../../../common/condition/and_condition.class.php';
require_once dirname(__FILE__).'/../../../common/condition/equality_condition.class.php';
require_once dirname(__FILE__).'/../../../common/condition/pattern_match_condition.class.php';

/**
 * A user manager provides some functionalities to the admin to manage
 * his users. For each functionality a component is available.
 */
 class RightsManager {
 	
 	const APPLICATION_NAME = 'rights';
 	
 	const PARAM_ACTION = 'go';
	const PARAM_MESSAGE = 'message';
	const PARAM_ERROR_MESSAGE = 'error_message';
	const PARAM_REMOVE_SELECTED = 'delete';
	const PARAM_FIRSTLETTER = 'firstletter';
	const PARAM_COMPONENT_ACTION = 'action';
	const PARAM_APPLICATION = 'application';
	
	const ACTION_EDIT_RIGHTS = 'edit';
	
	const VIEW_RIGHT = '1';
	const EDIT_RIGHT = '2';
	const ADD_RIGHT = '3';
	const DELETE_RIGHT = '4';
	
	const ANONYMOUS_VISITOR_ROLE = '1';
	const REGISTERED_ROLE = '2';
	const ADDER_ROLE = '3';
	const EDITOR_ROLE = '4';
	const DELETER_ROLE = '5';
	const ADMIN_ROLE = '6';
	
	private $parameters;
	private $search_parameters;
	private $user_id;
	private $user;
	private $user_search_form;
	private $group_search_form;
	private $category_menu;
	private $quota_url;
	private $publication_url;
	private $create_url;
	private $recycle_bin_url;
	private $breadcrumbs;
	
	
    function RightsManager($user = null) {
    	$this->user = $user;
		$this->parameters = array ();
		$this->set_action($_GET[self :: PARAM_ACTION]);
		//$this->create_url = $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_CREATE_USER));   	
    }
    
    /**
	 * Run this user manager
	 */
	function run()
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
			case self :: ACTION_EDIT_RIGHTS :
				$component = RightsManagerComponent :: factory('Editor', $this);
				break;
			default :
				$this->set_action(self :: ACTION_EDIT_RIGHTS);
				$component = RightsManagerComponent :: factory('Editor', $this);
		}
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
	 * Displays the header.
	 * @param array $breadcrumbs Breadcrumbs to show in the header.
	 * @param boolean $display_search Should the header include a search form or
	 * not?
	 */
	function display_header($breadcrumbtrail = array (), $display_search = false)
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
		echo '</div>';
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
	
	function retrieve_roles($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return RightsDataManager :: get_instance()->retrieve_roles($condition, $offset, $count, $order_property, $order_direction);
	}
	
	function retrieve_rights($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return RightsDataManager :: get_instance()->retrieve_rights($condition, $offset, $count, $order_property, $order_direction);
	}
	
	function retrieve_locations($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return RightsDataManager :: get_instance()->retrieve_locations($condition, $offset, $count, $order_property, $order_direction);
	}
	
	function retrieve_role($id)
	{
		return RightsDataManager :: get_instance()->retrieve_role($id);
	}
	
	function retrieve_location($id)
	{
		return RightsDataManager :: get_instance()->retrieve_location($id);
	}
	
	function retrieve_right($id)
	{
		return RightsDataManager :: get_instance()->retrieve_right($id);
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
	 * Sets the active URL in the navigation menu.
	 * @param string $url The active URL.
	 */
	function force_menu_url($url)
	{
		//$this->get_category_menu()->forceCurrentUrl($url);
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
	 * Gets the user id.
	 * @return int The requested user id.
	 */
	function get_user_id()
	{
		return $this->user->get_id();
	}
	
	function get_user()
	{
		return $this->user;
	}
	
	/**
	 * Gets the URL to the Dokeos claroline folder.
	 */
	function get_path($path_type)
	{
		return PAth :: get_path($path_type);
	}
	/**
	 * Wrapper for Display :: display_not_allowed().
	 */
	function not_allowed()
	{
		Display :: display_not_allowed();
	}
	
	public function get_application_platform_admin_links()
	{
		$links = array();
		$links[] = array('name' => Translation :: get('Edit'), 'action' => 'manage', 'url' => $this->get_link(array(RightsManager :: PARAM_ACTION => RightsManager :: ACTION_EDIT_RIGHTS)));
		return array('application' => array('name' => Translation :: get('Rights'), 'class' => 'rights'), 'links' => $links, 'search' => null);
	}
	
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
	
	function get_location_id_from_short_string($location)
	{
		$location = 'platform|' . $location;
		$rdm = RightsDataManager :: get_instance();
		return $rdm->retrieve_location_id_from_location_string($location);
	}
	
	function is_allowed($right, $role_id, $location_id)
	{
		$rdm = RightsDataManager :: get_instance();
		$rolerightlocation = $rdm->retrieve_role_right_location($right, $role_id, $location_id);
		return $rolerightlocation->get_value();
	}
	
	function retrieve_role_right_location($right_id, $role_id, $location_id)
	{
		return RightsDataManager :: get_instance()->retrieve_role_right_location($right_id, $role_id, $location_id);
	}
}
?>