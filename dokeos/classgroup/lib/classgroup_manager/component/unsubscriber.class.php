<?php
/**
 * @package application.lib.encyclopedia.encyclopedia_manager
 */
require_once dirname(__FILE__).'/../classgroupmanager.class.php';
require_once dirname(__FILE__).'/../classgroupmanagercomponent.class.php';

class ClassgroupManagerUnsubscriberComponent extends ClassgroupManagerComponent
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
			$trail->add(new Breadcrumb($this->get_url(array(ClassgroupManager :: PARAM_ACTION => ClassgroupManager :: ACTION_BROWSE_CLASSGROUPS)), Translation :: get('Groups')));
			$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UnsubscribeFromGroup')));
			
			$this->display_header($trail);
			Display :: display_error_message(Translation :: get('NotAllowed'));
			$this->display_footer();
			exit;
		}		
		
		$ids = $_GET[ClassgroupManager :: PARAM_CLASSGROUP_REL_USER_ID];
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
				$classgroupreluser = $this->retrieve_classgroup_rel_user($classgroupreluser_ids[1], $classgroupreluser_ids[2]);
				
				if ($classgroupreluser_ids[0] == $classgroupreluser->get_classgroup_id())
				{
					if (!$classgroupreluser->delete())
					{
						$failures++;
					}
					else
					{
						Events :: trigger_event('unsubscribe', 'classgroup', array('target_classgroup_id' => $classgroupreluser->get_classgroup_id(), 'target_user_id' => $classgroupreluser->get_user_id(), 'action_user_id' => $user->get_user_id()));
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
					$message = 'SelectedClassgroupRelUserNotDeleted';
				}
				else
				{
					$message = 'SelectedClassgroupRelUsersNotDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedClassgroupRelUserDeleted';
				}
				else
				{
					$message = 'SelectedClassgroupRelUsersDeleted';
				}
			}
			
			$this->redirect('url', Translation :: get($message), ($failures ? true : false), array(ClassgroupManager :: PARAM_ACTION => ClassgroupManager :: ACTION_VIEW_CLASSGROUP, ClassgroupManager :: PARAM_CLASSGROUP_ID => $classgroupreluser_ids[0]));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoClassgroupRelUserSelected')));
		}
	}
}
?>