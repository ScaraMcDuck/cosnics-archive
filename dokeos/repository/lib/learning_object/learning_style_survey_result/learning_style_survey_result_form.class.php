<?php

require_once dirname(__FILE__) . '/../../learningobjectform.class.php';
require_once dirname(__FILE__) . '/learning_style_survey_result.class.php';

/**
 * @author Tim De Pauw
 */
class LearningStyleSurveyResultForm extends LearningObjectForm
{
	function build_creation_form()
	{
		parent :: build_creation_form();
	}
	
	function build_editing_form()
	{
		// Not allowed
	}
	
	// Inherited
	function create_learning_object()
	{
		$object = new LearningStyleSurveyResult();
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}
}

?>