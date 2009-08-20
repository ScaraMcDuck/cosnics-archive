<?php
/**
 * @package user.usermanager
 */
require_once Path :: get_rights_path() . 'lib/user_right_manager/user_right_manager.class.php';
require_once Path :: get_rights_path() . 'lib/user_right_manager/user_right_manager_component.class.php';
require_once Path :: get_rights_path() . 'lib/rights_data_manager.class.php';
require_once Path :: get_rights_path() . 'lib/rights_utilities.class.php';
require_once Path :: get_rights_path() . 'lib/location_menu.class.php';
require_once Path :: get_rights_path() . 'lib/user_right_manager/component/location_browser_table/location_browser_table.class.php';
require_once Path :: get_library_path() . 'html/action_bar/action_bar_renderer.class.php';

class UserRightManagerBrowserComponent extends UserRightManagerComponent
{
	private $action_bar;
	
	private $application;
	private $location;
	private $user;

	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$this->application = Request :: get(UserRightManager :: PARAM_SOURCE);
		$location = Request :: get(UserRightManager :: PARAM_LOCATION);
		$user = Request :: get(UserRightManager :: PARAM_USER);

		$trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
		$trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_USER_RIGHTS)), Translation :: get('UserRights')));

		if (!isset($user))
		{
			$this->display_header($trail);
			$this->display_error_message(Translation :: get('NoUserSelected'));
			$this->display_footer();
			exit;
		}
		else
		{
		    $udm = UserDataManager :: get_instance();
		    $this->user = $udm->retrieve_user($user);
		    $trail->add(new Breadcrumb($this->get_url(array(UserRightManager :: PARAM_USER_RIGHT_ACTION => UserRightManager :: ACTION_BROWSE_USER_RIGHTS)), $this->user->get_fullname()));
		    $trail->add_help('rights general');
		}

		if (!isset($this->application))
		{
			$this->display_header($trail);
			echo $this->get_applications();
			$this->display_warning_message(Translation :: get('SelectApplication'));
			$this->display_footer();
			exit;
		}
		else
		{
		    $conditions = array();
   			$conditions[] = new EqualityCondition(Location :: PROPERTY_PARENT, 0);
   			$conditions[] = new EqualityCondition(Location :: PROPERTY_APPLICATION, $this->application);
   			$condition = new AndCondition($conditions);
   			$root = RightsDataManager :: get_instance()->retrieve_locations($condition, null, 1, array(new ObjectTableOrder(Location :: PROPERTY_LOCATION)))->next_result();

			if (isset($location))
			{
			    $this->location = $this->retrieve_location($location);
			}
			else
			{
			    $this->location = $root;
			}

//			$parent_conditions = array();
//			$parent_conditions[] = new InequalityCondition(Location :: PROPERTY_LEFT_VALUE, InequalityCondition :: LESS_THAN_OR_EQUAL, $this->location->get_left_value());
//			$parent_conditions[] = new InequalityCondition(Location :: PROPERTY_RIGHT_VALUE, InequalityCondition :: GREATER_THAN_OR_EQUAL, $this->location->get_right_value());
//			$parent_conditions[] = new EqualityCondition(Location :: PROPERTY_APPLICATION, $this->application);
//
//			$parent_condition = new AndCondition($parent_conditions);
//			$order = array(new ObjectTableOrder(Location :: PROPERTY_LEFT_VALUE));
//			$order_direction = array(SORT_ASC);
//
//			$parents = $this->retrieve_locations($parent_condition, null, null, $order, $order_direction);
//
//			while($parent = $parents->next_result())
//			{
//				$trail->add(new Breadcrumb($this->get_url(array('location' => $parent->get_id())), $parent->get_location()));
//			}

			$this->action_bar = $this->get_action_bar();

			$this->display_header($trail);
			echo $this->get_applications();
			
			echo $this->action_bar->as_html() . '<br />';

			$url_format = $this->get_url(array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_USER_RIGHTS, UserRightManager :: PARAM_USER_RIGHT_ACTION => UserRightManager :: ACTION_BROWSE_USER_RIGHTS, UserRightManager :: PARAM_USER => $this->user->get_id(), UserRightManager :: PARAM_SOURCE => $this->application, UserRightManager :: PARAM_LOCATION => '%s'));
			$url_format = str_replace('=%25s', '=%s', $url_format);
    		$location_menu = new LocationMenu($root->get_id(), $this->location->get_id(), $url_format);
    		echo '<div style="float: left; width: 18%; overflow: auto; height: 500px;">';
    		echo $location_menu->render_as_tree();
    		echo '</div>';

    		$table = new LocationBrowserTable($this, $this->get_parameters(), $this->get_condition());

    		echo '<div style="float: right; width: 80%;">';
    		echo $table->as_html();
    		echo '</div>';

			$this->display_footer();
		}
	}

	function get_condition()
	{
		$condition = new EqualityCondition(Location :: PROPERTY_PARENT, $this->location->get_id());

		$query = $this->action_bar->get_query();
		if(isset($query) && $query != '')
		{
			$and_conditions = array();
			$and_conditions[] = $condition;
			$and_conditions[] = new PatternMatchCondition(Location :: PROPERTY_LOCATION, '*' . $query . '*');
			$condition = new AndCondition($and_conditions);
		}

		return $condition;
	}

	function get_source()
	{
	    return $this->application;
	}

	function get_current_user()
	{
	    return $this->user;
	}

	function get_rights()
	{
		$application = $this->application;

		$base_path = (WebApplication :: is_application($application) ? (Path :: get_application_path() . 'lib/' . $application . '/') : (Path :: get(SYS_PATH). $application . '/lib/'));
		$class = $application . '_rights.class.php';
		$file = $base_path . $class;

		if(!file_exists($file))
		{
			$rights = array();
		}
		else
		{
		    require_once($file);

    		// TODO: When PHP 5.3 gets released, replace this by $class :: get_available_rights()
    	    $reflect = new ReflectionClass(Application :: application_to_class($application) . 'Rights');
    	    $rights = $reflect->getConstants();
		}

		return $rights;
	}

	function get_applications()
	{
		$application = $this->application;

		$html = array();

		$html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/application.js');
		$html[] = '<div class="configure">';

		$the_applications = WebApplication :: load_all();
		$the_applications = array_merge(array('admin', 'tracking', 'repository', 'user', 'group', 'rights', 'home', 'menu', 'webservice', 'reporting'), $the_applications);

		foreach ($the_applications as $the_application)
		{
			if (isset($application) && $application == $the_application)
			{
				$html[] = '<div class="application_current">';
			}
			else
			{
				$html[] = '<div class="application">';
			}

			$application_name = Translation :: get(DokeosUtilities :: underscores_to_camelcase($the_application));

			$html[] = '<a href="'. $this->get_url(array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_USER_RIGHTS, UserRightManager :: PARAM_USER => $this->user->get_id(), UserRightManager :: PARAM_SOURCE => $the_application)) .'">';
			$html[] = '<img src="'. Theme :: get_image_path('admin') . 'place_' . $the_application .'.png" border="0" style="vertical-align: middle;" alt="' . $application_name . '" title="' . $application_name . '"/><br />'. $application_name;
			$html[] = '</a>';
			$html[] = '</div>';
		}

		$html[] = '</div>';
		$html[] = '<div style="clear: both;"></div>';

		return implode("\n", $html);
	}

	function get_modification_links()
	{
		$location = $this->location;
		$locked_parent = $location->get_locked_parent();

		$toolbar = new Toolbar();

		if(!isset($locked_parent))
		{
			if ($location->is_locked())
			{
				$toolbar->add_item(new ToolbarItem(Translation :: get('UnlockChildren'), Theme :: get_common_image_path() . 'action_unlock.png', $this->get_url(array(RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ACTION => RightsTemplateManager :: ACTION_UNLOCK_RIGHTS_TEMPLATES, RightsTemplateManager :: PARAM_LOCATION => $location->get_id()))));
			}
			else
			{
				$toolbar->add_item(new ToolbarItem(Translation :: get('LockChildren'), Theme :: get_common_image_path() . 'action_lock.png', $this->get_url(array(RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ACTION => RightsTemplateManager :: ACTION_LOCK_RIGHTS_TEMPLATES, RightsTemplateManager :: PARAM_LOCATION => $location->get_id()))));
			}

			if (!$location->is_root())
			{
				if ($location->inherits())
				{
					$toolbar->add_item(new ToolbarItem(Translation :: get('Disinherit'), Theme :: get_common_image_path() . 'action_setting_false_inherit.png', $this->get_url(array(RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ACTION => RightsTemplateManager :: ACTION_DISINHERIT_RIGHTS_TEMPLATES, RightsTemplateManager :: PARAM_LOCATION => $location->get_id()))));
				}
				else
				{
					$toolbar->add_item(new ToolbarItem(Translation :: get('Inherit'), Theme :: get_common_image_path() . 'action_setting_true_inherit.png', $this->get_url(array(RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ACTION => RightsTemplateManager :: ACTION_INHERIT_RIGHTS_TEMPLATES, RightsTemplateManager :: PARAM_LOCATION => $location->get_id()))));
				}
			}
		}

		return $toolbar->as_html();
	}
	
	function get_action_bar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
		$action_bar->set_search_url($this->get_url(array(UserRightManager :: PARAM_SOURCE => $this->application, UserRightManager :: PARAM_USER => $this->user->get_id(), UserRightManager :: PARAM_LOCATION => $this->location->get_id())));
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('RootRights'), Theme :: get_common_image_path().'action_rights.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

		return $action_bar;
	}
}
?>