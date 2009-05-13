<?php
/**
 * @package application.lib.linker.linker_manager
 */
require_once dirname(__FILE__).'/linker_component.class.php';
require_once dirname(__FILE__).'/../linker_data_manager.class.php';
require_once dirname(__FILE__).'/../../web_application.class.php';

/**
 * A linker manager provides some functionalities to the admin to manage
 * his users. For each functionality a component is available.
 */
 class Linker extends WebApplication
 {
 	const APPLICATION_NAME = 'linker';

 	const PARAM_ACTION = 'go';
	const PARAM_DELETE_SELECTED = 'delete_selected';
	const PARAM_LINK_ID = 'profile';

	const ACTION_DELETE_LINK = 'delete';
	const ACTION_EDIT_LINK = 'edit';
	const ACTION_CREATE_LINK = 'create';
	const ACTION_BROWSE_LINKS = 'browse';

	private $parameters;
	private $user;

	/**
	 * Constructor
	 * @param User $user The current user
	 */
    function Linker($user = null)
    {
    	$this->user = $user;
		$this->parameters = array ();
		$this->set_action($_GET[self :: PARAM_ACTION]);
    }

    /**
	 * Run this linker manager
	 */
	function run()
	{
		$action = $this->get_action();
		$component = null;
		switch ($action)
		{
			case self :: ACTION_BROWSE_LINKS :
				$component = LinkerComponent :: factory('Browser', $this);
				break;
			case self :: ACTION_DELETE_LINK :
				$component = LinkerComponent :: factory('Deleter', $this);
				break;
			case self :: ACTION_EDIT_LINK :
				$component = LinkerComponent :: factory('Updater', $this);
				break;
			case self :: ACTION_CREATE_LINK :
				$component = LinkerComponent :: factory('Creator', $this);
				break;
			default :
				$this->set_action(self :: ACTION_BROWSE_LINKS);
				$component = LinkerComponent :: factory('Browser', $this);
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

		//echo $this->get_menu_html();
		echo '<div style="float: right; width: 100%;">';
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
	function get_url($additional_parameters = array (), $include_search = false, $encode_entities = false)
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

	/**
	 * Gets the user.
	 * @return int The requested user.
	 */
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
	 * Wrapper for Display :: not_allowed();.
	 */
	function not_allowed()
	{
		Display :: not_allowed();
	}

	/**
	 * Returns a list of actions available to the admin.
	 * @return Array $info Contains all possible actions.
	 */
	public function get_application_platform_admin_links()
	{
		$links = array();
		return array ('application' => array ('name' => self :: APPLICATION_NAME, 'class' => self :: APPLICATION_NAME), 'links' => $links, 'search' => $this->get_link(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_LINKS)));
	}

	/**
	 * Return a link to a certain action of this application
	 * @param array $paramaters The parameters to be added to the url
	 * @param boolean $encode Should the url be encoded ?
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

	function get_platform_setting($variable, $application = 'admin')
	{
		return PlatformSetting :: get($variable, $application);
	}
	
	// Data Retrieving
	
	function count_links($condition)
	{
		return LinkerDataManager :: get_instance()->count_links($condition);
	}
	
	function retrieve_links($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return LinkerDataManager :: get_instance()->retrieve_links($condition, $offset, $count, $order_property, $order_direction);
	}
	
 	function retrieve_link($id)
	{
		return LinkerDataManager :: get_instance()->retrieve_link($id);
	}
	
	// Url Creation
	
	function get_create_link_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_LINK));
	}
	
	function get_update_link_url($link)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_LINK,
								    self :: PARAM_LINK_ID => $link->get_id()));
	}
	
 	function get_delete_link_url($link)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_LINK,
								    self :: PARAM_LINK_ID => $link->get_id()));
	}
	
	// Dummy Methods which are needed because we don't work with learning objects
	function learning_object_is_published($object_id)
	{
	}

	function any_learning_object_is_published($object_ids)
	{
	}

	function get_learning_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
	}

	function get_learning_object_publication_attribute($object_id)
	{
		
	}

	function count_publication_attributes($type = null, $condition = null)
	{
		
	}

	function delete_learning_object_publications($object_id)
	{
		
	}

	function update_learning_object_publication_id($publication_attr)
	{
		
	}
		
	function get_learning_object_publication_locations($learning_object)
	{
		
	}
	
	function publish_learning_object($learning_object, $location)
	{
		
	}

}
?>