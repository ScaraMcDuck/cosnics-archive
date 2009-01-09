<?php
require_once dirname(__FILE__) . '/../../learning_object.class.php';
/**
 * @package repository.learningobject
 * @subpackage learning_path
 */
class LearningPathChapter extends LearningObject
{
	function is_ordered()
	{
		return true;
	}
	// Inherited
	function is_master_type()
	{
		return false;
	}
	
	function get_allowed_types()
	{
		return array('learning_path_chapter', 'announcement', 'calendar_event', 'description',
				     'document', 'link');
	}
	
}
?>