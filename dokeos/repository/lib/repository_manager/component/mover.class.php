<?php
require_once dirname(__FILE__).'/../repositorymanager.class.php';
require_once dirname(__FILE__).'/../repositorymanagercomponent.class.php';
require_once dirname(__FILE__).'/../../learningobjectcategorymenu.class.php';
require_once dirname(__FILE__).'/../../optionsmenurenderer.class.php';
require_once dirname(__FILE__).'/../../../../claroline/inc/lib/formvalidator/FormValidator.class.php';

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
					$new_category = 0;
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
					$new_category = $destination;
				}
				$this->return_to_browser(get_lang($message), $new_category);
			}
			else
			{
				$renderer = clone $form->defaultRenderer();
				$renderer->setElementTemplate('{label} {element} ');
				$form->accept($renderer);
				$this->display_header();
				$this->display_popup_form($renderer->toHTML());
				$this->display_footer();
			}
		}
		else
		{
			$this->display_error_page(get_lang('NoObjectSelected'));
		}
	}

	private function get_categories_for_select($exclude = array())
	{
		$cm = new LearningObjectCategoryMenu($this->get_user_id(), $this->get_root_category_id());
		$renderer = new OptionsMenuRenderer($exclude);
		$cm->render($renderer, 'sitemap');
		return $renderer->toArray('id');
	}
}
?>