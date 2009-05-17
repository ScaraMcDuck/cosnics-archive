<?php
/**
 * $Id$
 * @package application
 */
require_once dirname(__FILE__).'/application.class.php';

abstract class WebApplication extends Application
{

	private $user;

	private $parameters;
	private $search_parameters;
	
	private $breadcrumbs;
	
	const PARAM_ACTION = 'go';

	const PARAM_MESSAGE = 'message';
	const PARAM_ERROR_MESSAGE = 'error_message';
	const PARAM_APPLICATION = 'application';

	function WebApplication($user)
	{
		$this->user = $user;
		$this->parameters = array();
		$this->search_parameters = array();
		$this->breadcrumbs = array();
		$this->set_action($_GET[self :: PARAM_ACTION]);
	}

	/**
	 * Gets the URL of the current page in the application. Optionally takes
	 * an associative array of name/value pairs representing additional query
	 * string parameters; these will either be added to the parameters already
	 * present, or override them if a value with the same name exists.
	 * @param array $parameters The additional parameters, or null if none.
	 * @param boolean $encode Whether or not to encode HTML entities. Defaults
	 *                        to false.
	 * @return string The URL.
	 */
	function get_url($parameters = array (), $encode = false, $filter = false, $filter_on = array())
	{
		if (count($parameters))
		{
			$parameters = array_merge($this->get_parameters(), $parameters);
		}
		else
		{
			$parameters = $this->get_parameters();
		}

		if ($filter)
		{
			$url_parameters = array();
			
			foreach ($parameters as $key => $value)
			{
				if (!in_array($key, $filter_on))
				{
					$url_parameters[$key] = $value;
				}
			}
		}

		$url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'].'?'.http_build_query(($filter ? $url_parameters : $parameters));
		if ($encode)
		{
			$url = htmlentities($url);
		} 
		return $url;
	}
	
	/**
	 * Gets a link to the personal calendar application
	 * @param array $parameters
	 * @param boolean $encode
	 */
	public function get_link($parameters = array (), $encode = false)
	{
		$link = 'run.php';
		
		// Use this untill PHP 5.3 is available
		// Then use get_class($this) :: APPLICATION_NAME
		// and remove the get_application_name function();
		$parameters['application'] = $this->get_application_name();
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
	 * Redirect the end user to another location.
	 * @param string $action The action to take (default = browse learning
	 * objects).
	 * @param string $message The message to show (default = no message).
	 * @param boolean $error_message Is the passed message an error message?
	 * @param array $extra_params Additional parameters to be added to the url
	 */
	function redirect($action = null, $message = null, $error_message = false, $extra_params = array())
	{
		$params = array ();

		if (isset($extra_params))
		{
			foreach($extra_params as $key => $extra)
			{
				$params[$key] = $extra;
			}
		}

		if (isset ($message))
		{
			$params[$error_message ? self :: PARAM_ERROR_MESSAGE :  self :: PARAM_MESSAGE] = $message;
		}
		
		$url = $this->get_url($params);
		header('Location: '.$url); 
		
	}

	/**
	 * Returns the current URL parameters.
	 * @return array The parameters.
	 */
	function get_parameters()
	{
		return $this->parameters;
	}

	/**
	 * Returns the value of the given URL parameter.
	 * @param string $name The parameter name.
	 * @return string The parameter value.
	 */
	function get_parameter($name)
	{
		return $this->parameters[$name];
	}

	/**
	 * Sets the value of a URL parameter.
	 * @param string $name The parameter name.
	 * @param string $value The parameter value.
	 */
	function set_parameter($name, $value)
	{
		$this->parameters[$name] = $value;
	}
	
	function set_breadcrumbs($breadcrumbs)
	{
		$this->breadcrumbs = $breadcrumbs;
	}
	
	function get_breadcrumbs()
	{
		return $this->breadcrumbs;
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
		
		$categories = $this->get_breadcrumbs();
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
	 * Gets the user id of this personal calendars owner
	 * @return int
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
	
	function get_platform_setting($variable)
	{
		// Use this untill PHP 5.3 is available
		// Then use get_class($this) :: APPLICATION_NAME
		// and remove the get_application_name function();
		$application = $this->get_application_name();
		return PlatformSetting :: get($variable, $application);
	}
	
	function get_path($path_type)
	{
		return Path :: get($path_type);
	}
	
	abstract function get_application_name();
	
	/**
	 * Returns a list of actions available to the admin.
	 * @return Array $info Contains all possible actions.
	 */
	public function get_application_platform_admin_links()
	{
		// Use this untill PHP 5.3 is available
		// Then use get_class($this) :: APPLICATION_NAME
		// and remove the get_application_name function();
		$application = $this->get_application_name();
		
		$info = array();
		$info['application'] = array('name' => $application, 'class' => $application);
		$info['links'] = array();
		$info['search'] = null;
		
		return $info;
	}
}
?>