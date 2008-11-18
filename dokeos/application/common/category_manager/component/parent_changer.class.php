<?php
/**
 * @package reservations.lib.reservationsmanager.component
 */
require_once dirname(__FILE__).'/../category_manager.class.php';
require_once dirname(__FILE__).'/../category_manager_component.class.php';
require_once dirname(__FILE__).'/../platform_category.class.php';

class CategoryManagerParentChangerComponent extends CategoryManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$user = $this->get_user();

		$ids = $_GET[CategoryManager :: PARAM_CATEGORY_ID];
		
		if (!$user)
		{
			$this->display_header(null);
			Display :: display_error_message(Translation :: get("NotAllowed"));
			$this->display_footer();
			exit;
		}
		
		if (!is_array($ids))
		{
			$ids = array ($ids);
		}
		
		if(count($ids) != 0)
		{ 
			$bool = true;
			$parent = $this->retrieve_categories(new EqualityCondition(PlatformCategory :: PROPERTY_ID, $ids[0]))->next_result()->get_parent();
		
			$form = $this->get_move_form($ids, $parent);

			$success = true;

			if($form->validate())
			{
				$new_parent = $form->exportValue('category');
				foreach($ids as $id)
				{ 
					$category = $this->retrieve_categories(new EqualityCondition(PlatformCategory :: PROPERTY_ID, $id))->next_result();
					$category->set_parent($new_parent);
					$category->set_display_order($this->get_next_category_display_order($new_parent));
					$success &= $category->update();
				}
				
				$this->clean_display_order_old_parent($parent);
				
				if(get_class($this->get_parent()) == 'RepositoryCategoryManager')
					$this->repository_redirect(RepositoryManager :: ACTION_MANAGE_CATEGORIES, Translation :: get($success ? 'CategoryCreated' : 'CategoryNotCreated'), 0, ($success ? false : true), array(CategoryManager :: PARAM_ACTION => CategoryManager :: ACTION_BROWSE_CATEGORIES, CategoryManager :: PARAM_CATEGORY_ID => $parent));
				else
					$this->redirect('url', Translation :: get($success ? 'CategoryMoved' : 'CategoryNotMoved'), ($success ? false : true), array(CategoryManager :: PARAM_ACTION => CategoryManager :: ACTION_BROWSE_CATEGORIES, CategoryManager :: PARAM_CATEGORY_ID => $parent));
			}
			else
			{
				$this->display_header(new BreadcrumbTrail());
				$form->display();
				$this->display_footer();
			}
		}
		else
		{
			$this->display_header(null);
			Display :: display_error_message(Translation :: get("NoObjectSelected"));
			$this->display_footer();
		}
	}
	private $tree;
	
	function get_move_form($selected_categories, $current_parent)
	{
		if($current_parent != 0)
			$this->tree[0] = Translation :: get('Root');
	
		$this->build_category_tree(0, $selected_categories, $current_parent);
		$form = new FormValidator('select_category', 'post', $this->get_url(array(CategoryManager :: PARAM_ACTION => CategoryManager :: ACTION_CHANGE_CATEGORY_PARENT, CategoryManager :: PARAM_CATEGORY_ID => $_GET[CategoryManager :: PARAM_CATEGORY_ID])));
		$form->addElement('select','category',Translation :: get('Category'),$this->tree);
		$form->addElement('submit', 'submit', Translation :: get('Ok'));
		return $form;
	}
	
	private $level = 1;
	
	function build_category_tree($parent_id, $selected_categories, $current_parent)
	{
		$conditions[] = new EqualityCondition(LearningObjectPublicationCategory :: PROPERTY_PARENT, $parent_id);
		$conditions[] = new NotCondition(new EqualityCondition(LearningObjectPublicationCategory :: PROPERTY_ID, $current_parent));
		
		foreach($selected_categories as $selected_category)
			$conditions[] = new NotCondition(new EqualityCondition(LearningObjectPublicationCategory :: PROPERTY_ID, $selected_category));
			
		$condition = new AndCondition($conditions);

		$categories = $this->retrieve_categories($condition);
		
		$tree = array();
		while($cat = $categories->next_result())
		{
			$this->tree[$cat->get_id()] = str_repeat('--', $this->level) . ' ' . $cat->get_name();
			$this->level++;
			$this->build_category_tree($cat->get_id(),$selected_categories, $current_parent);
			$this->level--;
		}
	}
	
	function clean_display_order_old_parent($parent)
	{
		$condition = new EqualityCondition(LearningObjectPublicationCategory :: PROPERTY_PARENT, $parent);
		$categories = $this->retrieve_categories($condition);
		
		$i = 1;
		
		while($cat = $categories->next_result())
		{
			$cat->set_display_order($i);
			$cat->update();
			$i++;
		}
	}
	
}
?>