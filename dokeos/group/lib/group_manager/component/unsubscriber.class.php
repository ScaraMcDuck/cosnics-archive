<?php
/**
 * @package application.lib.encyclopedia.encyclopedia_manager
 */
require_once dirname(__FILE__).'/../group_manager.class.php';
require_once dirname(__FILE__).'/../group_manager_component.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';

class GroupManagerUnsubscriberComponent extends GroupManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$user = $this->get_user();

		if (!$user->is_platform_admin())
		{
			$trail = new BreadcrumbTrail();
			$admin = new AdminManager();
			$trail->add(new Breadcrumb($admin->get_link(array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER)), Translation :: get('Administration')));
			$trail->add(new Breadcrumb($this->get_url(array(GroupManager :: PARAM_ACTION => GroupManager :: ACTION_BROWSE_GROUPS)), Translation :: get('GroupList')));
			$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UnsubscribeFromGroup')));
			
			$this->display_header($trail);
			Display :: display_error_message(Translation :: get('NotAllowed'));
			$this->display_footer();
			exit;
		}		
		
		$ids = $_GET[GroupManager :: PARAM_GROUP_REL_USER_ID];
		$failures = 0;
		
		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}
			
			foreach ($ids as $id)
			{
				$classgroupreluser_ids = explode('|', $id);
				$classgroupreluser = $this->retrieve_classgroup_rel_user($classgroupreluser_ids[1], $classgroupreluser_ids[0]);

				if(!isset($classgroupreluser)) continue;
				
				if ($classgroupreluser_ids[0] == $classgroupreluser->get_classgroup_id())
				{
					if (!$classgroupreluser->delete())
					{
						$failures++;
					}
					else
					{
						Events :: trigger_event('unsubscribe_user', 'group', array('target_group_id' => $classgroupreluser->get_classgroup_id(), 'target_user_id' => $classgroupreluser->get_user_id(), 'action_user_id' => $user->get_id()));
					}
				}
				else
				{
					$failures++;
				}
			}
			
			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedGroupRelUserNotDeleted';
				}
				else
				{
					$message = 'SelectedGroupRelUsersNotDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedGroupRelUserDeleted';
				}
				else
				{
					$message = 'SelectedGroupRelUsersDeleted';
				}
			}
			
			$this->redirect('url', Translation :: get($message), ($failures ? true : false), array(GroupManager :: PARAM_ACTION => GroupManager :: ACTION_VIEW_GROUP, GroupManager :: PARAM_GROUP_ID => $classgroupreluser_ids[0]));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoGroupRelUserSelected')));
		}
	}
}
?>