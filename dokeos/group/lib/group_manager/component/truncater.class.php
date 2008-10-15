<?php
require_once dirname(__FILE__).'/../group_manager.class.php';
require_once dirname(__FILE__).'/../group_manager_component.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';

class GroupManagerTruncaterComponent extends GroupManagerComponent
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
			$trail->add(new Breadcrumb($this->get_url(), Translation :: get('EmptyGroup')));
			
			$this->display_header($trail);
			Display :: display_error_message(Translation :: get("NotAllowed"));
			$this->display_footer();
			exit;
		}
		
		
		$ids = $_GET[GroupManager :: PARAM_GROUP_ID];
		$failures = 0;
		
		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}
			
			foreach ($ids as $id)
			{
				$classgroup = $this->retrieve_classgroup($id);
				if (!$classgroup->truncate())
				{
					$failures++;
				}
				else
				{
					Events :: trigger_event('empty', 'group', array('target_group_id' => $classgroup->get_id(), 'action_user_id' => $user->get_id()));
				}
			}
			
			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedGroupNotEmptied';
				}
				else
				{
					$message = 'SelectedGroupsNotEmptied';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedGroupEmptied';
				}
				else
				{
					$message = 'SelectedGroupsEmptied';
				}
				
			}
			
			if(count($ids) == 1)
				$this->redirect('url', Translation :: get($message), ($failures ? true : false), array(GroupManager :: PARAM_ACTION => GroupManager :: ACTION_VIEW_GROUP, GroupManager :: PARAM_GROUP_ID => $ids[0]));
			else
				$this->redirect('url', Translation :: get($message), ($failures ? true : false), array(GroupManager :: PARAM_ACTION => GroupManager :: ACTION_BROWSE_GROUPS));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoGroupSelected')));
		}
	}
}
?>