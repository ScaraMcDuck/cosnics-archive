<?php
/**
 * @package application.lib.profiler
 */
require_once Path :: get_application_library_path(). 'category_manager/category_manager.class.php';
require_once dirname(__FILE__) . '/../weblcms_data_manager.class.php';
require_once dirname(__FILE__) . '/course_category.class.php';

class WeblcmsCategoryManager extends CategoryManager
{
	function WeblcmsCategoryManager($parent)
	{
		parent :: __construct($parent);
	}

	function get_category()
	{
		return new CourseCategory();
	}
	
	function get_category_form()
	{
		return new WeblcmsCategoryForm();
	}
	
	function count_categories($condition)
	{
		$wdm = WeblcmsDataManager :: get_instance();
		return $wdm->count_categories($condition);
	}
	
	function retrieve_categories($condition, $offset, $count, $order_property, $order_direction)
	{
		$wdm = WeblcmsDataManager :: get_instance();
		return $wdm->retrieve_categories($condition, $offset, $count, $order_property, $order_direction);
	}
}
?>