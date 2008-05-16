<?php

require_once dirname(__FILE__).'/../classgroupmanager.class.php';
require_once dirname(__FILE__).'/../classgroupmanagercomponent.class.php';
require_once dirname(__FILE__).'/../classgroupform.class.php';
require_once dirname(__FILE__).'/../../classgroupdatamanager.class.php';

class ClassgroupManagerEditorComponent extends ClassgroupManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{	
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(ClassgroupManager :: PARAM_ACTION => ClassgroupManager :: ACTION_BROWSE_CLASSGROUPS)), Translation :: get('Groups')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('ClassgroupUpdate')));
		
		$id = $_GET[ClassgroupManager :: PARAM_CLASSGROUP_ID];
		if ($id)
		{
			$classgroup = $this->retrieve_classgroup($id);
			$trail->add(new Breadcrumb($this->get_url(), $classgroup->get_name()));
		
			if (!$this->get_user()->is_platform_admin())
			{
				$this->display_header();
				Display :: display_error_message(Translation :: get("NotAllowed"));
				$this->display_footer();
				exit;
			}
			
			$form = new ClassgroupForm(ClassgroupForm :: TYPE_EDIT, $classgroup, $this->get_url(array(ClassgroupManager :: PARAM_CLASSGROUP_ID => $id)), $this->get_user());

			if($form->validate())
			{
				$success = $form->update_classgroup();
				$this->redirect('url', Translation :: get($success ? 'ClassgroupUpdated' : 'ClassgroupNotUpdated'), ($success ? false : true), array(ClassgroupManager :: PARAM_ACTION => ClassgroupManager :: ACTION_BROWSE_CLASSGROUPS));
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
			$this->display_error_page(htmlentities(Translation :: get('NoClassgroupSelected')));
		}
	}
}
?>