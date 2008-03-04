<?php
/**
 * $Id: course_settingstool.class.php 9222 2006-09-15 09:19:38Z bmol $
 * Course maintenance tool
 * @package application.weblcms.tool
 * @subpackage maintenance
 */
/**
 * This class implements the action to take after the user has completed a
 * course maintenance wizard
 */
class SubscribeWizardProcess extends HTML_QuickForm_Action
{
	/**
	 * The repository tool in which the wizard runs.
	 */
	private $parent;
	/**
	 * Constructor
	 * @param RepositoryTool $parent The repository tool in which the wizard
	 * runs.
	 */
	public function SubscribeWizardProcess($parent)
	{
		$this->parent = $parent;
	}
	function perform(& $page, $actionName)
	{
		$values = $page->controller->exportValues();
		//Todo: Split this up in several form-processing classes depending on selected action
		switch ($values['action'])
		{
			case ActionSelectionSubscribeWizardPage :: ACTION_SUBSCRIBE :
				$users = $values['users'];
				$group_id = $values['Group'];
				$failures = 0;
				$contains_dupes = false;
				
				foreach($users as $user)
				{
					$location_id = $values['User-'.$user];
					$existing_groupreluser = $this->parent->retrieve_group_rel_user($user, $location_id);
					
					if (!isset($existing_groupreluser))
					{
						$groupreluser = new GroupRelUser();
						$groupreluser->set_group_id($group_id);
						$groupreluser->set_location_id($location_id);
						$groupreluser->set_user_id($user);
						if (!$groupreluser->create())
						{
							$failures++;
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
			
				$this->parent->redirect('url', Translation :: get_lang($message), ($failures ? true : false), array(ClassGroupManager :: PARAM_ACTION => ClassGroupManager :: ACTION_BROWSE_CLASSGROUPS));
				exit;
				break;
		}
		$page->controller->container(true);
		$page->controller->run();
	}
}
?>