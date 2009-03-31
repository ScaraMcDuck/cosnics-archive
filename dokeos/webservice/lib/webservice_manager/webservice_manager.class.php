<?php

require_once dirname(__FILE__).'/webservice_manager_component.class.php';
require_once dirname(__FILE__).'/../webservice_rights.class.php';
require_once dirname(__FILE__).'/../webservice_data_manager.class.php';
require_once dirname(__FILE__).'/../../../common/html/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/../../../common/condition/or_condition.class.php';
require_once dirname(__FILE__).'/../../../common/condition/and_condition.class.php';
require_once dirname(__FILE__).'/../../../common/condition/equality_condition.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table.class.php';

/**
 * A webservice manager provides some functionalities to the admin to manage
 * his webservices.
 */
 class WebserviceManager {
 	
 	const APPLICATION_NAME = 'webservice';
 	
 	const PARAM_ACTION = 'go';
	const PARAM_MESSAGE = 'message';
	const PARAM_ERROR_MESSAGE = 'error_message';
	const PARAM_REMOVE_SELECTED = 'delete';
	const PARAM_FIRSTLETTER = 'firstletter';
	const PARAM_COMPONENT_ACTION = 'action';
    const PARAM_APPLICATION = 'application';

    const PARAM_LOCATION_ID = 'location';
	const PARAM_WEBSERVICE_ID = 'webservice';
	const PARAM_WEBSERVICE_CATEGORY_ID = 'webservice_category_id';
	
	const ACTION_BROWSE_WEBSERVICES = 'browse_webservices';
	const ACTION_BROWSE_WEBSERVICE_CATEGORIES = 'browse_webservice_categories';
    const ACTION_MANAGE_ROLES = 'rights_editor';
	
	private $parameters;
	private $search_parameters;
	private $user;
	private $breadcrumbs;
    private $instance;
		
    public function WebserviceManager($user = null)
    {
    	$this->user = $user;
		$this->parameters = array ();
		$this->set_action($_GET[self :: PARAM_ACTION]);			   	
    }

    /**
	 * Run this webservice manager
	 */
	function run()
	{		
		$action = $this->get_action();
		
		$component = null;
		switch ($action)
		{
			case self :: ACTION_BROWSE_WEBSERVICES :								
				$component = WebserviceManagerComponent :: factory('WebserviceBrowser', $this);			
				break;
            case self :: ACTION_MANAGE_ROLES :
				$component = WebserviceManagerComponent :: factory('RightsEditor', $this);
				break;
			default :				
				$component = WebserviceManagerComponent :: factory('WebserviceBrowser', $this);		
		}									
		$component->run(); //wordt gestart
		
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
	function display_header($breadcrumbtrail, $display_search = false)
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
		Display :: header($breadcrumbtrail);
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
		echo '</div>';
		echo '<div class="clear">&nbsp;</div>';
		Display :: footer();
	}
	
	/**
	 * Displays a normal message.
	 * @param string $message The message.
	 */
	function display_message($message)
	{
		Display :: normal_message($message);
	}
	/**
	 * Displays an error message.
	 * @param string $message The message.
	 */
	function display_error_message($message)
	{
		Display :: error_message($message);
	}
	/**
	 * Displays a warning message.
	 * @param string $message The message.
	 */
	function display_warning_message($message)
	{
		Display :: warning_message($message);
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
		Display :: normal_message($form_html);
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
	
	function retrieve_webservices($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return WebserviceDataManager :: get_instance()->retrieve_webservices($condition, $offset, $count, $order_property, $order_direction);
	}
	
 	function count_webservices($condition = null)
	{
		return WebserviceDataManager :: get_instance()->count_webservices($condition);
	}
	
	function retrieve_webservice_categories($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return WebserviceDataManager :: get_instance()->retrieve_webservice_categories($condition, $offset, $count, $order_property, $order_direction);
	}
	
	function retrieve_webservice($id)
	{
		return WebserviceDataManager :: get_instance()->retrieve_webservice($id);
	}

    function retrieve_webservice_by_name($name)
	{
		return WebserviceDataManager :: get_instance()->retrieve_webservice_by_name($name);
	}
	
	/*function retrieve_webservice_category($id)
	{
		return WebserviceDataManager :: get_instance()->retrieve_webservice_category($id);
	}*/
	
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
	 * Wrapper for Display :: not_allowed().
	 */
	function not_allowed()
	{
		Display :: not_allowed();
	}
	
	public function get_application_platform_admin_links()
	{
		$links		= array();
		$links[]	= array('name' => Translation :: get('List'),
							'description' => Translation :: get('ListDescription'),
							'action' => 'list',
							'url' => $this->get_link(array(WebserviceManager :: PARAM_ACTION => WebserviceManager :: ACTION_BROWSE_WEBSERVICES)));
		return array('application' => array('name' => Translation :: get('Webservice'), 'class' => 'webservice'), 'links' => $links, 'search' => null);
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

    public function get_manage_roles_url($webservice)
	{ 
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_MANAGE_ROLES, self :: PARAM_WEBSERVICE_ID => $webservice->get_id()));
	}

    public function get_manage_roles_cat_url($webserviceCategory)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_MANAGE_ROLES, self :: PARAM_WEBSERVICE_CATEGORY_ID => $webserviceCategory->get_id()));
	}

    public static function get_tool_bar_item($id)
	{
        $wdm = new WebserviceManager();
		$user_id = Session :: get_user_id();
		$user = UserDataManager :: get_instance()->retrieve_user($user_id);

		if(!$user || !$user->get_language())
			$language = PlatformSetting :: get('platform_language');
		else
			$language = $user->get_language();

		$toolbar_item = WebserviceDataManager :: get_instance()->retrieve_webservice_category($id);
        if(isset($toolbar_item))
        {
            $url = $wdm->get_manage_roles_cat_url($toolbar_item);            
        }
        else
        {   
            $wsm = new WebserviceManager();
            $url = $wsm->get_url(array (self :: PARAM_ACTION => self :: ACTION_MANAGE_ROLES, self :: PARAM_WEBSERVICE_CATEGORY_ID => null));
        }
        return new ToolbarItem('Change rights ', Theme :: get_common_image_path().'action_rights.png', $url, ToolbarItem :: DISPLAY_ICON_AND_LABEL, false);
	}
	
	/*function is_allowed($right, $role_id, $location_id)
	{
		$rdm = RightsDataManager :: get_instance();
		$rolerightlocation = $rdm->retrieve_role_right_location($right, $role_id, $location_id);
		return $rolerightlocation->get_value();
	}
	
	function retrieve_role_right_location($right_id, $role_id, $location_id)
	{
		return RightsDataManager :: get_instance()->retrieve_role_right_location($right_id, $role_id, $location_id);
	}
	
	function get_role_deleting_url($role)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_DELETE_ROLES, self :: PARAM_ROLE_ID => $role->get_id()));
	}
	
	function get_role_editing_url($role)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_EDIT_ROLES, self :: PARAM_ROLE_ID => $role->get_id()));
	}*/
 }