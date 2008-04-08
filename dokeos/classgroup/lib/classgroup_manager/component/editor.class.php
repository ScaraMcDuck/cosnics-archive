<?php

require_once dirname(__FILE__).'/../classgroupmanager.class.php';
require_once dirname(__FILE__).'/../classgroupmanagercomponent.class.php';
require_once dirname(__FILE__).'/../classgroupform.class.php';
require_once dirname(__FILE__).'/../../classgroupdatamanager.class.php';

class ClassGroupManagerEditorComponent extends ClassGroupManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{	
		$breadcrumbs = array();
		$breadcrumbs[] = array ('url' => $this->get_url(array(ClassGroupManager :: PARAM_ACTION => ClassGroupManager :: ACTION_BROWSE_CLASSGROUPS)), 'name' => Translation :: get('ClassGroups'));
		$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => Translation :: get('ClassGroupUpdate'));
		$id = $_GET[ClassGroupManager :: PARAM_CLASSGROUP_ID];
		if ($id)
		{
			$classgroup = $this->retrieve_classgroup($id);
			$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => $classgroup->get_name());
		
			if (!$this->get_user()->is_platform_admin())
			{
				$this->display_header();
				Display :: display_error_message(Translation :: get("NotAllowed"));
				$this->display_footer();
				exit;
			}
			
			$form = new ClassGroupForm(ClassGroupForm :: TYPE_EDIT, $classgroup, $this->get_url(array(ClassGroupManager :: PARAM_CLASSGROUP_ID => $id)));

			if($form->validate())
			{
				$success = $form->update_classgroup();
				$this->redirect('url', Translation :: get($success ? 'ClassGroupUpdated' : 'ClassGroupNotUpdated'), ($success ? false : true), array(ClassGroupManager :: PARAM_ACTION => ClassGroupManager :: ACTION_BROWSE_CLASSGROUPS));
			}
			else
			{
				$this->display_header($breadcrumbs);
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