<?php
/**
 * $Id: announcement.class.php 9191 2006-09-01 11:48:41Z bmol $
 * @package repository.learningobject
 * @subpackage exercise
 */
require_once dirname(__FILE__) . '/../../learning_object.class.php';
/**
 * This class represents a hotspot question
 */
class HotspotQuestion extends LearningObject
{
	const PROPERTY_ANSWERS = 'answers';
	const PROPERTY_IMAGE = 'image';
	
	public function add_answer($answer)
	{
		$answers = $this->get_answers();
		$answers[] = $answer;
		return $this->set_additional_property(self :: PROPERTY_ANSWERS, serialize($answers));
	}
	
	public function set_answers($answers)
	{
		return $this->set_additional_property(self :: PROPERTY_ANSWERS, serialize($answers));
	}
	
	public function get_answers()
	{
		if($result = unserialize($this->get_additional_property(self :: PROPERTY_ANSWERS)))
		{
			return $result;
		}
		return array();
	}
	
	public function get_number_of_answers()
	{
		return count($this->get_answers());
	}
	
	public function get_image()
	{
		return $this->get_additional_property(self :: PROPERTY_IMAGE);
	}
	
	public function set_image($image)
	{
		$this->set_additional_property(self :: PROPERTY_IMAGE, $image);
	}
	
	static function get_additional_property_names()
	{
		return array (
			self :: PROPERTY_ANSWERS,
			self :: PROPERTY_IMAGE
		);
	}
}
?>