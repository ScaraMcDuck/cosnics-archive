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
require_once dirname(__FILE__).'/../../learning_object_category_menu.class.php';
require_once Path :: get_library_path() . 'html/menu/options_menu_renderer.class.php';
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
/**
 * Repository manager component to move learning objects between categories in
 * the repository.
 */
class RepositoryManagerMoverComponent extends RepositoryManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		
		$ids = $_GET[RepositoryManager :: PARAM_LEARNING_OBJECT_ID];
		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}
			
			$object = $this->retrieve_learning_object($ids[0]);
			$parent = $object->get_parent_id();
			
			$this->tree = array();
			if($parent != 0)
				$this->tree[] = Translation :: get('Repository');
			
			$this->get_categories_for_select(0, $parent);
			$form = new FormValidator('move', 'post', $this->get_url(array (RepositoryManager :: PARAM_LEARNING_OBJECT_ID => $ids)));
			$form->addElement('select', RepositoryManager :: PARAM_DESTINATION_LEARNING_OBJECT_ID, Translation :: get('NewCategory'), $this->tree);
			$form->addElement('submit', 'submit', Translation :: get('Move'));
			if ($form->validate())
			{
				$destination = $form->exportValue(RepositoryManager :: PARAM_DESTINATION_LEARNING_OBJECT_ID);
				$failures = 0;
				foreach ($ids as $id)
				{
					$object = $this->retrieve_learning_object($id);
					$versions = $this->get_version_ids($object);
					
					foreach ($versions as $version)
					{
						$object = $this->retrieve_learning_object($version);
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
				}
				
				// TODO: SCARA - Correctto reflect possible version errors
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
				$this->redirect(RepositoryManager :: ACTION_BROWSE_LEARNING_OBJECTS, Translation :: get($message), ($failures ? null : $destination));
			}
			else
			{
				//$renderer = clone $form->defaultRenderer();
				//$renderer->setElementTemplate('{label} {element} ');
				//$form->accept($renderer);
				
				$trail->add(new Breadcrumb($this->get_url(), Translation :: get('Move')));
				$this->display_header($trail);
				echo $form->toHTML();
				$this->display_footer();
			}
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
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
	 
	 private $level = 1;
	 private $tree = array();
	 
	private function get_categories_for_select($parent_id, $current_parent)
	{
		$conditions[] = new EqualityCondition(PlatformCategory :: PROPERTY_PARENT, $parent_id);
		$conditions[] = new NotCondition(new EqualityCondition(PlatformCategory :: PROPERTY_ID, $current_parent));
			
		$condition = new AndCondition($conditions);

		$categories = $this->retrieve_categories($condition);
		
		$tree = array();
		while($cat = $categories->next_result())
		{
			$this->tree[$cat->get_id()] = str_repeat('--', $this->level) . ' ' . $cat->get_name();
			$this->level++;
			$this->get_categories_for_select($cat->get_id(), $current_parent);
			$this->level--;
		}
	}
}
?>