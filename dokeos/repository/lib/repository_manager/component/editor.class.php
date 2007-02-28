<?php
/**
 * $Id$
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../repositorymanager.class.php';
require_once dirname(__FILE__).'/../repositorymanagercomponent.class.php';
require_once dirname(__FILE__).'/../../learningobjectform.class.php';
/**
 * Repository manager component to edit an existing learning object.
 */
class RepositoryManagerEditorComponent extends RepositoryManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
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
			elseif (!$this->learning_object_edit_allowed($object))
			{
				$this->redirect(RepositoryManager :: ACTION_BROWSE_LEARNING_OBJECTS, get_lang('EditNotAllowed'), $object->get_parent_id(), true);
			}
			$form = LearningObjectForm :: factory(LearningObjectForm :: TYPE_EDIT, $object, 'edit', 'post', $this->get_url(array (RepositoryManager :: PARAM_LEARNING_OBJECT_ID => $id)));
			if ($form->validate())
			{
				$success = $form->update_learning_object();
				$category_id = $object->get_parent_id();
				$this->redirect(RepositoryManager :: ACTION_BROWSE_LEARNING_OBJECTS, get_lang($success == LearningObjectForm :: RESULT_SUCCESS ? 'ObjectUpdated' : 'ObjectUpdateFailed'), $category_id);
			}
			else
			{
				$breadcrumbs = array(array('url' => $this->get_url(), 'name' => get_lang('Edit')));
				$this->display_header($breadcrumbs);
				$form->display();
				$this->display_footer();
			}
		}
		else
		{
			$this->display_error_page(htmlentities(get_lang('NoObjectSelected')));
		}
	}
}
?>