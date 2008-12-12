<?php
/**
 * @package migration.migrationmanager
 */
require_once dirname(__FILE__).'/migration_manager_component.class.php';
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';

/**
 * A migration manager provides some functionalities to the administrator to migrate
 * from an old system to the LCMS
 *
 * @author Sven Vanpoucke
 */
class MigrationManager 
{
	const APPLICATION_NAME = 'migration';
	
	/**#@+
    * Constant defining a parameter of the repository manager.
 	*/
	// SortableTable hogs 'action' so we'll use something else.
	const PARAM_ACTION = 'go';
	const PARAM_MESSAGE = 'message';
	const PARAM_ERROR_MESSAGE = 'error_message';

   /**#@+
    * Constant defining an action of the repository manager.
 	*/
	const ACTION_MIGRATE = 'migrate';
	
	/**#@+
    * Property of this repository manager.
 	*/
	private $parameters;
	private $user;
	private $breadcrumbs;
	
	/**#@-*/
	/**
	 * Constructor
	 * @param int $user_id The user id of current user
	 */
	function MigrationManager($user)
	{
		$this->user = $user;
		$this->parameters = array ();
		$this->set_action($_GET[self :: PARAM_ACTION]);
	}
	
	/**
	 * Runs the migrationmanager, choose the correct component with the given parameters
	 */
	function run()
	{
		$action = $this->get_action();
		$component = null;
		switch ($action)
		{
			case self :: ACTION_MIGRATE :
				$component = MigrationManagerComponent :: factory('Migration', $this);
				break;
			default :
				$this->set_action(self :: ACTION_MIGRATE);
				$component = MigrationManagerComponent :: factory('Migration', $this);
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
	function display_header($breadcrumbs = array ())
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
		$this->display_header_content();
	}
	
	/**
	 * Displays the content of the header
	 */
	function display_header_content()
	{	
		echo '<!DOCTYPE html
		     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
		     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
		echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">'."\n";
		echo '<head>'."\n";
		echo '<title>-- Dokeos Migration --</title>'."\n";
		echo '<link rel="stylesheet" href="../layout/css/default.css" type="text/css"/>'."\n";
		echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />'."\n";		
		echo '</head>'."\n";
		echo '<body dir="'. Translation :: get('text_dir') .'">' . "\n";
		
		echo '<!-- #outerframe container to control some general layout of all pages -->'."\n";
		echo '<div id="outerframe">'."\n";
		
		echo '<div id="header">  <!-- header section start -->'."\n";
		echo '<div id="header1"> <!-- top of banner with institution name/hompage link -->'."\n";
		echo 'Dokeos Migration';

		echo '</div>'."\n";
		echo '<div class="clear">&nbsp;</div>'."\n";
		echo '</div> <!-- end of the whole #header section -->'."\n";
		echo '<div id="main"> <!-- start of #main wrapper for #content and #menu divs -->'."\n";
		echo '<!--   Begin Of script Output   -->'."\n";
	}
	
	/**
	 * Displays the footer.
	 */
	function display_footer()
	{
		echo '</div>';
		echo '<div class="clear">&nbsp;</div> <!-- \'clearing\' div to make sure that footer stays below the main and right column sections -->'."\n";
		echo "\n";
		echo '<div id="footer"> <!-- start of #footer section -->'."\n";
		echo $dokeos_version . '&nbsp;&copy;&nbsp;2007-'. date('Y');
		echo '</div> <!-- end of #footer -->'."\n";
		echo '</div> <!-- end of #outerframe opened in header -->'."\n";
		echo "\n";
		echo '</body>'."\n";
		echo '</html>'."\n";
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
	 * Wrapper for Display :: not_allowed().
	 */
	function not_allowed()
	{
		Display :: not_allowed();
	}
	
	public function get_application_platform_admin_links()
	{
		$links = array();
		return array('application' => array('name' => Translation :: get('Migration'), 'class' => self :: APPLICATION_NAME), 'links' => $links, 'search' => null);
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
	
	public function get_link($parameters = array (), $encode = false)
	{
		$link = 'index_'. self :: APPLICATION_NAME .'_manager.php';
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
	 * Gets the user.
	 * @return int The user.
	 */
	function get_user()
	{
		return $this->user;
	}
}
?>