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
		$trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($parent->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_MANAGE_CATEGORIES)), Translation :: get('ManageCategories')));
		parent :: __construct($parent, $trail);
	}

	function get_category()
	{
		$category = new LearningObjectPublicationCategory();
		$category->set_tool($this->get_parent()->get_tool_id());
		$category->set_course($this->get_parent()->get_course_id());
		$category->set_allow_change(1);
		return $category;
	}
	
	function allowed_to_delete_category($category_id)
	{
		$wdm = WeblcmsDataManager :: get_instance();
		
		$category = $wdm->retrieve_learning_object_publication_categories(new EqualityCondition('id', $category_id))->next_result();
		if($category)
		{
			if($category->get_tool() == 'document' && !$category->get_allow_change()) 
				return false;
		}
		
		$count = $wdm->count_learning_object_publications($this->get_parent()->get_course_id(), array($category_id), null, null, new EqualityCondition('tool', $this->get_parent()->get_tool_id()));
		return ($count == 0);
	}
	
	function allowed_to_edit_category($category_id)
	{
		$wdm = WeblcmsDataManager :: get_instance();
		
		$category = $wdm->retrieve_learning_object_publication_categories(new EqualityCondition('id', $category_id))->next_result();
		if($category)
		{
			if($category->get_tool() == 'document' && !$category->get_allow_change()) 
				return false;
		}
		
		return true;
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
	
	function get_next_category_display_order($parent_id)
	{
		$wdm = WeblcmsDataManager :: get_instance();
		$category = $this->get_category();
		$category->set_parent($parent_id);
		
		return $wdm->select_next_learning_object_publication_category_display_order($category);
	}
}
?>