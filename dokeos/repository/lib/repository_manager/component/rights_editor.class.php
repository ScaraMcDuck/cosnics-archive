<?php
/**
 * $Id$
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../repository_manager_component.class.php';
require_once dirname(__FILE__).'/../../repository_rights.class.php';

/**
 * Repository manager component to edit the rights for the learning objects in
 * the repository.
 */
class RepositoryManagerRightsEditorComponent extends RepositoryManagerComponent
{
	private $location;

	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$object = Request :: get(RepositoryManager :: PARAM_LEARNING_OBJECT_ID);
		$this->location = RepositoryRights :: get_location_by_identifier('learning_object', $object);

		$trail = new BreadcrumbTrail(false);
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('EditRights')));

		$component_action = $_GET[RightsManager :: PARAM_COMPONENT_ACTION];

		switch($component_action)
		{
			case 'edit':
				$this->edit_right();
				break;
			case 'lock':
				$this->lock_location();
				break;
			case 'inherit':
				$this->inherit_location();
				break;
			default :
				$this->show_rights_list();
		}
	}

	function get_rights_table_html()
	{
		$rdm = RightsDataManager :: get_instance();

		$application = RepositoryManager :: APPLICATION_NAME;
		$location = $this->location;

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

		$roles = $rdm->retrieve_roles();
		$locked_parent = $location->get_locked_parent();

		while ($role = $roles->next_result())
		{
			$html[] = '<div style="padding: 5px; border-bottom: 1px solid #DDDDDD;">';
			$html[] = '<div style="float: left; width: 50%;">'. Translation :: get($role->get_name()) .'</div>';
			$html[] = '<div style="float: right; width: 40%;">';

			foreach ($rights_array as $id => $name)
			{
				$html[] = '<div id="r_'. $id .'_'. $role->get_id() .'_'. $location->get_id() .'" style="float: left; width: 24%; text-align: center;">';
				if (isset($locked_parent))
				{
					$value = $rdm->retrieve_role_right_location($id, $role->get_id(), $locked_parent->get_id())->get_value();
					$html[] = '<a href="'. $this->get_url(array('application' => $this->application, 'location' => $locked_parent->get_id())) .'">' . ($value == 1 ? '<img src="'. Theme :: get_common_image_path() .'action_setting_true_locked.png" title="'. Translation :: get('LockedTrue') .'" />' : '<img src="'. Theme :: get_common_image_path() .'action_setting_false_locked.png" title="'. Translation :: get('LockedFalse') .'" />') . '</a>';
				}
				else
				{
					$value = $rdm->retrieve_role_right_location($id, $role->get_id(), $location->get_id())->get_value();

					if (!$value)
					{
						if ($location->inherits())
						{
							$inherited_value = RightsUtilities :: is_allowed_for_role($role->get_id(), $id, $location, $location->get_application());

							if ($inherited_value)
							{
								$html[] = '<a class="setRight" href="'. $this->get_url(array(RightsManager :: PARAM_COMPONENT_ACTION => 'edit', 'application' => $this->application, 'role_id' => $role->get_id(), 'right_id' => $id, 'location' => $location->get_id())) .'">' . '<div class="rightInheritTrue"></div></a>';
							}
							else
							{
								$html[] = '<a class="setRight" href="'. $this->get_url(array(RightsManager :: PARAM_COMPONENT_ACTION => 'edit', 'application' => $this->application, 'role_id' => $role->get_id(), 'right_id' => $id, 'location' => $location->get_id())) .'">' . '<div class="rightFalse"></div></a>';
							}
						}
						else
						{
							$html[] = '<a class="setRight" href="'. $this->get_url(array(RightsManager :: PARAM_COMPONENT_ACTION => 'edit', 'application' => $this->application, 'role_id' => $role->get_id(), 'right_id' => $id, 'location' => $location->get_id())) .'">' . '<div class="rightFalse"></div></a>';
						}
					}
					else
					{
						$html[] = '<a class="setRight" href="'. $this->get_url(array(RightsManager :: PARAM_COMPONENT_ACTION => 'edit', 'application' => $this->application, 'role_id' => $role->get_id(), 'right_id' => $id, 'location' => $location->get_id())) .'">' . '<div class="rightTrue"></div></a>';
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

	function get_modification_links()
	{
		$location = $this->location;
		$locked_parent = $location->get_locked_parent();

		$toolbar = new Toolbar();

		if(!isset($locked_parent))
		{
			if ($location->is_locked())
			{
				$toolbar->add_item(new ToolbarItem(Translation :: get('UnlockChildren'), Theme :: get_common_image_path() . 'action_unlock.png', $this->get_url(array(RightsManager :: PARAM_COMPONENT_ACTION => 'lock', 'application' => $this->application, 'object' => $location->get_identifier()))));
			}
			else
			{
				$toolbar->add_item(new ToolbarItem(Translation :: get('LockChildren'), Theme :: get_common_image_path() . 'action_lock.png', $this->get_url(array(RightsManager :: PARAM_COMPONENT_ACTION => 'lock', 'application' => $this->application, 'object' => $location->get_identifier()))));
			}

			if (!$location->is_root())
			{
				if ($location->inherits())
				{
					$toolbar->add_item(new ToolbarItem(Translation :: get('LocationNoInherit'), Theme :: get_common_image_path() . 'action_setting_false_inherit.png', $this->get_url(array(RightsManager :: PARAM_COMPONENT_ACTION => 'inherit', 'application' => $this->application, 'object' => $location->get_identifier()))));
				}
				else
				{
					$toolbar->add_item(new ToolbarItem(Translation :: get('LocationInherit'), Theme :: get_common_image_path() . 'action_setting_true_inherit.png', $this->get_url(array(RightsManager :: PARAM_COMPONENT_ACTION => 'inherit', 'application' => $this->application, 'object' => $location->get_identifier()))));
				}
			}
		}

		return $toolbar->as_html();
	}

	function edit_right()
	{
		$role = $_GET['role_id'];
		$right = $_GET['right_id'];
		$location =  $this->location;

		$success = RightsUtilities :: invert_role_right_location($right, $role, $location);
		$this->redirect(Translation :: get($success == true ? 'RightUpdated' : 'RightUpdateFailed'), !$success, array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_EDIT_LEARNING_OBJECT_RIGHTS, 'object' => $location->get_identifier()));
	}

	function lock_location()
	{
		$location = $this->location;
		$success = RightsUtilities :: switch_location_lock($location);

		if ($location->is_locked())
		{
			$true_message = 'LocationLocked';
			$false_message = 'LocactionNotLocked';
		}
		else
		{
			$true_message = 'LocationUnlocked';
			$false_message = 'LocactionNotUnlocked';
		}

		$this->redirect(Translation :: get($success == true ? $true_message : $false_message), !$success, array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_EDIT_LEARNING_OBJECT_RIGHTS, 'object' => $location->get_identifier()));
	}

	function inherit_location()
	{
		$location = $this->location;

		$success = RightsUtilities :: switch_location_inherit($location);
		$this->redirect(Translation :: get($success == true ? 'LocationUpdated' : 'LocationNotUpdated'), !$success, array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_EDIT_LEARNING_OBJECT_RIGHTS, 'object' => $location->get_identifier()));
	}

	function show_rights_list()
	{
        $object = Request :: get(RepositoryManager :: PARAM_LEARNING_OBJECT_ID);
        $rdm = RepositoryDataManager::get_instance();
        $lo = $rdm->retrieve_learning_object($object);
		$trail = new BreadcrumbTrail(false);
        $trail->add(new Breadcrumb($this->get_url(array(RepositoryManager::PARAM_ACTION => RepositoryManager::ACTION_VIEW_LEARNING_OBJECTS, RepositoryManager::PARAM_LEARNING_OBJECT_ID => $object)), $lo->get_title()));
        $trail->add(new Breadcrumb($this->get_url(array(RepositoryManager::PARAM_LEARNING_OBJECT_ID => $object)), Translation :: get('EditRights')));

			$this->display_header($trail, false, true, 'repository rights');
			echo $this->get_modification_links();
			echo $this->get_rights_table_html();
			echo RightsUtilities :: get_rights_legend();
			$this->display_footer();
	}
}
?>