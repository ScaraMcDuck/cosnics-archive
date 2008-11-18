<?php

require_once Path :: get_application_library_path(). 'category_manager/platform_category.class.php';
require_once dirname(__FILE__) . '/../weblcms_data_manager.class.php';

/**
 * @package category
 */
/**
 *	@author Sven Vanpoucke
 */

class LearningObjectPublicationCategory extends PlatformCategory
{
	const PROPERTY_COURSE = 'course';
	const PROPERTY_TOOL = 'tool';
	
	function create()
	{
		$wdm = WeblcmsDataManager :: get_instance();
		$this->set_id($wdm->get_next_learning_object_publication_category_id());
		$this->set_display_order($wdm->select_next_learning_object_publication_category_display_order($this->get_parent()));
		return $wdm->create_learning_object_publication_category($this);
	}
	
	function update()
	{
		return WeblcmsDataManager :: get_instance()->update_learning_object_publication_category($this);
	}
	
	function delete()
	{
		return WeblcmsDataManager :: get_instance()->delete_learning_object_publication_category($this);
	}
	
	static function get_table_name()
	{
		return DokeosUtilities :: camelcase_to_underscores('LearningObjectPublicationCategory');
	}
	
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_COURSE, self :: PROPERTY_ID, self :: PROPERTY_NAME,
					  self :: PROPERTY_TOOL, self :: PROPERTY_PARENT, self :: PROPERTY_DISPLAY_ORDER);
	}
	
	function get_course()
	{
		return $this->get_default_property(self :: PROPERTY_COURSE);
	}
	
	function set_course($course)
	{
		$this->set_default_property(self :: PROPERTY_COURSE, $course);
	}	
	
	function get_tool()
	{
		return $this->get_default_property(self :: PROPERTY_TOOL);
	}
	
	function set_tool($tool)
	{
		$this->set_default_property(self :: PROPERTY_TOOL, $tool);
	}	
}