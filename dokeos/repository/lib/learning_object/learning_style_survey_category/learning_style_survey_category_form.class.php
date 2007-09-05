<?php

require_once dirname(__FILE__) . '/../../learningobjectform.class.php';
require_once dirname(__FILE__) . '/learning_style_survey_category.class.php';

/**
 * @author Tim De Pauw
 */
class LearningStyleSurveyCategoryForm extends LearningObjectForm
{
	function create_learning_object()
	{
		$object = new LearningStyleSurveyCategory();
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}
}

?>