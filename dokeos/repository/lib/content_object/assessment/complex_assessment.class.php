<?php
/**
 * @package repository.learningobject
 * @subpackage assessment
 */
require_once dirname(__FILE__) . '/../../complex_content_object_item.class.php';
/**
 * This class represents a complex assessment (used to create complex learning objects)
 */
class ComplexAssessment extends ComplexContentObjectItem
{
	function get_allowed_types()
	{
		return array('open_question', 'hotspot_question', 'fill_in_blanks_question', 'multiple_choice_question', 
					 'matching_question', 'select_question', 'matrix_question');
	}
}
?>