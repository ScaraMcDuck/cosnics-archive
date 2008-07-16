<?php
/**
 * @package application.lib.encyclopedia.encyclopedia_manager
 */
require_once dirname(__FILE__).'/../class_group_manager.class.php';
require_once dirname(__FILE__).'/../class_group_manager_component.class.php';

class ClassGroupManagerUnsubscriberComponent extends ClassGroupManagerComponent
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
			$trail->add(new Breadcrumb($this->get_url(array(ClassGroupManager :: PARAM_ACTION => ClassGroupManager :: ACTION_BROWSE_CLASSGROUPS)), Translation :: get('Groups')));
			$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UnsubscribeFromGroup')));
			
			$this->display_header($trail);
			Display :: display_error_message(Translation :: get('NotAllowed'));
			$this->display_footer();
			exit;
		}		
		
		$ids = $_GET[ClassGroupManager :: PARAM_CLASSGROUP_REL_USER_ID];
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
						Events :: trigger_event('unsubscribe_user', 'class_group', array('target_class_group_id' => $classgroupreluser->get_classgroup_id(), 'target_user_id' => $classgroupreluser->get_user_id(), 'action_user_id' => $user->get_id()));
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
					$message = 'SelectedClassGroupRelUserNotDeleted';
				}
				else
				{
					$message = 'SelectedClassGroupRelUsersNotDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedClassGroupRelUserDeleted';
				}
				else
				{
					$message = 'SelectedClassGroupRelUsersDeleted';
				}
			}
			
			$this->redirect('url', Translation :: get($message), ($failures ? true : false), array(ClassGroupManager :: PARAM_ACTION => ClassGroupManager :: ACTION_VIEW_CLASSGROUP, ClassGroupManager :: PARAM_CLASSGROUP_ID => $classgroupreluser_ids[0]));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoClassGroupRelUserSelected')));
		}
	}
}
?>