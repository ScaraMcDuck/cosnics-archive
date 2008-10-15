<?php

require_once dirname(__FILE__).'/../group_manager.class.php';
require_once dirname(__FILE__).'/../group_manager_component.class.php';
require_once dirname(__FILE__).'/../group_form.class.php';
require_once dirname(__FILE__).'/../../group_data_manager.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';

class GroupManagerEditorComponent extends GroupManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{	
		$trail = new BreadcrumbTrail();
		$admin = new AdminManager();
		$trail->add(new Breadcrumb($admin->get_link(array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER)), Translation :: get('Administration')));
		$trail->add(new Breadcrumb($this->get_url(array(GroupManager :: PARAM_ACTION => GroupManager :: ACTION_BROWSE_GROUPS)), Translation :: get('GroupList')));
		
		$id = $_GET[GroupManager :: PARAM_GROUP_ID];
		if ($id)
		{
			$classgroup = $this->retrieve_classgroup($id);
			$trail->add(new Breadcrumb($this->get_url(array(GroupManager :: PARAM_ACTION => GroupManager :: ACTION_VIEW_GROUP, GroupManager :: PARAM_GROUP_ID => $_GET[GroupManager :: PARAM_GROUP_ID])), $classgroup->get_name()));
			$trail->add(new Breadcrumb($this->get_url(), Translation :: get('GroupUpdate')));
		
			if (!$this->get_user()->is_platform_admin())
			{
				$this->display_header();
				Display :: display_error_message(Translation :: get("NotAllowed"));
				$this->display_footer();
				exit;
			}
			
			$form = new GroupForm(GroupForm :: TYPE_EDIT, $classgroup, $this->get_url(array(GroupManager :: PARAM_GROUP_ID => $id)), $this->get_user());

			if($form->validate())
			{
				$success = $form->update_classgroup();
				$classgroup = $form->get_classgroup();
				$this->redirect('url', Translation :: get($success ? 'GroupUpdated' : 'GroupNotUpdated'), ($success ? false : true), array(GroupManager :: PARAM_ACTION => GroupManager :: ACTION_VIEW_GROUP, GroupManager :: PARAM_GROUP_ID => $classgroup->get_id()));
			}
			else
			{
				$this->display_header($trail);
				$form->display();
				$this->display_footer();
			}
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoGroupSelected')));
		}
	}
}
?>