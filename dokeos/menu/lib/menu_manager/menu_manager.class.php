<?php
/**
 * @package user.usermanager
 */
require_once dirname(__FILE__).'/menu_manager_component.class.php';
require_once dirname(__FILE__).'/../menu_data_manager.class.php';
require_once dirname(__FILE__).'/../menu_item.class.php';
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/../../../common/condition/or_condition.class.php';
require_once dirname(__FILE__).'/../../../common/condition/and_condition.class.php';
require_once dirname(__FILE__).'/../../../common/condition/equality_condition.class.php';
require_once dirname(__FILE__).'/../../../common/condition/pattern_match_condition.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table.class.php';
require_once dirname(__FILE__).'/component/menu_item_browser/menu_item_browser_table.class.php';
/**
 * A user manager provides some functionalities to the admin to manage
 * his users. For each functionality a component is available.
 */
 class MenuManager {
 	
 	const APPLICATION_NAME = 'menu';
 	
 	const PARAM_ACTION = 'go';
	const PARAM_MESSAGE = 'message';
	const PARAM_ERROR_MESSAGE = 'error_message';
	const PARAM_COMPONENT_ACTION = 'action';
	const PARAM_DIRECTION = 'direction';
	const PARAM_CATEGORY = 'category';
	const PARAM_DELETE_SELECTED = 'delete_selected';
	
	const ACTION_RENDER_BAR = 'render_bar';
	const ACTION_RENDER_MINI_BAR = 'render_mini_bar';
	const ACTION_RENDER_TREE = 'render_tree';
	const ACTION_RENDER_SITEMAP = 'render_sitemap';	
	const ACTION_SORT_MENU = 'sort';
	
	const ACTION_COMPONENT_BROWSE_CATEGORY = 'browse';
	const ACTION_COMPONENT_ADD_CATEGORY = 'add';
	const ACTION_COMPONENT_EDIT_CATEGORY = 'edit';
	const ACTION_COMPONENT_DELETE_CATEGORY = 'delete';
	const ACTION_COMPONENT_MOVE_CATEGORY = 'move';
	
	private $parameters;
	private $user;
	private $breadcrumbs;
	
	
    function MenuManager($user) {
    	if (isset($user))
    	{
	    	$this->user = $user;
    	}
		$this->parameters = array ();
		$this->set_action(isset($_GET[self :: PARAM_ACTION]) ? $_GET[self :: PARAM_ACTION] : null);
		$this->parse_input_from_table();
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
			case self :: ACTION_SORT_MENU :
				$component = MenuManagerComponent :: factory('Sorter', $this);
				break;
			default :
				$this->set_action(self :: ACTION_SORT_MENU);
				$component = MenuManagerComponent :: factory('Sorter', $this);
		}
		$component->run();
	}
	
	function render_menu($type)
	{
		switch ($type)
		{
			case self :: ACTION_RENDER_BAR :
				$component = MenuManagerComponent :: factory('Bar', $this);
				break;
			case self :: ACTION_RENDER_MINI_BAR :
				$component = MenuManagerComponent :: factory('MiniBar', $this);
				break;
			case self :: ACTION_RENDER_TREE :
				$component = MenuManagerComponent :: factory('Tree', $this);
				break;
			case self :: ACTION_RENDER_SITEMAP :
				$component = MenuManagerComponent :: factory('Sitemap', $this);
				break;
			default :
				$this->set_action(self :: ACTION_RENDER_BAR);
				$component = MenuManagerComponent :: factory('Bar', $this);
		}
		return $component->run();
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
	
	function display_header($breadcrumbtrail = null, $display_search = false)
	{
		if (is_null($breadcrumbtrail) || !is_object($breadcrumbtrail))
		{
			$trail = new BreadcrumbTrail();
		}
		else
		{
			$trail = $breadcrumbtrail;
		}
		
		$title = $trail->get_last()->get_name();
		$title_short = $title;
		if (strlen($title_short) > 53)
		{
			$title_short = substr($title_short, 0, 50).'&hellip;';
		}
		Display :: header($trail);
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
	
	function count_menu_items($condition = null)
	{
		return MenuDataManager :: get_instance()->count_menu_items($condition);
	}
	
	function retrieve_menu_items($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return MenuDataManager :: get_instance()->retrieve_menu_items($condition, $offset, $count, $order_property, $order_direction);
	}
	
	function retrieve_menu_item($id)
	{
		return MenuDataManager :: get_instance()->retrieve_menu_item($id);
	}
	
	function retrieve_menu_item_at_sort($parent, $sort, $direction)
	{
		return MenuDataManager :: get_instance()->retrieve_menu_item_at_sort($parent, $sort, $direction);
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
		return Path :: get($path_type);
	}
	/**
	 * Wrapper for Display :: not_allowed().
	 */
	function not_allowed()
	{
		Display :: not_allowed();
	}
	
	function is_allowed($right, $locations = array())
	{
		$user = $this->get_user();
		
		if (is_object($user) && $user->is_platform_admin())
		{
			return true; 
		}
		
		if (count($locations))
		{
			$location_string = self :: APPLICATION_NAME . '|' . implode ('|', $locations);
		}
		else
		{
			$location_string =  self :: APPLICATION_NAME;
		}
		$location = RightsManager :: get_location_id_from_short_string($location_string);
		
		if (is_object($user))
		{
			if ($groupreluser = GroupsManager :: retrieve_group_rel_user($user->get_id(), $location->get_id()))
			{
				$grouprole = GroupsManager :: retrieve_group_role($groupreluser->get_group_id(), $location->get_id());
				$role_id = $grouprole->get_role_id();
			}
			else
			{
				$role_id = UserManager :: get_user_role_id($user, $location);
			}
		}
		else
		{
			$role_id = 1;
		}
		
		return RightsManager :: is_allowed($right, $role_id, $location->get_id());
	}
	
	public function get_application_platform_admin_links()
	{
		$links		= array();
		$links[]	= array('name' => Translation :: get('Manage'),
							'description' => Translation :: get('ManageDescription'),
							'action' => 'sort',
							'url' => $this->get_link(array(MenuManager :: PARAM_ACTION => MenuManager :: ACTION_SORT_MENU)));
		return array('application' => array('name' => Translation :: get('Menu'), 'class' => 'menu'), 'links' => $links, 'search' => null);
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
	
	function get_menu_item_creation_url()
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_SORT_MENU, self :: PARAM_COMPONENT_ACTION => self :: ACTION_COMPONENT_ADD_CATEGORY));
	}
	
	function get_menu_item_editing_url($menu_item)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_SORT_MENU, self :: PARAM_COMPONENT_ACTION => self :: ACTION_COMPONENT_EDIT_CATEGORY, self :: PARAM_CATEGORY => $menu_item->get_id()));
	}
	
	function get_menu_item_deleting_url($menu_item)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_SORT_MENU, self :: PARAM_COMPONENT_ACTION => self :: ACTION_COMPONENT_DELETE_CATEGORY, self :: PARAM_CATEGORY => $menu_item->get_id()));
	}
	
	function get_menu_item_moving_url($menu_item, $direction)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_SORT_MENU, self :: PARAM_COMPONENT_ACTION => self :: ACTION_COMPONENT_MOVE_CATEGORY, self :: PARAM_CATEGORY => $menu_item->get_id(), self :: PARAM_DIRECTION => $direction));
	}
	
	private function parse_input_from_table()
	{
		if (isset ($_POST['action']))
		{
			$selected_ids = $_POST[MenuItemBrowserTable :: DEFAULT_NAME.ObjectTable :: CHECKBOX_NAME_SUFFIX];
			if (empty ($selected_ids))
			{
				$selected_ids = array ();
			}
			elseif (!is_array($selected_ids))
			{
				$selected_ids = array ($selected_ids);
			}
			
			switch ($_POST['action'])
			{
				case self :: PARAM_DELETE_SELECTED :
					$this->set_action(self :: ACTION_SORT_MENU);
					$_GET[self :: PARAM_COMPONENT_ACTION] = self :: ACTION_COMPONENT_DELETE_CATEGORY;
					$_GET[self :: PARAM_CATEGORY] = $selected_ids;
					break;
			}
		}
	}
}
?>