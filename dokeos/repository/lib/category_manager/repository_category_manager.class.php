<?php
/**
 * @package application.lib.profiler
 */
require_once Path :: get_application_library_path(). 'category_manager/category_manager.class.php';
require_once dirname(__FILE__) . '/../repository_data_manager.class.php';
require_once dirname(__FILE__) . '/repository_category.class.php';

class RepositoryCategoryManager extends CategoryManager
{
	function RepositoryCategoryManager($parent,$trail)
	{
		parent :: __construct($parent,$trail);
	}

	function get_category()
	{
		return new RepositoryCategory();
	}
	
	function count_categories($condition)
	{
		$wdm = RepositoryDataManager :: get_instance();
		
		if($condition)
			$conditions[] = $condition;
		$conditions[] = new EqualityCondition(RepositoryCategory :: PROPERTY_USER_ID, $this->get_user_id());
		$condition = new AndCondition($conditions);
		
		return $wdm->count_categories($condition);
	}
	
	function retrieve_categories($condition, $offset, $count, $order_property, $order_direction)
	{
		$wdm = RepositoryDataManager :: get_instance();
		
		if($condition)
		{
			$conditions[] = $condition;
		}
		
		$conditions[] = new EqualityCondition(RepositoryCategory :: PROPERTY_USER_ID, $this->get_user_id());
		$condition = new AndCondition($conditions);
		
		return $wdm->retrieve_categories($condition, $offset, $count, $order_property, $order_direction);
	}
	
	function get_next_category_display_order($parent_id)
	{
		$wdm = RepositoryDataManager :: get_instance();
		return $wdm->select_next_category_display_order($parent_id, Session :: get_user_id());
	}
	
	function allowed_to_delete_category($category_id)
	{
		$condition = new EqualityCondition(LearningObject :: PROPERTY_PARENT_ID, $category_id);
		$count = RepositoryDataManager :: get_instance()->count_learning_objects(null, $condition);
		
		return ($count == 0);
	}
}
?>