<?php
/**
 * @package user.groupsmanager
 */
require_once dirname(__FILE__).'/classgroupmanagercomponent.class.php';
require_once dirname(__FILE__).'/classgroupsearchform.class.php';
require_once dirname(__FILE__).'/../classgroupdatamanager.class.php';
require_once dirname(__FILE__).'/../classgroup.class.php';
require_once dirname(__FILE__).'/../../../common/html/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/../../../common/condition/orcondition.class.php';
require_once dirname(__FILE__).'/../../../common/condition/andcondition.class.php';
require_once dirname(__FILE__).'/../../../common/condition/equalitycondition.class.php';
require_once dirname(__FILE__).'/../../../common/condition/likecondition.class.php';
//require_once dirname(__FILE__).'/../classgroup_rel_user_table/classgrouprelusertable.class.php';
require_once dirname(__FILE__).'/../classgroup_table/classgrouptable.class.php';

/**
 * A user manager provides some functionalities to the admin to manage
 * his users. For each functionality a component is available.
 */
 class ClassGroupManager {
 	
 	const APPLICATION_NAME = 'classgroup';
 	
 	const PARAM_ACTION = 'go';
	const PARAM_MESSAGE = 'message';
	const PARAM_ERROR_MESSAGE = 'error_message';
	const PARAM_CLASSGROUP_ID = 'classgroup_id';
	const PARAM_CLASSGROUP_REL_USER_ID = 'classgroup_rel_user_id';
	const PARAM_USER_ID = 'user_id';
	const PARAM_REMOVE_SELECTED = 'delete';
	const PARAM_UNSUBSCRIBE_SELECTED = 'unsubscribe';
	const PARAM_TRUNCATE_SELECTED = 'truncate';
	const PARAM_FIRSTLETTER = 'firstletter';
	const PARAM_COMPONENT_ACTION = 'action';
	
	const ACTION_CREATE_CLASSGROUP = 'create';
	const ACTION_BROWSE_CLASSGROUPS = 'browse';
	const ACTION_EDIT_CLASSGROUP = 'edit';
	const ACTION_DELETE_CLASSGROUP = 'delete';
	const ACTION_TRUNCATE_CLASSGROUP = 'truncate';
	const ACTION_VIEW_CLASSGROUP = 'view';
	const ACTION_SUBSCRIBE_USER_TO_CLASSGROUP = 'subscribe';
	const ACTION_UNSUBSCRIBE_USER_FROM_CLASSGROUP = 'unsubscribe';
	
	private $parameters;
	private $search_parameters;
	private $user_id;
	private $user;
	private $category_menu;
	private $quota_url;
	private $publication_url;
	private $create_url;
	private $recycle_bin_url;
	private $breadcrumbs;
	
    function ClassGroupManager($user = null) {
    	$this->user = $user;
		$this->parameters = array ();
		$this->set_action($_GET[self :: PARAM_ACTION]);
		$this->parse_input_from_table();
		$this->create_url = $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_CREATE_CLASSGROUP));   	
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
			case self :: ACTION_CREATE_CLASSGROUP :
				$component = ClassGroupManagerComponent :: factory('Creator', $this);
				break;
			case self :: ACTION_EDIT_CLASSGROUP :
				$component = ClassGroupManagerComponent :: factory('Editor', $this);
				break;
			case self :: ACTION_TRUNCATE_CLASSGROUP :
				$component = ClassGroupManagerComponent :: factory('Truncater', $this);
				break;
			case self :: ACTION_VIEW_CLASSGROUP :
				$component = ClassGroupManagerComponent :: factory('Viewer', $this);
				break;
			case self :: ACTION_BROWSE_CLASSGROUPS :
				$component = ClassGroupManagerComponent :: factory('Browser', $this);
				break;
			case self :: ACTION_UNSUBSCRIBE_USER_FROM_CLASSGROUP :
				$component = ClassGroupManagerComponent :: factory('Unsubscriber', $this);
				break;
			case self :: ACTION_SUBSCRIBE_USER_TO_CLASSGROUP :
				$component = ClassGroupManagerComponent :: factory('Subscriber', $this);
				break;
			default :
				$this->set_action(self :: ACTION_BROWSE_CLASSGROUPS);
				$component = ClassGroupManagerComponent :: factory('Browser', $this);
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
	function display_header($breadcrumbs = array (), $display_search = false)
	{
		global $interbreadcrumb;
		if (isset ($this->breadcrumbs) && is_array($this->breadcrumbs))
		{
			$breadcrumbs = array_merge($this->breadcrumbs, $breadcrumbs);
		}
		$current_crumb = array_pop($breadcrumbs);
		$interbreadcrumb = $breadcrumbs;
		$title = $current_crumb['name'];
		$title_short = $title;
		if (strlen($title_short) > 53)
		{
			$title_short = substr($title_short, 0, 50).'&hellip;';
		}
		Display :: display_header($title_short);
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
	
	private function display_search_form()
	{
		echo $this->get_search_form()->display();
	}
	
	function count_classgroups($condition = null)
	{
		return ClassGroupDataManager :: get_instance()->count_classgroups($condition);
	}
	
	function count_classgroup_rel_users($condition = null)
	{
		return ClassGroupDataManager :: get_instance()->count_classgroup_rel_users($condition);
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
	
	function get_search_condition()
	{
		return $this->get_search_form()->get_condition();
	}
	
	private function get_search_form()
	{
		if (!isset ($this->search_form))
		{
			$this->search_form = new ClassGroupSearchForm($this, $this->get_url());
		}
		return $this->search_form;
	}
	
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
	
	function retrieve_classgroups($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return ClassGroupDataManager :: get_instance()->retrieve_classgroups($condition, $offset, $count, $order_property, $order_direction);
	}
	
	function retrieve_classgroup_rel_users($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return ClassGroupDataManager :: get_instance()->retrieve_classgroup_rel_users($condition, $offset, $count, $order_property, $order_direction);
	}
	
	function retrieve_classgroup_rel_user($user_id)
	{
		return ClassGroupDataManager :: get_instance()->retrieve_classgroup_rel_user($user_id);
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
		return $this->user->get_user_id();
	}
	
	function get_user()
	{
		return $this->user;
	}
	
	function retrieve_classgroup($id)
	{
		$gdm = ClassGroupDataManager :: get_instance();
		return $gdm->retrieve_classgroup($id);
	}
	
	/**
	 * Gets the URL to the Dokeos claroline folder.
	 */
	function get_web_code_path()
	{
		return Path :: get(WEB_IMG_PATH);
	}
	/**
	 * Wrapper for api_not_allowed().
	 */
	function not_allowed()
	{
		Display :: display_not_allowed();
	}
	
	public function get_application_platform_admin_links()
	{
		$links = array();
		$links[] = array('name' => Translation :: get('ClassGroupList'), 'action' => 'list', 'url' => $this->get_link(array(ClassGroupManager :: PARAM_ACTION => ClassGroupManager :: ACTION_BROWSE_CLASSGROUPS)));
		$links[] = array('name' => Translation :: get('ClassGroupCreate'), 'action' => 'add', 'url' => $this->get_link(array(ClassGroupManager :: PARAM_ACTION => ClassGroupManager :: ACTION_CREATE_CLASSGROUP)));
		return array('application' => array('name' => Translation :: get('ClassGroups'), 'class' => 'classgroups'), 'links' => $links, 'search' => $this->get_link(array(ClassGroupManager :: PARAM_ACTION => ClassGroupManager :: ACTION_BROWSE_CLASSGROUPS)));
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
	
	function get_classgroup_editing_url($classgroup)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_EDIT_CLASSGROUP, self :: PARAM_CLASSGROUP_ID => $classgroup->get_id()));
	}
	
	function get_classgroup_emptying_url($classgroup)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_TRUNCATE_CLASSGROUP, self :: PARAM_CLASSGROUP_ID => $classgroup->get_id()));
	}
	
	function get_classgroup_viewing_url($classgroup)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_VIEW_CLASSGROUP, self :: PARAM_CLASSGROUP_ID => $classgroup->get_id()));
	}
	
	function get_classgroup_rel_user_unsubscribing_url($classgroupreluser)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_UNSUBSCRIBE_USER_FROM_CLASSGROUP, self :: PARAM_CLASSGROUP_REL_USER_ID => $classgroupreluser->get_classgroup_id() . '|' . $classgroupreluser->get_user_id()));
	}
	
	function get_classgroup_rel_user_subscribing_url($classgroup)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_SUBSCRIBE_USER_TO_CLASSGROUP));
	}
	
	private function parse_input_from_table()
	{
		if (isset ($_POST['action']))
		{
			if (isset($_POST['table']) && $_POST['table'] == 'classgroupreluser')
			{
				$selected_ids = $_POST[ClassGroupRelUserTable :: DEFAULT_NAME.ClassGroupRelUserTable :: CHECKBOX_NAME_SUFFIX];
				
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
					case self :: PARAM_UNSUBSCRIBE_SELECTED :
						$this->set_action(self :: ACTION_UNSUBSCRIBE_USER_FROM_CLASSGROUP);
						$_GET[self :: PARAM_CLASSGROUP_REL_USER_ID] = $selected_ids;
						break;
				}
			}
			elseif(isset($_POST['table']) && $_POST['table'] == 'classgroup')
			{
				$selected_ids = $_POST[ClassGroupTable :: DEFAULT_NAME.ClassGroupTable :: CHECKBOX_NAME_SUFFIX];
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
					case self :: PARAM_REMOVE_SELECTED :
						$this->set_action(self :: ACTION_DELETE_CLASSGROUP);
						$_GET[self :: PARAM_CLASSGROUP_ID] = $selected_ids;
						break;
					case self :: PARAM_TRUNCATE_SELECTED :
						$this->set_action(self :: ACTION_TRUNCATE_CLASSGROUP);
						$_GET[self :: PARAM_CLASSGROUP_ID] = $selected_ids;
						break;
				}
			}
		}
	}
}
?>