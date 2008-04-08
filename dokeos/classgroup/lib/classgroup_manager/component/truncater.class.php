<?php
require_once dirname(__FILE__).'/../classgroupmanager.class.php';
require_once dirname(__FILE__).'/../classgroupmanagercomponent.class.php';

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
			$breadcrumbs = array();
			$breadcrumbs[] = array ('url' => $this->get_url(array(ClassGroupManager :: PARAM_ACTION => ClassGroupManager :: ACTION_BROWSE_CLASSGROUPS)), 'name' => Translation :: get('ClassGroups'));
			$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => Translation :: get('EmptyGroup'));
			
			$this->display_header($breadcrumbs);
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