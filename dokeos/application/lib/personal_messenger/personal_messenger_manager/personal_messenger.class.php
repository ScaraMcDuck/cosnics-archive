<?php
/**
 * @package application.personalmessenger
 */
require_once dirname(__FILE__).'/personalmessengercomponent.class.php';
//require_once dirname(__FILE__).'/personalmessagesearchform.class.php';
require_once dirname(__FILE__).'/../personalmessengerdatamanager.class.php';
require_once dirname(__FILE__).'/../../webapplication.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/configuration.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/condition/orcondition.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/condition/andcondition.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/condition/notcondition.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/condition/equalitycondition.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/condition/likecondition.class.php';
require_once dirname(__FILE__).'/../../../../users/lib/usersdatamanager.class.php';

/**
 * A user manager provides some functionalities to the admin to manage
 * his users. For each functionality a component is available.
 */
 class PersonalMessenger extends WebApplication
 {
 	
 	const APPLICATION_NAME = 'personal_messenger';
 	
 	const PARAM_ACTION = 'go';
	const PARAM_RECYCLE_SELECTED = 'recycle_selected';
	const PARAM_MOVE_SELECTED = 'move_selected';
	const PARAM_FOLDER = 'folder';
	
	const ACTION_FOLDER_INBOX = 'inbox';
	const ACTION_FOLDER_OUTBOX = 'outbox';
	
	const ACTION_BROWSE_MESSAGES = 'browse';
	
	private $parameters;
	private $search_parameters;
	private $user;
	private $search_form;
	private $breadcrumbs;
	
    function PersonalMessenger($user = null)
    {
    	$this->user = $user;
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
			case self :: ACTION_BROWSE_MESSAGES :
				$component = PersonalMessengerComponent :: factory('Browser', $this);
				break;
			default :
				$this->set_action(self :: ACTION_BROWSE_MESSAGES);
				$component = PersonalMessengerComponent :: factory('Browser', $this);
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
		global $interbredcrump;
		if (isset ($this->breadcrumbs) && is_array($this->breadcrumbs))
		{
			$breadcrumbs = array_merge($this->breadcrumbs, $breadcrumbs);
		}
		$current_crumb = array_pop($breadcrumbs);
		$interbredcrump = $breadcrumbs;
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
	
	/**
	 * Displays the footer.
	 */
	function display_footer()
	{
		echo '</div>';
		echo '<div class="clear">&nbsp;</div>';
		// TODO: Find out why we need to reconnect here.
		global $dbHost, $dbLogin, $dbPass, $mainDbName;
		mysql_connect($dbHost, $dbLogin, $dbPass);
		mysql_select_db($mainDbName);
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
			$this->search_form = new UserSearchForm($this, $this->get_url());
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
	
	/**
	 * Gets the URL to the Dokeos claroline folder.
	 */
	function get_web_code_path()
	{
		return api_get_path(WEB_CODE_PATH);
	}
	/**
	 * Wrapper for api_not_allowed().
	 */
	function not_allowed()
	{
		api_not_allowed();
	}
	
	public function get_application_platform_admin_links()
	{
		$links = array();
//		$links[] = array('name' => get_lang('UserList'), 'action' => 'list', 'url' => $this->get_link(array(UserManager :: PARAM_ACTION => UserManager :: ACTION_BROWSE_USERS)));
//		$links[] = array('name' => get_lang('UserCreate'), 'action' => 'add', 'url' => $this->get_link(array(UserManager :: PARAM_ACTION => UserManager :: ACTION_CREATE_USER)));
//		$links[] = array('name' => get_lang('UserExport'), 'action' => 'export', 'url' => $this->get_link(array(UserManager :: PARAM_ACTION => UserManager :: ACTION_EXPORT_USERS)));
//		$links[] = array('name' => get_lang('UserImport'), 'action' => 'import', 'url' => $this->get_link(array(UserManager :: PARAM_ACTION => UserManager :: ACTION_IMPORT_USERS)));
		return array('application' => array('name' => get_lang('PersonalMessenger'), 'class' => 'pms'), 'links' => $links, 'search' => $this->get_link(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_MESSAGES)));
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
	
	/**
	 * Always returns false, as this application does not publish learning
	 * objects.
	 * @return boolean Always false.
	 */
	function learning_object_is_published($object_id)
	{
		return false;
	}

	/**
	 * Always returns false, as this application does not publish learning
	 * objects.
	 * @return boolean Always false.
	 */
	function any_learning_object_is_published($object_ids)
	{
		return false;
	}

	/**
	 * Always returns an empty array, as this application does not publish
	 * learning objects.
	 * @return array An empty array.
	 */
	function get_learning_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return array ();
	}
	
	/**
	 * Always returns null, as this application does not publish learning objects.
	 * @return null.
	 */
	function get_learning_object_publication_attribute($object_id)
	{
		return null;
	}	
	
	function count_publication_attributes($type = null, $condition = null)
	{
		return null;
	}
	
	function delete_learning_object_publications($object_id)
	{
		return true;
	}
	
	function update_learning_object_publication_id($publication_attr)
	{
		return true;
	}
	
	function count_personal_message_publications($condition = null)
	{
		$pmdm = PersonalMessengerDataManager :: get_instance();
		return $pmdm->count_personal_message_publications($condition);
	}
	
	function retrieve_personal_message_publications($condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1)
	{
		$pmdm = PersonalMessengerDataManager :: get_instance();
		return $pmdm->retrieve_personal_message_publications($condition, $orderBy, $orderDir, $offset, $maxObjects);
	}
}
?>