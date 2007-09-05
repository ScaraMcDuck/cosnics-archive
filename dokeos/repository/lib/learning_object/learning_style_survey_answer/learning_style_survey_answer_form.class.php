<?php

require_once dirname(__FILE__) . '/../../learningobjectform.class.php';
require_once dirname(__FILE__) . '/learning_style_survey_question.class.php';

/**
 * @author Tim De Pauw
 */
class LearningStyleSurveyAnswerForm extends LearningObjectForm
{
	function create_learning_object()
	{
		$object = new LearningStyleSurveyQuestion();
		$this->set_learning_object($object);
		// TODO
		return parent :: create_learning_object();
	}
}

?>