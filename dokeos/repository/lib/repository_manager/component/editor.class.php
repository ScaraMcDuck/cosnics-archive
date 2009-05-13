<?php
/**
 * $Id$
 * @package repository.repositorymanager
 * 
 * @author Bart Mollet
 * @author Tim De Pauw
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__).'/../repository_manager.class.php';
require_once dirname(__FILE__).'/../repository_manager_component.class.php';
require_once dirname(__FILE__).'/../../learning_object_form.class.php';
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
		$trail = new BreadcrumbTrail(false);
		
		$id = $_GET[RepositoryManager :: PARAM_LEARNING_OBJECT_ID];
		if ($id)
		{
			$object = $this->retrieve_learning_object($id);
			// TODO: Roles & Rights.
			if ($object->get_owner_id() != $this->get_user_id())
			{
				$this->not_allowed();
			}
			elseif (!$object->is_latest_version())
			{
				$this->redirect(RepositoryManager :: ACTION_BROWSE_LEARNING_OBJECTS, Translation :: get('EditNotAllowed'), $object->get_parent_id(), true);
			}
			$form = LearningObjectForm :: factory(LearningObjectForm :: TYPE_EDIT, $object, 'edit', 'post', $this->get_url(array (RepositoryManager :: PARAM_LEARNING_OBJECT_ID => $id)));
			if ($form->validate())
			{
				$success = $form->update_learning_object();
				$category_id = $object->get_parent_id();
				$this->redirect(RepositoryManager :: ACTION_BROWSE_LEARNING_OBJECTS, Translation :: get($success == LearningObjectForm :: RESULT_SUCCESS ? 'ObjectUpdated' : 'ObjectUpdateFailed'), $category_id);
			}
			else
			{
                $trail->add(new Breadcrumb($this->get_url(array(RepositoryManager::PARAM_ACTION => RepositoryManager::ACTION_VIEW_LEARNING_OBJECTS,RepositoryManager::PARAM_LEARNING_OBJECT_ID => $id)), $object->get_title()));
				$trail->add(new Breadcrumb($this->get_url(array(RepositoryManager::PARAM_LEARNING_OBJECT_ID => $id)), Translation :: get('Edit')));
				$this->display_header($trail);
				$form->display();
				$this->display_footer();
			}
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
		}
	}
}
?>