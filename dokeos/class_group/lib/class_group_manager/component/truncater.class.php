<?php
require_once dirname(__FILE__).'/../class_group_manager.class.php';
require_once dirname(__FILE__).'/../class_group_manager_component.class.php';

class ClassGroupManagerTruncaterComponent extends ClassGroupManagerComponent
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
			$trail->add(new Breadcrumb($this->get_url(), Translation :: get('EmptyGroup')));
			
			$this->display_header($trail);
			Display :: display_error_message(Translation :: get("NotAllowed"));
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
				$classgroup = $this->retrieve_classgroup($id);
				if (!$classgroup->truncate())
				{
					$failures++;
				}
				else
				{
					Events :: trigger_event('empty', 'class_group', array('target_class_group_id' => $classgroup->get_id(), 'action_user_id' => $user->get_user_id()));
				}
			}
			
			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedClassGroupNotEmptied';
				}
				else
				{
					$message = 'SelectedClassGroupsNotEmptied';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedClassGroupEmptied';
				}
				else
				{
					$message = 'SelectedClassGroupsEmptied';
				}
				
			}
			
			$this->redirect('url', Translation :: get($message), ($failures ? true : false), array(ClassGroupManager :: PARAM_ACTION => ClassGroupManager :: ACTION_BROWSE_CLASSGROUPS));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoClassGroupSelected')));
		}
	}
}
?>