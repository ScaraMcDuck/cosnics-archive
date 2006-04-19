<?php
require_once dirname(__FILE__) . '/../../learningobject.class.php';
/**
 * @package repository.learningobject.learning_path
 */
class LearningPathChapter extends LearningObject
{
	const PROPERTY_DISPLAY_ORDER = 'display_order';
	function get_display_order_index ()
	{
		return $this->get_additional_property(self :: PROPERTY_DISPLAY_ORDER);
	}
	function set_display_order_index ($display_order)
	{
		return $this->set_additional_property(self :: PROPERTY_DISPLAY_ORDER, $display_order);
	}
}
?>