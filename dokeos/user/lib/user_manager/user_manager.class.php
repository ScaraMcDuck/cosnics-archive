<?php
/**
 * @package users.lib.usermanager
 */
require_once dirname(__FILE__).'/user_manager_component.class.php';
require_once dirname(__FILE__).'/../forms/user_search_form.class.php';
require_once dirname(__FILE__).'/../user_data_manager.class.php';
require_once dirname(__FILE__).'/../user.class.php';
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
require_once Path :: get_library_path().'condition/or_condition.class.php';
require_once Path :: get_library_path().'condition/and_condition.class.php';
require_once Path :: get_library_path().'condition/equality_condition.class.php';
require_once dirname(__FILE__).'/../user_block.class.php';

/**
 * A user manager provides some functionalities to the admin to manage
 * his users. For each functionality a component is available.
 */
 class UserManager {

 	const APPLICATION_NAME = 'user';

 	const PARAM_ACTION = 'go';
	const PARAM_MESSAGE = 'message';
	const PARAM_ERROR_MESSAGE = 'error_message';
	const PARAM_USER_USER_ID = 'user_id';
	const PARAM_REMOVE_SELECTED = 'delete';
	const PARAM_FIRSTLETTER = 'firstletter';

	const ACTION_CREATE_USER = 'create';
	const ACTION_BROWSE_USERS = 'adminbrowse';
	const ACTION_EXPORT_USERS = 'export';
	const ACTION_IMPORT_USERS = 'import';
	const ACTION_UPDATE_USER = 'update';
	const ACTION_DELETE_USER = 'delete';
	const ACTION_REGISTER_USER = 'register';
	const ACTION_VIEW_ACCOUNT = 'account';
	const ACTION_USER_QUOTA = 'quota';
	const ACTION_RESET_PASSWORD = 'reset_password';
	const ACTION_CHANGE_USER = 'change_user';
	const ACTION_MANAGE_ROLES = 'manage_user_roles';
	
	const ACTION_VIEW_BUDDYLIST = 'buddy_view';
	const ACTION_CREATE_BUDDYLIST_CATEGORY = 'buddy_create_category';
	const ACTION_DELETE_BUDDYLIST_CATEGORY = 'buddy_delete_category';
	const ACTION_UPDATE_BUDDYLIST_CATEGORY = 'buddy_update_category';
	const ACTION_CREATE_BUDDYLIST_ITEM = 'buddy_create_item';
	const ACTION_DELETE_BUDDYLIST_ITEM = 'buddy_delete_item';
	const ACTION_CHANGE_BUDDYLIST_ITEM_STATUS = 'buddy_status_change';
	const ACTION_CHANGE_BUDDYLIST_ITEM_CATEGORY = 'buddy_category_change';
	
	const PARAM_BUDDYLIST_CATEGORY = 'buddylist_category';
	const PARAM_BUDDYLIST_ITEM = 'buddylist_item';

	private $parameters;
	private $search_parameters;
	private $user_id;
	private $user;
	private $search_form;
	private $category_menu;
	private $quota_url;
	private $publication_url;
	private $create_url;
	private $recycle_bin_url;
	private $breadcrumbs;


    function UserManager($user = null)
    {
    	$this->load_user($user);
    	$this->load_user_theme();
    	
    	// Can users set their own theme and if they
    	// can, do they have one set ? If so apply it
    	$user = $this->get_user();
    	
    	if (is_object($user))
    	{
    		$user_can_set_theme = $this->get_platform_setting('allow_user_theme_selection');
    		
    		if ($user_can_set_theme && $user->has_theme())
    		{
    			Theme :: set_theme($user->get_theme());
    		}
    	}
    	
		$this->parameters = array ();
		
		$this->set_action(isset($_GET[self :: PARAM_ACTION]) ? $_GET[self :: PARAM_ACTION] : null);
		$this->create_url = $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_CREATE_USER));
    }
    
	/**
	 * Sets the current user based on the input passed on to the UserManager.
	 * @param mixed $user The user.
	 */
    function load_user($user)
    {
    	if (isset($user))
    	{
    		if (is_object($user))
    		{
   				$this->user_id = $user->get_id();
	    		$this->user = $user;
    		}
    		else
    		{
   				$this->user_id = $user;
   				if (!is_null($user))
   				{
	    			$this->user = $this->retrieve_user($this->user_id);
   				}
   				else
   				{
   					$this->user = null;
   				}
    		}
    	}
    }
    
	/**
	 * Sets the platform theme to the user's selection if allowed.
	 */
    function load_user_theme()
    {
    	// TODO: Add theme to userforms.
    	$user = $this->get_user();
    	
    	if (is_object($user))
    	{
    		$user_can_set_theme = $this->get_platform_setting('allow_user_theme_selection');
    		
    		if ($user_can_set_theme && $user->has_theme())
    		{
    			Theme :: set_theme($user->get_theme());
    		}
    	}
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
			case self :: ACTION_CREATE_USER :
				$component = UserManagerComponent :: factory('Creator', $this);
				break;
			case self :: ACTION_REGISTER_USER :
				$component = UserManagerComponent :: factory('Register', $this);
				break;
			case self :: ACTION_UPDATE_USER :
				$component = UserManagerComponent :: factory('Updater', $this);
				break;
			case self :: ACTION_DELETE_USER:
				$component = UserManagerComponent :: factory('Deleter', $this);
				break;
			case self :: ACTION_IMPORT_USERS :
				$this->force_menu_url($this->create_url, true);
				$component = UserManagerComponent :: factory('Importer', $this);
				break;
			case self :: ACTION_EXPORT_USERS :
				$this->force_menu_url($this->create_url, true);
				$component = UserManagerComponent :: factory('Exporter', $this);
				break;
			case self :: ACTION_USER_QUOTA :
				$component = UserManagerComponent :: factory('quota', $this);
				break;
			case self :: ACTION_BROWSE_USERS :
				$component = UserManagerComponent :: factory('AdminUserBrowser', $this);
				break;
			case self :: ACTION_VIEW_ACCOUNT :
				$component = UserManagerComponent :: factory('Account', $this);
				break;
			case self :: ACTION_RESET_PASSWORD :
				$component = UserManagerComponent :: factory('ResetPassword', $this);
				break;
			case self :: ACTION_CHANGE_USER :
				$component = UserManagerComponent :: factory('ChangeUser', $this);
				break;
			case self :: ACTION_MANAGE_ROLES :
				$component = UserManagerComponent :: factory('UserRoleManager', $this);
				break;
			case self :: ACTION_VIEW_BUDDYLIST :
				$component = UserManagerComponent :: factory('BuddyListViewer', $this);
				break;
			case self :: ACTION_CREATE_BUDDYLIST_CATEGORY :
				$component = UserManagerComponent :: factory('BuddyListCategoryCreator', $this);
				break;
			case self :: ACTION_DELETE_BUDDYLIST_CATEGORY :
				$component = UserManagerComponent :: factory('BuddyListCategoryDeleter', $this);
				break;
			case self :: ACTION_UPDATE_BUDDYLIST_CATEGORY :
				$component = UserManagerComponent :: factory('BuddyListCategoryEditor', $this);
				break;
			case self :: ACTION_CREATE_BUDDYLIST_ITEM :	
				$component = UserManagerComponent :: factory('BuddyListItemCreator', $this);
				break;
			case self :: ACTION_DELETE_BUDDYLIST_ITEM :
				$component = UserManagerComponent :: factory('BuddyListItemDeleter', $this);
				break;
			case self :: ACTION_CHANGE_BUDDYLIST_ITEM_STATUS :
				$component = UserManagerComponent :: factory('BuddyListItemStatusChanger', $this);
				break;
			case self :: ACTION_CHANGE_BUDDYLIST_ITEM_CATEGORY :
				$component = UserManagerComponent :: factory('BuddyListItemCategoryChanger', $this);
				break;
			default :
				$this->set_action(self :: ACTION_BROWSE_USERS);
				$component = UserManagerComponent :: factory('AdminUserBrowser', $this);
		}
		$component->run();
	}
	
    /**
	 * Renders the users block and returns it. 
	 */
	function render_block($block)
	{
		$user_block = UserBlock :: factory($this, $block);
		return $user_block->run();
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
	 * Display the search form
	 */
	private function display_search_form()
	{
		echo $this->get_search_form()->display();
	}

	/**
	 * Counts the users
	 * @param $condition
	 */
	function count_users($condition = null)
	{
		return UserDataManager :: get_instance()->count_users($condition);
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
	 * @see UserSearchForm::get_condition()
	 */
	function get_search_condition()
	{
		return $this->get_search_form()->get_condition();
	}

	/**
	 * Gets the Search form
	 */
	private function get_search_form()
	{
		if (!isset ($this->search_form))
		{
			$this->search_form = new UserSearchForm($this, $this->get_url());
		}
		return $this->search_form;
	}

	/**
	 * Gets the validation of the search form
	 */
	function get_search_validate()
	{
		return $this->get_search_form()->validate();
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
	 * Retrieve the users
	 * @param $condition
	 * @param $offset
	 * @param $count
	 * @param $order_property
	 * @param $order_direction
	 */
	function retrieve_users($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return UserDataManager :: get_instance()->retrieve_users($condition, $offset, $count, $order_property, $order_direction);
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
		$url = parse_url($this->get_path(WEB_PATH));
		$url = $url['scheme'].'://'.$url['host'];
		$eventual_parameters = array_merge($this->get_parameters($include_search), $additional_parameters);
		$url .= $_SERVER['PHP_SELF'].'?'.http_build_query($eventual_parameters);
		if ($encode_entities)
		{
			$url .= htmlentities($url);
		}

		return $url;
	}
	/**
	 * Gets the user id.
	 * @return int The requested user id.
	 */
	function get_user_id()
	{
		return $this->user_id;
	}

	/**
	 * Gets the user
	 * @return user the requested user.
	 */
	function get_user()
	{
		return $this->user;
	}

	/**
	 * Retrieves a user.
	 * @param int $id The id of the user.
	 */
	function retrieve_user($id)
	{
		$udm = UserDataManager :: get_instance();
		return $udm->retrieve_user($id);
	}

	function retrieve_user_by_username($username)
	{
		$udm = UserDataManager :: get_instance();
		return $udm->retrieve_user_by_username($username);
	}

	/**
	 * @see RepositoryDataManager::learning_object_deletion_allowed()
	 */
	function user_deletion_allowed($user)
	{
		$udm = UserDataManager :: get_instance();
		return $udm->user_deletion_allowed($user);
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

	/**
	 * Gets the available links to display in the platform admin
	 * @retun array of links and actions
	 */
	public function get_application_platform_admin_links()
	{
		$links		= array();
		$links[]	= array('name' => Translation :: get('List'),
							'description' => Translation :: get('ListDescription'),
							'action' => 'list',
							'url' => $this->get_link(array(UserManager :: PARAM_ACTION => UserManager :: ACTION_BROWSE_USERS)));
		$links[]	= array('name' => Translation :: get('Create'),
							'description' => Translation :: get('CreateDescription'),
							'action' => 'add',
							'url' => $this->get_link(array(UserManager :: PARAM_ACTION => UserManager :: ACTION_CREATE_USER)));
		$links[]	= array('name' => Translation :: get('Export'),
							'description' => Translation :: get('ExportDescription'),
							'action' => 'export',
							'url' => $this->get_link(array(UserManager :: PARAM_ACTION => UserManager :: ACTION_EXPORT_USERS)));
		$links[]	= array('name' => Translation :: get('Import'),
							'description' => Translation :: get('ImportDescription'),
							'action' => 'import',
							'url' => $this->get_link(array(UserManager :: PARAM_ACTION => UserManager :: ACTION_IMPORT_USERS)));
		
		return array('application' => array('name' => Translation :: get('Users'), 'class' => 'user'), 'links' => $links, 'search' => $this->get_link(array(UserManager :: PARAM_ACTION => UserManager :: ACTION_BROWSE_USERS)));
	}

	/**
	 * Gets a link
	 * @return $link the requested link
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
	 * gets the user editing url
	 * @param return the requested url
	 */
	function get_user_editing_url($user)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_UPDATE_USER, self :: PARAM_USER_USER_ID => $user->get_id()));
	}
	
	function get_change_user_url($user)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_CHANGE_USER, 
															   self :: PARAM_USER_USER_ID => $user->get_id()));
	}

	/**
	 * gets the user quota url
	 * @param return the requested url
	 */
	function get_user_quota_url($user)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_USER_QUOTA, self :: PARAM_USER_USER_ID => $user->get_id()));
	}
	/**
	 * gets the user delete url
	 * @param return the requested url
	 */
	function get_user_delete_url($user)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_DELETE_USER, self :: PARAM_USER_USER_ID => $user->get_id()));
	}
	
	function get_manage_roles_url($user)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_MANAGE_ROLES, self :: PARAM_USER_USER_ID => $user->get_id()));
	}
	
	function get_platform_setting($variable, $application = self :: APPLICATION_NAME)
	{
		return PlatformSetting :: get($variable, $application = self :: APPLICATION_NAME);
	}	
	
	function get_create_buddylist_category_url()
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_CREATE_BUDDYLIST_CATEGORY));
	}
	
 	function get_delete_buddylist_category_url($category_id)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_DELETE_BUDDYLIST_CATEGORY,
									 self :: PARAM_BUDDYLIST_CATEGORY => $category_id));
	}
	
 	function get_update_buddylist_category_url($category_id)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_UPDATE_BUDDYLIST_CATEGORY,
									 self :: PARAM_BUDDYLIST_CATEGORY => $category_id));
	}
	
 	function get_create_buddylist_item_url()
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_CREATE_BUDDYLIST_ITEM));
	}
	
 	function get_delete_buddylist_item_url($item_id)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_DELETE_BUDDYLIST_ITEM,
									 self :: PARAM_BUDDYLIST_ITEM => $item_id));
	}
	
 	function get_change_buddylist_item_status_url($item_id, $status)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_CHANGE_BUDDYLIST_ITEM_STATUS,
									 self :: PARAM_BUDDYLIST_ITEM => $item_id, 'status' => $status));
	}
	
}
?>