<?php
/**
 * @package install.installmanager
 */
require_once dirname(__FILE__).'/installmanagercomponent.class.php';
require_once dirname(__FILE__).'/../installdatamanager.class.php';
require_once dirname(__FILE__).'/../../../common/html/formvalidator/FormValidator.class.php';
/**
 * An install manager provides some functionalities to the end user to install
 * his Dokeos platform
 *
 * @author Hans De Bisschop
 */
class InstallManager
{
	const APPLICATION_NAME = 'install';

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
	const ACTION_INSTALL_PLATFORM = 'install';

   /**#@+
    * Property of this repository manager.
 	*/
	private $parameters;
	private $breadcrumbs;
	/**#@-*/
	/**
	 * Constructor
	 * @param int $user_id The user id of current user
	 */
	function InstallManager()
	{
		$this->parameters = array ();
		$this->set_action($_GET[self :: PARAM_ACTION]);
	}
	/**
	 * Run this repository manager
	 */
	function run()
	{
		/*
		 * Only setting breadcrumbs here. Some stuff still calls
		 * forceCurrentUrl(), but that should not affect the breadcrumbs.
		 */
		$action = $this->get_action();
		$component = null;
		switch ($action)
		{
			case self :: ACTION_INSTALL_PLATFORM :
				$component = InstallManagerComponent :: factory('Installer', $this);
				break;
			default :
				$this->set_action(self :: ACTION_INSTALL_PLATFORM);
				$component = InstallManagerComponent :: factory('Installer', $this);
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
		$this->display_header_content();
//		echo '<div style="float: left; width: 100%;">';
//		echo '<div>';
//		echo '<h3 style="float: left;" title="'.$title.'">'.$title_short.'</h3>';
//		echo '</div>';
//		echo '<div class="clear">&nbsp;</div>';
//		if ($msg = $_GET[self :: PARAM_MESSAGE])
//		{
//			$this->display_message($msg);
//		}
//		if($msg = $_GET[self::PARAM_ERROR_MESSAGE])
//		{
//			$this->display_error_message($msg);
//		}
	}
	
	function display_header_content()
	{
		global $dokeos_version, $installType, $updateFromVersion;
		
		echo '<!DOCTYPE html
		     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
		     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
		echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">'."\n";
		echo '<head>'."\n";
		echo '<title>-- ' .$dokeos_version . ' Installation --</title>'."\n";
		echo '<link rel="stylesheet" href="../main/css/default.css" type="text/css"/>'."\n";
		echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />'."\n";		
		echo '</head>'."\n";
		echo '<body dir="'. get_lang('text_dir') .'">' . "\n";
		
		echo '<!-- #outerframe container to control some general layout of all pages -->'."\n";
		echo '<div id="outerframe">'."\n";
		
		echo '<div id="header">  <!-- header section start -->'."\n";
		echo '<div id="header1"> <!-- top of banner with institution name/hompage link -->'."\n";
		echo 'Dokeos installation - version ' . $dokeos_version;
		if ($installType == 'new')
		{
			echo ' - New installation';
		}
		elseif ($installType == 'update')
		{
			echo ' - Update from Dokeos ' . implode('|',$updateFromVersion);
		}		
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
		global $dokeos_version, $installType, $updateFromVersion;
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
	function redirect($action = self :: ACTION_BROWSE_LEARNING_OBJECTS, $message = null, $new_category_id = 0, $error_message = false, $extra_params = null)
	{
		$params = array ();
		$params[self :: PARAM_ACTION] = $action;
		if (isset ($message))
		{
			$params[$error_message ? self :: PARAM_ERROR_MESSAGE :  self :: PARAM_MESSAGE] = $message;
		}
		if ($new_category_id)
		{
			$params[self :: PARAM_CATEGORY_ID] = $new_category_id;
		}
		if (isset($extra_params))
		{
			foreach($extra_params as $key => $extra)
			{
				$params[$key] = $extra;
			}
		}
		$url = $this->get_url($params);
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
	 * Wrapper for Display :: display_not_allowed().
	 */
	function not_allowed()
	{
		Display :: display_not_allowed();
	}

	public function get_application_platform_admin_links()
	{
		$links = array();
		$links[] = array('name' => get_lang('NoOptionsAvailable'), action => 'empty', 'url' => $this->get_link());
		return array('application' => array('name' => get_lang('Repository'), 'class' => self :: APPLICATION_NAME), 'links' => $links, 'search' => $this->get_link(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_LEARNING_OBJECTS)));
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
}
?>