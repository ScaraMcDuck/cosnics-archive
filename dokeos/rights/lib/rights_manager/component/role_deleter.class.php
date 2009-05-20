<?php
require_once dirname(__FILE__).'/../rights_manager.class.php';
require_once dirname(__FILE__).'/../rights_manager_component.class.php';
require_once dirname(__FILE__).'/role_browser_table/role_browser_table.class.php';

class RightsManagerRoleDeleterComponent extends RightsManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[RightsManager :: PARAM_ROLE_ID];
		$failures = 0;
		
		if (!empty($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}
			
			foreach ($ids as $id)
			{
				$role = $this->retrieve_role($id);
				
				if (!$role->delete())
				{
					$failures++;
				}
			}
			
			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedRoleDeleted';
				}
				else
				{
					$message = 'SelectedRoleDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedRolesDeleted';
				}
				else
				{
					$message = 'SelectedRolesDeleted';
				}
			}
			
			$this->redirect(Translation :: get($message), ($failures ? true : false), array(RightsManager :: PARAM_ACTION => RightsManager :: ACTION_BROWSE_ROLES));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoRoleSelected')));
		}
	}
}
?>