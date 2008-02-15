<?php
/**
 * @package admin.lib.admin_manager
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__).'/admincomponent.class.php';
require_once dirname(__FILE__).'/../admindatamanager.class.php';

/**
 * The admin allows the platform admin to configure certain aspects of his platform
 */
class Admin {

	const PARAM_ACTION = 'go';
	const PARAM_MESSAGE = 'message';
	const PARAM_ERROR_MESSAGE = 'error_message';

	const ACTION_ADMIN_BROWSER = 'browse';
	const ACTION_SYSTEM_ANNOUNCEMENTS = 'systemannouncements';
	const ACTION_LANGUAGES = 'languages';
	const ACTION_SETTINGS = 'settings';

	private $parameters;

	private $user;

	/**
	 * Constructor
	 * @param User $user The current user
	 */
    function Admin($user = null) {
    	$this->user = $user;
		$this->parameters = array ();
		$this->set_action($_GET[self :: PARAM_ACTION]);
    }

	/**
	 * Run this admin manager
	 */
    function run()
    {
		$action = $this->get_action();
		$component = null;
		switch ($action)
		{
			case self :: ACTION_SYSTEM_ANNOUNCEMENTS :
				$component = AdminComponent :: factory('Systemannouncements', $this);
				break;
			default :
				$component = AdminComponent :: factory('Browser', $this);
		}
		$component->run();
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
	/**
	 * Displays the footer.
	 */
	function display_footer()
	{
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
	 * Gets the value of a parameter.
	 * @param string $name The parameter name.
	 * @return string The parameter value.
	 */
	function get_parameter($name)
	{
		return $this->parameters[$name];
	}

	function get_user()
	{
		return $this->user;
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

	function get_application_platform_admin_links()
	{
		$adm = AdminDataManager :: get_instance();
		return $adm->get_application_platform_admin_links($this->user);
	}

	/**
	 * Gets the URL to the Dokeos claroline folder.
	 */
	function get_web_code_path()
	{
		return Path :: get_path('WEB_CODE_PATH');
	}
}
?>