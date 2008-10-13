<?php
/**
 * @package application.lib.encyclopedia.encyclopedia_manager
 */
require_once dirname(__FILE__).'/../class_group_manager.class.php';
require_once dirname(__FILE__).'/../class_group_manager_component.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';

class ClassGroupManagerDeleterComponent extends ClassGroupManagerComponent
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
			$trail->add(new Breadcrumb($this->get_url(array(ClassGroupManager :: PARAM_ACTION => ClassGroupManager :: ACTION_BROWSE_CLASSGROUPS)), Translation :: get('ClassGroupList')));
			$trail->add(new Breadcrumb($this->get_url(), Translation :: get('DeleteGroup')));
			
			$this->display_header($trail);
			Display :: display_error_message(Translation :: get('NotAllowed'));
			$this->display_footer();
			exit;
		}		
		
		$ids = $_GET[ClassGroupManager :: PARAM_CLASSGROUP_ID];
		$failures = 0;
		
		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}
			
			foreach ($ids as $id)
			{
				$group = $this->retrieve_classgroup($id);
				
				if (!$group->delete())
				{
					$failures++;
				}
				else
				{
					Events :: trigger_event('delete', 'class_group', array('target_class_group_id' => $group->get_id(), 'action_user_id' => $user->get_id()));
				}
			}
			
			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedClassGroupDeleted';
				}
				else
				{
					$message = 'SelectedClassGroupDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedClassGroupsDeleted';
				}
				else
				{
					$message = 'SelectedClassGroupsDeleted';
				}
			}
			
			$this->redirect('url', Translation :: get($message), ($failures ? true : false), array(ClassGroupManager :: PARAM_ACTION => ClassGroupManager :: ACTION_BROWSE_CLASSGROUPS));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoClassGroupsSelected')));
		}
	}
}
?>