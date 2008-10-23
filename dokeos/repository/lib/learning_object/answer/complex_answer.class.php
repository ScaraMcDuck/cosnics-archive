<?php
/**
 * @package repository.learningobject
 * @subpackage answer
 */
require_once dirname(__FILE__) . '/../../complex_learning_object_item.class.php';

class ComplexAnswer extends ComplexLearningObjectItem
{
	const PROPERTY_SCORE = 'score';
	const PROPERTY_DISPLAY_ORDER = 'display_order';
	
	function get_score() 
	{
		return $this->get_additional_property(self :: PROPERTY_SCORE);
	}
	
	function set_score($score)
	{
		$this->set_additional_property(self :: PROPERTY_SCORE, $score);
	}
	
	function get_display_order()
	{
		return $this->get_additional_property(self :: PROPERTY_DISPLAY_ORDER);
	}
	
	function set_display_order($display_order)
	{
		$this->set_additional_property(self :: PROPERTY_DISPLAY_ORDER, $display_order);
	}
	
	static function get_additional_property_names()
	{
		return array(self :: PROPERTY_SCORE, self :: PROPERTY_DISPLAY_ORDER);
	}
	
	function get_allowed_types()
	{
		return array('answer');
	}
}
?>