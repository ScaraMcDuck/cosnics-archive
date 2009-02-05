<?php
/**
 */
require_once dirname(__FILE__).'/../category_manager.class.php';
require_once dirname(__FILE__).'/../category_manager_component.class.php';

class CategoryManagerAjaxCategoryMoverComponent extends CategoryManagerComponent
{
	function run()
	{
		$source = $_POST['source'];
		$target = $_POST['target'];
		
		if(!isset($source) || !isset($target)) exit;
		
		//echo $_POST['target'] . ' ' . $_POST['source'];
		
		$category = $this->retrieve_categories(new EqualityCondition('id', $source))->next_result();
		$old_parent = $category->get_parent();
		$category->set_parent($target);
		$category->set_display_order($this->get_next_category_display_order($target));
		$category->update();
		
		$counter = 1;
		
		$categories = $this->retrieve_categories(new EqualityCondition('parent', $old_parent), null, null, array('display_order'), array(SORT_ASC));
		while($cat = $categories->next_result())
		{
			$cat->set_display_order($counter);
			$cat->update();
			$counter++;
		}
	}
}
?>