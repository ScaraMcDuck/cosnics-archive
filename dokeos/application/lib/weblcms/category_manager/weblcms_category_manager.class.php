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
		$trail = new BreadcrumbTrail();
        $admin = new AdminManager();
        $trail->add(new Breadcrumb('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']."?application=weblcms", Translation :: get('MyCourses')));
        $trail->add(new Breadcrumb($parent->get_url(), Translation :: get('ManageCategories')));
		parent :: __construct($parent, $trail);
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
	
	function get_next_category_display_order($parent_id)
	{
		$wdm = WeblcmsDataManager :: get_instance();
		return $wdm->select_next_display_order($parent_id);
	}
}
?>