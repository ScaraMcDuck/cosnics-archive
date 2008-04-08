<?php
/**
 * @package application.lib.encyclopedia.encyclopedia_manager
 */
require_once dirname(__FILE__).'/../classgroupmanager.class.php';
require_once dirname(__FILE__).'/../classgroupmanagercomponent.class.php';

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
			$breadcrumbs = array();
			$breadcrumbs[] = array ('url' => $this->get_url(array(ClassGroupManager :: PARAM_ACTION => ClassGroupManager :: ACTION_BROWSE_CLASSGROUPS)), 'name' => Translation :: get('ClassGroups'));
			$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => Translation :: get('EmptyGroup'));
			
			$this->display_header($breadcrumbs);
			Display :: display_error_message(Translation :: get("NotAllowed"));
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
				$classgroupreluser = $this->retrieve_classgroup_rel_user($classgroupreluser_ids[1], $classgroupreluser_ids[2]);
				
				if ($classgroupreluser_ids[0] == $classgroupreluser->get_classgroup_id())
				{
					if (!$classgroupreluser->delete())
					{
						$failures++;
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