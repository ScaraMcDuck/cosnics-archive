<?php
require_once dirname(__FILE__).'/../repositorymanager.class.php';
require_once dirname(__FILE__).'/../repositorymanagercomponent.class.php';
require_once dirname(__FILE__).'/../../learningobjectform.class.php';

class RepositoryManagerEditorComponent extends RepositoryManagerComponent
{
	function run()
	{
		$id = $_GET[RepositoryManager :: PARAM_LEARNING_OBJECT_ID];
		if ($id)
		{
			$object = $this->retrieve_learning_object($id);
			// TODO: Roles & Rights.
			if ($object->get_owner_id() != $this->get_user_id())
			{
				$this->not_allowed();
			}
			$form = LearningObjectForm :: factory(LearningObjectForm :: TYPE_EDIT, $object, 'edit', 'post', $this->get_url(array (RepositoryManager :: PARAM_LEARNING_OBJECT_ID => $id)));
			if ($form->validate())
			{
				$success = $form->update_learning_object();
				$category_id = $object->get_parent_id();
				$this->return_to_browser(get_lang($success ? 'ObjectUpdated' : 'ObjectUpdateFailed'), $category_id);
			}
			else
			{
				$this->display_header();
				$form->display();
				$this->display_footer();
			}
		}
		else
		{
			$this->display_error_page(get_lang('NoObjectSelected'));
		}
	}
}
?>