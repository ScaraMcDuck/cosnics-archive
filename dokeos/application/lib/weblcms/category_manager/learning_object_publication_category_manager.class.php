<?php
/**
 * @package application.lib.profiler
 */
require_once Path :: get_application_library_path(). 'category_manager/category_manager.class.php';
require_once dirname(__FILE__) . '/../weblcms_data_manager.class.php';
require_once dirname(__FILE__) . '/learning_object_publication_category.class.php';

class LearningObjectPublicationCategoryManager extends CategoryManager
{
	
	function LearningObjectPublicationCategoryManager($parent)
	{
		parent :: __construct($parent);
	}

	function get_category()
	{
		$category = new LearningObjectPublicationCategory();
		$category->set_tool($this->get_parent()->get_tool_id());
		$category->set_course($this->get_parent()->get_course_id());
		return $category;
	}
	
	function allowed_to_delete_category($category_id)
	{
		$wdm = WeblcmsDataManager :: get_instance();
		$count = $wdm->count_learning_object_publications($this->get_parent()->get_course_id(), array($category_id), null, null, new EqualityCondition('tool', $this->get_parent()->get_tool_id()));
		return ($count == 0);
	}
	
	function count_categories($condition)
	{
		$wdm = WeblcmsDataManager :: get_instance();
		
		if($condition)
			$conditions[] = $condition;
		$conditions[] = new EqualityCondition(LearningObjectPublicationCategory :: PROPERTY_COURSE, $this->get_parent()->get_course_id());
		$conditions[] = new EqualityCondition(LearningObjectPublicationCategory :: PROPERTY_TOOL, $this->get_parent()->get_tool_id());
		$condition = new AndCondition($conditions);
		
		return $wdm->count_learning_object_publication_categories($condition);
	}
	
	function retrieve_categories($condition, $offset, $count, $order_property, $order_direction)
	{
		$wdm = WeblcmsDataManager :: get_instance();
		
		if($condition)
			$conditions[] = $condition;
		$conditions[] = new EqualityCondition(LearningObjectPublicationCategory :: PROPERTY_COURSE, $this->get_parent()->get_course_id());
		$conditions[] = new EqualityCondition(LearningObjectPublicationCategory :: PROPERTY_TOOL, $this->get_parent()->get_tool_id());
		$condition = new AndCondition($conditions);
		
		return $wdm->retrieve_learning_object_publication_categories($condition, $offset, $count, $order_property, $order_direction);
	}
}
?>