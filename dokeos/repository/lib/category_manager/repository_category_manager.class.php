<?php
/**
 * @package application.lib.profiler
 */
require_once Path :: get_application_library_path(). 'category_manager/category_manager.class.php';
require_once dirname(__FILE__) . '/../repository_data_manager.class.php';
require_once dirname(__FILE__) . '/repository_category.class.php';

class RepositoryCategoryManager extends CategoryManager
{
	function RepositoryCategoryManager($parent)
	{
		parent :: __construct($parent);
	}

	function get_category()
	{
		return new RepositoryCategory();
	}
	
	function count_categories($condition)
	{
		$wdm = RepositoryDataManager :: get_instance();
		return $wdm->count_categories($condition);
	}
	
	function retrieve_categories($condition, $offset, $count, $order_property, $order_direction)
	{
		$wdm = RepositoryDataManager :: get_instance();
		return $wdm->retrieve_categories($condition, $offset, $count, $order_property, $order_direction);
	}
}
?>