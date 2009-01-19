<?php
/**
 * $Id: announcement_display.class.php 9191 2006-09-01 11:48:41Z bmol $
 * @package repository.learningobject
 * @subpackage exercise
 */
/**
 * This class can be used to display open questions
 */
class OpenQuestionDisplay extends LearningObjectDisplay
{
	function get_full_html()
	{
		return parent :: get_full_html();
	}
	function get_description()
	{
		$description = parent::get_description();
		$object = $this->get_learning_object();
		$type_id = $object->get_question_type();
		
		switch($type_id)
		{
			case 1: $type = Translation :: get('OpenQuestion'); break;
			case 2: $type = Translation :: get('OpenQuestionWithDocument'); break;
			case 3: $type = Translation :: get('DocumentQuestion'); break;
			default: $type = Translation :: get('OpenQuestion'); break;
		}
		
		return '<b>' . Translation :: get('Type') . ':</b> ' . $type . $description;
	}
}
?>