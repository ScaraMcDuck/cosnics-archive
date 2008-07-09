<?php
/**
 * @package user.usermanager
 */
require_once dirname(__FILE__).'/home_manager_component.class.php';
require_once dirname(__FILE__).'/../home_data_manager.class.php';
require_once dirname(__FILE__).'/../home_column.class.php';
require_once dirname(__FILE__).'/../home_block.class.php';
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/../../../common/condition/or_condition.class.php';
require_once dirname(__FILE__).'/../../../common/condition/and_condition.class.php';
require_once dirname(__FILE__).'/../../../common/condition/equality_condition.class.php';
require_once dirname(__FILE__).'/../../../common/condition/pattern_match_condition.class.php';

/**
 * A user manager provides some functionalities to the admin to manage
 * his users. For each functionality a component is available.
 */
 class HomeManager {
 	
 	const APPLICATION_NAME = 'home';
 	
 	const PARAM_ACTION = 'go';
	const PARAM_MESSAGE = 'message';
	const PARAM_ERROR_MESSAGE = 'error_message';
	const PARAM_HOME_ID = 'id';
	const PARAM_HOME_TYPE = 'type';
	const PARAM_DIRECTION = 'direction';
	
	const ACTION_VIEW_HOME = 'home';
	const ACTION_BUILD_HOME = 'build';
	const ACTION_MANAGE_HOME = 'manage';
	const ACTION_EDIT_HOME = 'edit';
	const ACTION_DELETE_HOME = 'delete';
	const ACTION_MOVE_HOME = 'move';
	const ACTION_CREATE_HOME = 'create';
	const ACTION_CONFIGURE_HOME = 'configure';
	
	const TYPE_BLOCK = 'block';
	const TYPE_COLUMN = 'column';
	const TYPE_ROW = 'row';
	
	private $parameters;
	private $user;
	private $breadcrumbs;
	
	
    function HomeManager($user = null) {
    	if (isset($user))
    	{
	    	$this->user = $user;
    	}
		$this->parameters = array ();
		$this->set_action($_GET[self :: PARAM_ACTION]);
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
			case self :: ACTION_BUILD_HOME :
				$component = HomeManagerComponent :: factory('Builder', $this);
				break;
			case self :: ACTION_EDIT_HOME :
				$component = HomeManagerComponent :: factory('Editor', $this);
				break;
			case self :: ACTION_MOVE_HOME :
				$component = HomeManagerComponent :: factory('Mover', $this);
				break;
			case self :: ACTION_DELETE_HOME :
				$component = HomeManagerComponent :: factory('Deleter', $this);
				break;
			case self :: ACTION_CREATE_HOME :
				$component = HomeManagerComponent :: factory('Creator', $this);
				break;
			case self :: ACTION_CONFIGURE_HOME :
				$component = HomeManagerComponent :: factory('Configurer', $this);
				break;
			default :
				$this->set_action(self :: ACTION_MANAGE_HOME);
				$component = HomeManagerComponent :: factory('Manager', $this);
		}
		$component->run();
	}
	
	function render_menu($type)
	{
		switch ($type)
		{
			case self :: ACTION_VIEW_HOME :
				$component = HomeManagerComponent :: factory('Home', $this);
				break;
			default :
				$this->set_action(self :: ACTION_VIEW_HOME);
				$component = HomeManagerComponent :: factory('Home', $this);
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
	
	function display_header($breadcrumbtrail = null, $display_search = false)
	{
		// TODO: Implement these small breadcrumbtrail-changes everywhere
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
		Display :: display_header($trail);
		
		if (!is_null($breadcrumbtrail))
		{
			echo '<h3 style="float: left;" title="'.$title.'">'.$title_short.'</h3>';
		}
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
	
	function count_home_rows($condition = null)
	{
		return HomeDataManager :: get_instance()->count_home_rows($condition);
	}
	
	function count_home_columns($condition = null)
	{
		return HomeDataManager :: get_instance()->count_home_columns($condition);
	}
	
	function count_home_blocks($condition = null)
	{
		return HomeDataManager :: get_instance()->count_home_blocks($condition);
	}
	
	function retrieve_home_rows($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return HomeDataManager :: get_instance()->retrieve_home_rows($condition, $offset, $count, $order_property, $order_direction);
	}
	
	function retrieve_home_columns($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return HomeDataManager :: get_instance()->retrieve_home_columns($condition, $offset, $count, $order_property, $order_direction);
	}
	
	function retrieve_home_blocks($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return HomeDataManager :: get_instance()->retrieve_home_blocks($condition, $offset, $count, $order_property, $order_direction);
	}
	
	function retrieve_home_block($id)
	{
		return HomeDataManager :: get_instance()->retrieve_home_block($id);
	}
	
	function retrieve_home_column($id)
	{
		return HomeDataManager :: get_instance()->retrieve_home_column($id);
	}
	
	function retrieve_home_row($id)
	{
		return HomeDataManager :: get_instance()->retrieve_home_row($id);
	}
	
	function truncate_home($user_id)
	{
		return HomeDataManager :: get_instance()->truncate_home($user_id);
	}
	
	function retrieve_home_block_config($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return HomeDataManager :: get_instance()->retrieve_home_block_config($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null);
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
	 * Wrapper for Display :: display_not_allowed().
	 */
	function not_allowed()
	{
		Display :: display_not_allowed();
	}
	
	public function get_application_platform_admin_links()
	{
		$links = array();
		$links[] = array('name' => Translation :: get('Manage'), 'action' => 'manage', 'url' => $this->get_link(array(HomeManager :: PARAM_ACTION => HomeManager :: ACTION_MANAGE_HOME)));
		$links[] = array('name' => Translation :: get('Build'), 'action' => 'build', 'url' => $this->get_link(array(HomeManager :: PARAM_ACTION => HomeManager :: ACTION_BUILD_HOME)));
		return array('application' => array('name' => Translation :: get('Home'), 'class' => 'home'), 'links' => $links, 'search' => null);
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
	
	function get_home_row_creation_url()
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_CREATE_HOME, self :: PARAM_HOME_TYPE => self :: TYPE_ROW));
	}
	
	function get_home_block_creation_url()
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_CREATE_HOME, self :: PARAM_HOME_TYPE => self :: TYPE_BLOCK,));
	}
	
	function get_home_column_creation_url()
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_CREATE_HOME, self :: PARAM_HOME_TYPE => self :: TYPE_COLUMN));
	}
	
	function get_home_row_editing_url($home_row)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_EDIT_HOME, self :: PARAM_HOME_TYPE => self :: TYPE_ROW, self :: PARAM_HOME_ID => $home_row->get_id()));
	}
	
	function get_home_block_editing_url($home_block)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_EDIT_HOME, self :: PARAM_HOME_TYPE => self :: TYPE_BLOCK, self :: PARAM_HOME_ID => $home_block->get_id()));
	}
	
	function get_home_block_configuring_url($home_block)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_CONFIGURE_HOME, self :: PARAM_HOME_ID => $home_block->get_id()));
	}
	
	function get_home_column_editing_url($home_column)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_EDIT_HOME, self :: PARAM_HOME_TYPE => self :: TYPE_COLUMN, self :: PARAM_HOME_ID => $home_column->get_id()));
	}
	
	function get_home_row_deleting_url($home_row)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_DELETE_HOME, self :: PARAM_HOME_TYPE => self :: TYPE_ROW, self :: PARAM_HOME_ID => $home_row->get_id()));
	}
	
	function get_home_block_deleting_url($home_block)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_DELETE_HOME, self :: PARAM_HOME_TYPE => self :: TYPE_BLOCK, self :: PARAM_HOME_ID => $home_block->get_id()));
	}
	
	function get_home_column_deleting_url($home_column)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_DELETE_HOME, self :: PARAM_HOME_TYPE => self :: TYPE_COLUMN, self :: PARAM_HOME_ID => $home_column->get_id()));
	}
	
	function get_home_column_moving_url($home_column, $direction)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_MOVE_HOME, self :: PARAM_HOME_TYPE => self :: TYPE_COLUMN, self :: PARAM_HOME_ID => $home_column->get_id(), self :: PARAM_DIRECTION => $direction));
	}
	
	function get_home_block_moving_url($home_block, $direction)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_MOVE_HOME, self :: PARAM_HOME_TYPE => self :: TYPE_BLOCK, self :: PARAM_HOME_ID => $home_block->get_id(), self :: PARAM_DIRECTION => $direction));
	}
	
	function get_home_row_moving_url($home_row, $direction)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_MOVE_HOME, self :: PARAM_HOME_TYPE => self :: TYPE_ROW, self :: PARAM_HOME_ID => $home_row->get_id(), self :: PARAM_DIRECTION => $direction));
	}
	
	function retrieve_home_block_at_sort($parent, $sort, $direction)
	{
		$hdm = HomeDataManager :: get_instance();
		return $hdm->retrieve_home_block_at_sort($parent, $sort, $direction);
	}
	
	function retrieve_home_column_at_sort($parent, $sort, $direction)
	{
		$hdm = HomeDataManager :: get_instance();
		return $hdm->retrieve_home_column_at_sort($parent, $sort, $direction);
	}
	
	function retrieve_home_row_at_sort($sort, $direction)
	{
		$hdm = HomeDataManager :: get_instance();
		return $hdm->retrieve_home_row_at_sort($sort, $direction);
	}
	
	function get_platform_setting($variable, $application = self :: APPLICATION_NAME)
	{
		return PlatformSetting :: get($variable, $application = self :: APPLICATION_NAME);
	}	
}
?>