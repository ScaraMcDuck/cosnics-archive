<?php
/**
 * @author Michael Kyndt
 */
require_once dirname(__FILE__).'/reporting_manager_component.class.php';
require_once dirname(__FILE__).'/../reporting_data_manager.class.php';
require_once dirname(__FILE__).'/../../../common/html/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/../../../common/condition/or_condition.class.php';
require_once dirname(__FILE__).'/../../../common/condition/and_condition.class.php';
require_once dirname(__FILE__).'/../../../common/condition/equality_condition.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table.class.php';

/**
 * A webservice manager provides some functionalities to the admin to manage
 * his webservices.
 */
 class ReportingManager {
 	
 	const APPLICATION_NAME = 'reporting';
 	
 	const PARAM_ACTION = 'go';
	const PARAM_MESSAGE = 'message';
	const PARAM_ERROR_MESSAGE = 'error_message';
	const PARAM_REMOVE_SELECTED = 'delete';
	const PARAM_FIRSTLETTER = 'firstletter';
	const PARAM_COMPONENT_ACTION = 'action';
	const PARAM_APPLICATION = 'application';
	//const PARAM_TEMPLATE = 'template';
    const PARAM_TEMPLATE_ID = 'template';
	
	const PARAM_ROLE_ID = 'role';
	
	const ACTION_BROWSE_TEMPLATES = 'browse_templates';
	const ACTION_ADD_TEMPLATE = 'add_template';
	const ACTION_DELETE_TEMPLATE = 'delete_template';
	const ACTION_VIEW_TEMPLATE = 'application_templates';
	//const PARAM_TEMPLATE_ID = 'template_id';
	
	private $parameters;
	private $search_parameters;
	private $user_id;
	private $user;
	private $user_search_form;
	private $group_search_form;
	private $category_menu;
	private $quota_url;
	private $publication_url;
	private $create_url;
	private $recycle_bin_url;
	private $breadcrumbs;
		
    function ReportingManager($user = null) 
    {
    	$this->user = $user;
		$this->parameters = array ();
		$this->set_action($_GET[self :: PARAM_ACTION]);		
		//$this->create_url = $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_CREATE_GROUP));   	
    }
    
    /**
	 * Run this reporting manager
	 */
	function run()
	{
		$action = $this->get_action();
		$component = null;		
		switch ($action)
		{
			case self :: ACTION_ADD_TEMPLATE :
				$component = ReportingManagerComponent :: factory('ReportingTemplateAdd', $this);
				break;
			case self :: ACTION_DELETE_TEMPLATE :
				$component = ReportingManagerComponent :: factory('ReportingTemplateDelete', $this);
				break;
			case self :: ACTION_BROWSE_TEMPLATES :
				$component = ReportingManagerComponent :: factory('ReportingTemplateBrowser', $this);
				break;
			case self :: ACTION_VIEW_TEMPLATE :
				$component = ReportingManagerComponent :: factory('ReportingTemplateView',$this);
				break;
			default:
				$this->set_action(self :: ACTION_BROWSE_TEMPLATES);
				$component = ReportingManagerComponent :: factory('ReportingTemplateBrowser', $this);
				break;
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
	function display_header($breadcrumbtrail = array(), $display_search = false)
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
		Display :: header($breadcrumbtrail, $title_short);
		echo '<h3 style="float: left;" title="'.$title.'">'.$title_short.'</h3>';
		if ($display_search)
		{
			$this->display_search_form();
		}
		echo '<div class="clear">&nbsp;</div>';
		
		$message = Request :: get(self :: PARAM_MESSAGE);
		if (isset($message))
		{
			$this->display_message($message);
		}
		$message = Request :: get(self :: PARAM_ERROR_MESSAGE);
		if(isset($message))
		{
			$this->display_error_message($message);
		}
	}	
	
	/**
	 * Displays the footer.
	 */
	function display_footer()
	{
		//echo '</div>';
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
	
//	function retrieve_available_templates($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
//	{
//		return ReportingDataManager :: get_instance()->retrieve_available_templates($condition,$offset,$order_property,$order_direction);
//	}

	/**
	 * Returns an array of platform reporting templates for an application
	 */
	function retrieve_platform_reporting_templates_for_application($application)
	{
		$rpdm = ReportingDatamanager :: get_instance();
		$conditions[] = new EqualityCondition('application',$application);
		$conditions[] = new EqualityCondition('platform','1');
		$cond = new AndCondition($conditions);
		$templateresultset = $rpdm->retrieve_reporting_templates($cond);
		while($template = $templateresultset->next_result())
		{
			$templates[] = $template;
		}
		return $templates;
	}

    function retrieve_platform_reporting_templates_for_application_res($application)
	{
		$rpdm = ReportingDatamanager :: get_instance();
		$conditions[] = new EqualityCondition('application',$application);
		$conditions[] = new EqualityCondition('platform','1');
		$cond = new AndCondition($conditions);
		return $rpdm->retrieve_reporting_templates($cond);
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
		return PAth :: get_path($path_type);
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
		$links[] = array('name' => Translation :: get('List'), 'action' => 'list', 'url' => $this->get_link(array(ReportingManager :: PARAM_ACTION => ReportingManager :: ACTION_BROWSE_TEMPLATES)));
		//$links[] = array('name' => Translation :: get('Create'), 'action' => 'add', 'url' => $this->get_link(array(ReportingManager :: PARAM_ACTION => ReportingManager :: ACTION_ADD_TEMPLATE)));
		//$links[] = array('name' => Translation :: get('Delete'), 'action' => 'remove', 'url' => $this->get_link(array(ReportingManager :: PARAM_ACTION => ReportingManager :: ACTION_DELETE_TEMPLATE)));
		return array('application' => array('name' => Translation :: get('Reporting'), 'class' => 'reporting'), 'links' => $links, 'search' => null);
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

    function get_reporting_template_viewing_url($reporting_template)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_VIEW_TEMPLATE, self :: PARAM_TEMPLATE_ID => $reporting_template->get_id()));
	}

	/*function is_allowed($right, $role_id, $location_id)
	{
		$rdm = RightsDataManager :: get_instance();
		$rolerightlocation = $rdm->retrieve_role_right_location($right, $role_id, $location_id);
		return $rolerightlocation->get_value();
	}
	
	function retrieve_role_right_location($right_id, $role_id, $location_id)
	{
		return RightsDataManager :: get_instance()->retrieve_role_right_location($right_id, $role_id, $location_id);
	}
	
	function get_role_deleting_url($role)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_DELETE_ROLES, self :: PARAM_ROLE_ID => $role->get_id()));
	}
	
	function get_role_editing_url($role)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_EDIT_ROLES, self :: PARAM_ROLE_ID => $role->get_id()));
	}*/
 }