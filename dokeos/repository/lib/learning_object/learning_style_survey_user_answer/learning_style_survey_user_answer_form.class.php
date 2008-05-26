<?php

require_once dirname(__FILE__) . '/../../learning_object_form.class.php';
require_once dirname(__FILE__) . '/learning_style_survey_user_answer.class.php';

/**
 * @author Tim De Pauw
 */
class LearningStyleSurveyUserAnswerForm extends LearningObjectForm
{
	// Inherited
	function create_learning_object()
	{
		$object = new LearningStyleSurveyUserAnswer();
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}
}

?>