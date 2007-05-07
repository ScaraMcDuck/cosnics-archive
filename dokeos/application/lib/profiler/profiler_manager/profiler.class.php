<?php
/**
 * @package application.Profiler
 */
require_once dirname(__FILE__).'/profilercomponent.class.php';
require_once dirname(__FILE__).'/profilersearchform.class.php';
require_once dirname(__FILE__).'/../profilerdatamanager.class.php';
require_once dirname(__FILE__).'/../../webapplication.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/configuration.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/condition/orcondition.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/condition/andcondition.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/condition/notcondition.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/condition/equalitycondition.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/condition/likecondition.class.php';
require_once dirname(__FILE__).'/../../../../users/lib/usersdatamanager.class.php';
require_once dirname(__FILE__).'/../profile_publication_table/profilepublicationtable.class.php';
require_once dirname(__FILE__).'/../profilepublisher.class.php';
require_once dirname(__FILE__).'/../profilermenu.class.php';

/**
 * A user manager provides some functionalities to the admin to manage
 * his users. For each functionality a component is available.
 */
 class Profiler extends WebApplication
 {
 	
 	const APPLICATION_NAME = 'profiler';
 	
 	const PARAM_ACTION = 'go';
	const PARAM_DELETE_SELECTED = 'delete_selected';
	const PARAM_MARK_SELECTED_READ = 'mark_selected_read';
	const PARAM_MARK_SELECTED_UNREAD = 'mark_selected_unread';
	const PARAM_FIRSTLETTER = 'firstletter';
	const PARAM_PROFILE_ID = 'profile';
	
	const ACTION_DELETE_PUBLICATION = 'delete';
	const ACTION_VIEW_PUBLICATION = 'view';
	const ACTION_CREATE_PUBLICATION = 'create';
	const ACTION_BROWSE_PROFILES = 'browse';
	
	private $parameters;
	private $search_parameters;
	private $user;
	private $search_form;
	private $breadcrumbs;
	private $firstletter;
	
    function Profiler($user = null)
    {
    	$this->user = $user;
		$this->parameters = array ();
		$this->set_action($_GET[self :: PARAM_ACTION]);
		$this->parse_input_from_table();
		
		if (isset($_GET[Profiler :: PARAM_FIRSTLETTER]))
		{
			$this->firstletter = $_GET[Profiler :: PARAM_FIRSTLETTER];
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
			case self :: ACTION_BROWSE_PROFILES :
				$component = ProfilerComponent :: factory('Browser', $this);
				break;
			case self :: ACTION_VIEW_PUBLICATION :
				$component = ProfilerComponent :: factory('Viewer', $this);
				break;
			case self :: ACTION_DELETE_PUBLICATION :
				$component = ProfilerComponent :: factory('Deleter', $this);
				break;
			case self :: ACTION_CREATE_PUBLICATION :
				$component = ProfilerComponent :: factory('Publisher', $this);
				break;
			default :
				$this->set_action(self :: ACTION_BROWSE_PROFILES);
				$component = ProfilerComponent :: factory('Browser', $this);
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
		
		echo $this->get_menu_html();
		echo '<div style="float: right; width: 80%;">';
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
	
	function get_menu_html()
	{
		$extra_items = array ();
		$create = array ();
		$create['title'] = get_lang('Publish');
		$create['url'] = $this->get_profile_creation_url();
		$create['class'] = 'create';
		$extra_items[] = & $create;
		
		if ($this->get_search_validate())
		{
			// $search_url = $this->get_url();
			$search_url = '#';
			$search = array ();
			$search['title'] = get_lang('SearchResults');
			$search['url'] = $search_url;
			$search['class'] = 'search_results';
			$extra_items[] = & $search;
		}
		else
		{
			$search_url = null;
		}
		
		$temp_replacement = '__FIRSTLETTER__';
		$url_format = $this->get_url(array (Profiler :: PARAM_ACTION => Profiler :: ACTION_BROWSE_PROFILES, Profiler :: PARAM_FIRSTLETTER => $temp_replacement));
		$url_format = str_replace($temp_replacement, '%s', $url_format);
		$user_menu = new ProfilerMenu($this->firstletter, $url_format, & $extra_items);
		
		if ($this->get_action() == self :: ACTION_CREATE_PUBLICATION)
		{
			$user_menu->forceCurrentUrl($create['url'], true);
		}
		elseif(!isset($this->firstletter))
		{
			$user_menu->forceCurrentUrl($this->get_profile_home_url(), true);
		}
		
		if (isset ($search_url))
		{
			$user_menu->forceCurrentUrl($search_url, true);
		}
		
		$html = array();
		$html[] = '<div style="float: left; width: 20%;">';
		$html[] = $user_menu->render_as_tree();
		$html[] = '</div>';
		
		return implode($html, "\n");
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
		$links[] = array('name' => get_lang('ProfileList'), 'action' => 'list', 'url' => $this->get_link(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_PROFILES)));
		return array ('application' => array ('name' => self :: APPLICATION_NAME, 'class' => self :: APPLICATION_NAME), 'links' => $links, 'search' => $this->get_link(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_PROFILES)));
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
	
	function learning_object_is_published($object_id)
	{
		return ProfilerDataManager :: get_instance()->learning_object_is_published($object_id);
	}

	function any_learning_object_is_published($object_ids)
	{
		return ProfilerDataManager :: get_instance()->any_learning_object_is_published($object_ids);
	}

	function get_learning_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return ProfilerDataManager :: get_instance()->get_learning_object_publication_attributes($this->get_user(), $object_id, $type, $offset, $count, $order_property, $order_direction);
	}
	
	function get_learning_object_publication_attribute($object_id)
	{
		return ProfilerDataManager :: get_instance()->get_learning_object_publication_attribute($object_id);
	}	
	
	function count_publication_attributes($type = null, $condition = null)
	{
		return ProfilerDataManager :: get_instance()->count_publication_attributes($this->get_user(), $type, $condition);
	}
	
	function delete_learning_object_publications($object_id)
	{
		return ProfilerDataManager :: get_instance()->delete_profile_publications($object_id);
	}
	
	function update_learning_object_publication_id($publication_attr)
	{
		return ProfilerDataManager :: get_instance()->update_profile_publication_id($publication_attr);
	}
	
	function count_profile_publications($condition = null)
	{
		$pmdm = ProfilerDataManager :: get_instance();
		return $pmdm->count_profile_publications($condition);
	}
	
	function count_unread_profile_publications()
	{
		$pmdm = ProfilerDataManager :: get_instance();
		return $pmdm->count_unread_profile_publications($this->user);
	}
	
	function retrieve_profile_publication($id)
	{
		$pmdm = ProfilerDataManager :: get_instance();
		return $pmdm->retrieve_profile_publication($id);
	}
	
	function retrieve_profile_publications($condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1)
	{
		$pmdm = ProfilerDataManager :: get_instance();
		return $pmdm->retrieve_profile_publications($condition, $orderBy, $orderDir, $offset, $maxObjects);
	}
	
	function get_publication_deleting_url($profile)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_DELETE_PUBLICATION, self :: PARAM_PROFILE_ID => $profile->get_id()));
	}
	
	function get_publication_viewing_url($profile)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_VIEW_PUBLICATION, self :: PARAM_PROFILE_ID => $profile->get_id()));
	}
	
	function get_publication_reply_url($profile)
	{
		return $this->get_url(array (Profiler :: PARAM_ACTION => Profiler :: ACTION_CREATE_PUBLICATION, ProfilePublisher :: PARAM_ACTION => 'publicationcreator', ProfilePublisher :: PARAM_LEARNING_OBJECT_ID => $profile->get_profile(), self :: PARAM_PROFILE_ID => $profile->get_id(), ProfilePublisher :: PARAM_EDIT => 1));
	}
	
	function get_profile_creation_url()
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_CREATE_PUBLICATION));
	}
	
	function get_profile_home_url()
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_BROWSE_PROFILES));
	}
	
	function get_search_condition()
	{
		return $this->get_search_form()->get_condition();
	}
	
	private function get_search_form()
	{
		if (!isset ($this->search_form))
		{
			$this->search_form = new ProfilerSearchForm($this, $this->get_url());
		}
		return $this->search_form;
	}
	
	function get_search_validate()
	{
		return $this->get_search_form()->validate();
	}
	
	private function parse_input_from_table()
	{
		if (isset ($_POST['action']))
		{
			$selected_ids = $_POST[ProfilePublicationTable :: DEFAULT_NAME.ProfilePublicationTable :: CHECKBOX_NAME_SUFFIX];
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
				case self :: PARAM_MARK_SELECTED_READ :
					$this->set_action(self :: ACTION_MARK_PUBLICATION);
					$_GET[self :: PARAM_PROFILE_ID] = $selected_ids;
					$_GET[self :: PARAM_MARK_TYPE] = self :: PARAM_MARK_SELECTED_READ;
					break;
				case self :: PARAM_MARK_SELECTED_UNREAD :
					$this->set_action(self :: ACTION_MARK_PUBLICATION);
					$_GET[self :: PARAM_PROFILE_ID] = $selected_ids;
					$_GET[self :: PARAM_MARK_TYPE] = self :: PARAM_MARK_SELECTED_UNREAD;
					break;
				case self :: PARAM_DELETE_SELECTED :
					$this->set_action(self :: ACTION_DELETE_PUBLICATION);
					$_GET[self :: PARAM_PROFILE_ID] = $selected_ids;
					break;
			}
		}
	}
}
?>