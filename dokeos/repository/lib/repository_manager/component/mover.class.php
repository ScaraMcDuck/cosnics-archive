<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../repositorymanager.class.php';
require_once dirname(__FILE__).'/../repositorymanagercomponent.class.php';
require_once dirname(__FILE__).'/../../learningobjectcategorymenu.class.php';
require_once dirname(__FILE__).'/../../optionsmenurenderer.class.php';
require_once dirname(__FILE__).'/../../../../claroline/inc/lib/formvalidator/FormValidator.class.php';
/**
 * Repository manager component to move learning objects between categories in
 * the repository.
 */
class RepositoryManagerMoverComponent extends RepositoryManagerComponent
{
	function run()
	{
		$ids = $_GET[RepositoryManager :: PARAM_LEARNING_OBJECT_ID];
		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}
			$categories = $this->get_categories_for_select($ids);
			$form = new FormValidator('move', 'post', $this->get_url(array (RepositoryManager :: PARAM_LEARNING_OBJECT_ID => $ids)));
			$form->addElement('select', RepositoryManager :: PARAM_DESTINATION_LEARNING_OBJECT_ID, get_lang('NewCategory'), $categories);
			$form->addElement('submit', 'submit', get_lang('Move'));
			if ($form->validate())
			{
				$destination = $form->exportValue(RepositoryManager :: PARAM_DESTINATION_LEARNING_OBJECT_ID);
				$failures = 0;
				foreach ($ids as $id)
				{
					$object = $this->retrieve_learning_object($id);
					// TODO: Roles & Rights.
					if ($object->get_owner_id() != $this->get_user_id())
					{
						$failures++;
					}
					elseif ($object->get_parent_id() != $destination)
					{
						if (!$object->move_allowed($destination))
						{
							$failures++;
						}
						else
						{
							$object->set_parent_id($destination);
							$object->update(false);
						}
					}
				}
				if ($failures)
				{
					if (count($ids) == 1)
					{
						$message = 'SelectedObjectNotMoved';
					}
					else
					{
						$message = 'NotAllSelectedObjectsMoved';
					}
				}
				else
				{
					if (count($ids) == 1)
					{
						$message = 'SelectedObjectMoved';
					}
					else
					{
						$message = 'AllSelectedObjectsMoved';
					}
				}
				$this->redirect(RepositoryManager :: ACTION_BROWSE_LEARNING_OBJECTS, get_lang($message));
			}
			else
			{
				$renderer = clone $form->defaultRenderer();
				$renderer->setElementTemplate('{label} {element} ');
				$form->accept($renderer);
				$breadcrumbs = array(array('url' => $this->get_url(), 'name' => get_lang('Move')));
				$this->display_header($breadcrumbs);
				$this->display_popup_form($renderer->toHTML());
				$this->display_footer();
			}
		}
		else
		{
			$this->display_error_page(htmlentities(get_lang('NoObjectSelected')));
		}
	}
	/**
	 * Get all categories from which a user can select a target category when
	 * moving learning objects.
	 * @param array $exclude An array of category-id's which should be excluded
	 * from the resulting list.
	 * @return array A list of possible categories from which a user can choose.
	 * Can be used as input for a QuickForm select field.
	 */
	private function get_categories_for_select($exclude = array())
	{
		$cm = new LearningObjectCategoryMenu($this->get_user_id());
		$renderer = new OptionsMenuRenderer($exclude);
		$cm->render($renderer, 'sitemap');
		return $renderer->toArray();
	}
}
?>