<?php
/**
 * @package user.usermanager
 */
require_once Path :: get_rights_path() . 'lib/user_right_manager/user_right_manager.class.php';
require_once Path :: get_rights_path() . 'lib/user_right_manager/user_right_manager_component.class.php';
require_once Path :: get_rights_path() . 'lib/rights_data_manager.class.php';
require_once Path :: get_rights_path() . 'lib/rights_utilities.class.php';

class UserRightManagerBrowserComponent extends UserRightManagerComponent
{
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

		if (isset($location))
		{
			$this->location = $this->retrieve_location($location);
		}

		if (!isset($this->application) && !isset($this->location))
		{
			$this->display_header($trail);
			echo $this->get_applications();
			$this->display_warning_message(Translation :: get('SelectApplication'));
			$this->display_footer();
			exit;
		}
		else
		{

			if (!isset($this->application))
			{
				$this->application = 'admin';
			}

			if (!isset($this->location))
			{
				$root_conditions = array();
				$root_conditions[] = new EqualityCondition(Location :: PROPERTY_APPLICATION, $this->application);
				$root_conditions[] = new EqualityCondition(Location :: PROPERTY_PARENT, 0);

				$root_condition = new AndCondition($root_conditions);

				$root = $this->retrieve_locations($root_condition, null, 1);
				if ($root->size() > 0)
				{
					$this->location = $this->retrieve_location($root->next_result()->get_id());
				}
				else
				{
					$this->display_header($trail);
					$this->display_warning_message(Translation :: get('NoSuchLocationAndOrApplication'));
					$this->display_footer();
					exit;
				}
			}

			$parent_conditions = array();
			$parent_conditions[] = new InequalityCondition(Location :: PROPERTY_LEFT_VALUE, InequalityCondition :: LESS_THAN_OR_EQUAL, $this->location->get_left_value());
			$parent_conditions[] = new InequalityCondition(Location :: PROPERTY_RIGHT_VALUE, InequalityCondition :: GREATER_THAN_OR_EQUAL, $this->location->get_right_value());
			$parent_conditions[] = new EqualityCondition(Location :: PROPERTY_APPLICATION, $this->application);

			$parent_condition = new AndCondition($parent_conditions);
			$order = array(new ObjectTableOrder(Location :: PROPERTY_LEFT_VALUE));
			$order_direction = array(SORT_ASC);

			$parents = $this->retrieve_locations($parent_condition, null, null, $order, $order_direction);

			while($parent = $parents->next_result())
			{
				$trail->add(new Breadcrumb($this->get_url(array('location' => $parent->get_id())), $parent->get_location()));
			}

			$this->display_header($trail);
			$table = $this->get_rights_table_html();

			if ($table)
			{
			    echo $this->get_applications();
//			    echo $this->get_modification_links();
			    echo $table;
    			echo $this->get_location_information();
    			echo $this->get_relations();
    			echo RightsUtilities :: get_rights_legend();
			}
			else
			{
			    echo '<div class="warning-message">' . Translation :: get('NoRightsForApplication') . '</div>';
			}
			$this->display_footer();
		}
	}

	function get_rights_table_html()
	{
		$application = $this->application;
		$location = $this->location;

		$base_path = (WebApplication :: is_application($application) ? (Path :: get_application_path() . 'lib/' . $application . '/') : (Path :: get(SYS_PATH). $application . '/lib/'));
		$class = $application . '_rights.class.php';
		$file = $base_path . $class;

		if(!file_exists($file))
		{
			return false;
		}

		require_once($file);

		// TODO: When PHP 5.3 gets released, replace this by $class :: get_available_rights()
	    $reflect = new ReflectionClass(Application :: application_to_class($application) . 'Rights');
	    $rights = $reflect->getConstants();
	    // TODO: When PHP 5.3 gets released, replace this by $class :: get_available_rights()

		$rights_array = array();

		$html = array();

		$html[] = '<div style="margin-bottom: 10px;">';
		$html[] = '<div style="padding: 5px; border-bottom: 1px solid #DDDDDD;">';
		$html[] = '<div style="float: left; width: 50%;"></div>';
		$html[] = '<div style="float: right; width: 40%;">';

		foreach($rights as $right_name => $right_id)
		{
			$real_right_name = DokeosUtilities :: underscores_to_camelcase(strtolower($right_name));
			$html[] = '<div style="float: left; width: 24%; text-align: center;">'. Translation :: get($real_right_name) .'</div>';
			$rights_array[$right_id] = $right_name;
		}

		$html[] = '</div>';
		$html[] = '<div style="clear: both;"></div>';
		$html[] = '</div>';
		$html[] = '<div style="clear: both;"></div>';

		$rights_templates = $this->retrieve_rights_templates();
		$locked_parent = $location->get_locked_parent();

		while ($rights_template = $rights_templates->next_result())
		{
			$html[] = '<div style="padding: 5px; border-bottom: 1px solid #DDDDDD;">';
			$html[] = '<div style="float: left; width: 50%;">'. Translation :: get($rights_template->get_name()) .'</div>';
			$html[] = '<div style="float: right; width: 40%;">';

			foreach ($rights_array as $id => $name)
			{
				$html[] = '<div id="r_'. $id .'_'. $rights_template->get_id() .'_'. $location->get_id() .'" style="float: left; width: 24%; text-align: center;">';
				if (isset($locked_parent))
				{
					$value = $this->is_allowed($id, $rights_template->get_id(), $locked_parent->get_id());
					$html[] = '<a href="'. $this->get_url(array('application' => $this->application, 'location' => $locked_parent->get_id())) .'">' . ($value == 1 ? '<img src="'. Theme :: get_common_image_path() .'action_setting_true_locked.png" title="'. Translation :: get('LockedTrue') .'" />' : '<img src="'. Theme :: get_common_image_path() .'action_setting_false_locked.png" title="'. Translation :: get('LockedFalse') .'" />') . '</a>';
				}
				else
				{
					$value = $this->is_allowed($id, $rights_template->get_id(), $location->get_id());

					if (!$value)
					{
						if ($location->inherits())
						{
							$inherited_value = RightsUtilities :: is_allowed_for_rights_template($rights_template->get_id(), $id, $location, $this->application);

							if ($inherited_value)
							{
								$html[] = '<a class="setRight" href="'. $this->get_url(array(RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ACTION => RightsTemplateManager :: ACTION_SET_RIGHTS_TEMPLATES, 'rights_template_id' => $rights_template->get_id(), 'right_id' => $id, RightsTemplateManager :: PARAM_LOCATION => $location->get_id())) .'">' . '<div class="rightInheritTrue"></div></a>';
							}
							else
							{
								$html[] = '<a class="setRight" href="'. $this->get_url(array(RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ACTION => RightsTemplateManager :: ACTION_SET_RIGHTS_TEMPLATES, 'rights_template_id' => $rights_template->get_id(), 'right_id' => $id, RightsTemplateManager :: PARAM_LOCATION => $location->get_id())) .'">' . '<div class="rightFalse"></div></a>';
							}
						}
						else
						{
							$html[] = '<a class="setRight" href="'. $this->get_url(array(RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ACTION => RightsTemplateManager :: ACTION_SET_RIGHTS_TEMPLATES, 'rights_template_id' => $rights_template->get_id(), 'right_id' => $id, RightsTemplateManager :: PARAM_LOCATION => $location->get_id())) .'">' . '<div class="rightFalse"></div></a>';
						}
					}
					else
					{
						$html[] = '<a class="setRight" href="'. $this->get_url(array(RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ACTION => RightsTemplateManager :: ACTION_SET_RIGHTS_TEMPLATES, 'rights_template_id' => $rights_template->get_id(), 'right_id' => $id, RightsTemplateManager :: PARAM_LOCATION => $location->get_id())) .'">' . '<div class="rightTrue"></div></a>';
					}
				}
				$html[] = '</div>';
			}

			$html[] = '</div>';
			$html[] = '<div style="clear: both;"></div>';
			$html[] = '</div>';
			$html[] = '<div style="clear: both;"></div>';
		}

		$html[] = '<script type="text/javascript" src="'. Path :: get(WEB_LIB_PATH) . 'javascript/rights_ajax.js' .'"></script>';
		$html[] = '</div>';

		return implode("\n", $html);
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

	function get_location_information()
	{
		$location = $this->location;

		$html = array();

		$html[] = '<div class="learning_object" style="background-image: url('.Theme :: get_common_image_path().'place_location.png);">';
		$html[] = '<div class="title">'. Translation :: get('Location') .'</div>';

		$html[] = Translation :: get('Application'). ': ' . Translation :: get(Application :: application_to_class($location->get_application())) . '<br />';
		$html[] = Translation :: get('Type'). ': ' . DokeosUtilities :: underscores_to_camelcase($location->get_type()) . '<br />';
		$html[] = Translation :: get('Name'). ': ' . $location->get_location() . '<br />';
		$html[] = '</div>';

		return implode("\n", $html);
	}

	function get_relations()
	{
		$html = array();

		//$html[] = DokeosUtilities :: add_block_hider();
		//$html[] = DokeosUtilities :: build_block_hider('location_relations');

		$parents = $this->location->get_parents(false);

		if ($parents->size() > 0)
		{
			$html[] = '<div class="learning_object" style="background-image: url('.Theme :: get_common_image_path().'place_parents.png);">';
			$html[] = '<div class="title">'. Translation :: get('Parents') .'</div>';

			$parents_html = array();
			while($parent = $parents->next_result())
			{
				$parents_html[] = '<a href="'. $this->get_url(array(RightsTemplateManager :: PARAM_SOURCE => $this->application, RightsTemplateManager :: PARAM_LOCATION => $parent->get_id())) .'">'. $parent->get_location() .'</a>';
			}
			$html[] = implode(', ', $parents_html);

			$html[] = '</div>';
		}

		$siblings = $this->location->get_siblings(false);

		if ($siblings->size() > 0)
		{
			$html[] = '<div class="learning_object" style="background-image: url('.Theme :: get_common_image_path().'place_siblings.png);">';
			$html[] = '<div class="title">'. Translation :: get('Siblings') .'</div>';
			$html[] = '<ul class="rights_siblings">';

			$siblings_html = array();
			while($sibling = $siblings->next_result())
			{
				$siblings_html[] = '<a href="'. $this->get_url(array(RightsTemplateManager :: PARAM_SOURCE => $this->application, RightsTemplateManager :: PARAM_LOCATION => $sibling->get_id())) .'">'. $sibling->get_location() .'</a>';
			}
			$html[] = implode(', ', $siblings_html);

			$html[] = '</ul>';
			$html[] = '</div>';
		}

		$children = $this->location->get_children();

		if ($children->size() > 0)
		{
			$html[] = '<div class="learning_object" style="background-image: url('.Theme :: get_common_image_path().'place_children.png);">';
			$html[] = '<div class="title">'. Translation :: get('Children') .'</div>';
			$html[] = '<ul class="rights_children">';

			$children_html = array();
			while($child = $children->next_result())
			{
				$children_html[] = '<a href="'. $this->get_url(array(RightsTemplateManager :: PARAM_SOURCE => $this->application, RightsTemplateManager :: PARAM_LOCATION => $child->get_id())) .'">'. $child->get_location() .'</a>';
			}
			$html[] = implode(', ', $children_html);

			$html[] = '</ul>';
			$html[] = '</div>';
		}

		//$html[] = DokeosUtilities :: build_block_hider();

		return implode("\n", $html);
	}

//	function display_header($trail, $helpitem)
//	{
//		$this->get_parent()->display_header($trail, $helpitem);
//		echo $this->get_applications();
//		echo '<div class="clear">&nbsp;</div>';
//	}

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
}
?>