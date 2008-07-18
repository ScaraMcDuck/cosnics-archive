<?php
/**
 * @package application.lib.encyclopedia.encyclopedia_manager
 */
require_once dirname(__FILE__).'/../class_group_manager.class.php';
require_once dirname(__FILE__).'/../class_group_manager_component.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';

class ClassGroupManagerSubscriberComponent extends ClassGroupManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{ 
		$user = $this->get_user();
		$classgroup_id = $_GET[ClassGroupManager :: PARAM_CLASSGROUP_ID];
		if (!$user->is_platform_admin())
		{
			$trail = new BreadcrumbTrail();
			$admin = new Admin();
			$trail->add(new Breadcrumb($admin->get_link(array(Admin :: PARAM_ACTION => Admin :: ACTION_ADMIN_BROWSER)), Translation :: get('Administration')));
			$trail->add(new Breadcrumb($this->get_url(array(ClassGroupManager :: PARAM_ACTION => ClassGroupManager :: ACTION_BROWSE_CLASSGROUPS)), Translation :: get('ClassGroupList')));
			$trail->add(new Breadcrumb($this->get_url(), Translation :: get('SubscribeToClassGroup')));
			
			$this->display_header($trail);
			Display :: display_error_message(Translation :: get('NotAllowed'));
			$this->display_footer();
			exit;
		}		
		
		$users = $_GET[ClassGroupManager :: PARAM_USER_ID];

		$failures = 0;
		
		if (!empty ($users))
		{
			if (!is_array($users))
			{
				$users = array ($users);
			}
			
			foreach($users as $user)
			{ 
				$existing_groupreluser = $this->retrieve_classgroup_rel_user($user, $classgroup_id);
				
				if (!isset($existing_groupreluser))
				{ 
					$groupreluser = new ClassGroupRelUser();
					$groupreluser->set_classgroup_id($classgroup_id);
					$groupreluser->set_user_id($user);
					
					if (!$groupreluser->create())
					{
						$failures++;
					}
					else
					{
						Events :: trigger_event('subscribe_user', 'class_group', array('target_class_group_id' => $groupreluser->get_classgroup_id(), 'target_user_id' => $groupreluser->get_user_id(), 'action_user_id' => $this->get_user()->get_id()));
					}
				}
				else
				{
					$contains_dupes = true;
				}
			}
			
			if ($failures)
			{
				if (count($users) == 1)
				{
					$message = 'SelectedUserNotAddedToGroup' . ($contains_dupes ? 'Dupes' : '');
				}
				else
				{
					$message = 'SelectedUsersNotAddedToGroup' . ($contains_dupes ? 'Dupes' : '');
				}
			}
			else
			{
				if (count($users) == 1)
				{
					$message = 'SelectedUserAddedToGroup' . ($contains_dupes ? 'Dupes' : '');
				}
				else
				{
						$message = 'SelectedUsersAddedToGroup' . ($contains_dupes ? 'Dupes' : '');
				}
			}
		
			$this->redirect('url', Translation :: get($message), ($failures ? true : false), array(ClassGroupManager :: PARAM_ACTION => ClassGroupManager :: ACTION_VIEW_CLASSGROUP, ClassGroupManager :: PARAM_CLASSGROUP_ID => $classgroup_id));
			exit;
			break;
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoClassGroupRelUserSelected')));
		}
	}
}
?>