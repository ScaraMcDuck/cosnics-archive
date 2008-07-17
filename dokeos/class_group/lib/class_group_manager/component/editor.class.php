<?php

require_once dirname(__FILE__).'/../class_group_manager.class.php';
require_once dirname(__FILE__).'/../class_group_manager_component.class.php';
require_once dirname(__FILE__).'/../class_group_form.class.php';
require_once dirname(__FILE__).'/../../class_group_data_manager.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';

class ClassGroupManagerEditorComponent extends ClassGroupManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{	
		$trail = new BreadcrumbTrail();
		$admin = new Admin();
		$trail->add(new Breadcrumb($admin->get_link(array(Admin :: PARAM_ACTION => Admin :: ACTION_ADMIN_BROWSER)), Translation :: get('Administration')));
		$trail->add(new Breadcrumb($this->get_url(array(ClassGroupManager :: PARAM_ACTION => ClassGroupManager :: ACTION_BROWSE_CLASSGROUPS)), Translation :: get('ClassGroupList')));
		
		$id = $_GET[ClassGroupManager :: PARAM_CLASSGROUP_ID];
		if ($id)
		{
			$classgroup = $this->retrieve_classgroup($id);
			$trail->add(new Breadcrumb($this->get_url(array(ClassGroupManager :: PARAM_ACTION => ClassGroupManager :: ACTION_VIEW_CLASSGROUP, ClassGroupManager :: PARAM_CLASSGROUP_ID => $_GET[ClassGroupManager :: PARAM_CLASSGROUP_ID])), $classgroup->get_name()));
			$trail->add(new Breadcrumb($this->get_url(), Translation :: get('ClassGroupUpdate')));
		
			if (!$this->get_user()->is_platform_admin())
			{
				$this->display_header();
				Display :: display_error_message(Translation :: get("NotAllowed"));
				$this->display_footer();
				exit;
			}
			
			$form = new ClassGroupForm(ClassGroupForm :: TYPE_EDIT, $classgroup, $this->get_url(array(ClassGroupManager :: PARAM_CLASSGROUP_ID => $id)), $this->get_user());

			if($form->validate())
			{
				$success = $form->update_classgroup();
				$classgroup = $form->get_classgroup();
				$this->redirect('url', Translation :: get($success ? 'ClassGroupUpdated' : 'ClassGroupNotUpdated'), ($success ? false : true), array(ClassGroupManager :: PARAM_ACTION => ClassGroupManager :: ACTION_VIEW_CLASSGROUP, ClassGroupManager :: PARAM_CLASSGROUP_ID => $classgroup->get_id()));
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
			$this->display_error_page(htmlentities(Translation :: get('NoClassGroupSelected')));
		}
	}
}
?>