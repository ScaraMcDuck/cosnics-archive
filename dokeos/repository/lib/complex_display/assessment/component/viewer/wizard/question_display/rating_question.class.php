<?php

require_once dirname(__FILE__).'/../question_display.class.php';

class RatingQuestionDisplay extends QuestionDisplay
{
	function add_question_form($formvalidator)
	{
		$renderer = $formvalidator->defaultRenderer();

		$clo_question = $this->get_clo_question();
		$question = RepositoryDataManager :: get_instance()->retrieve_learning_object($clo_question->get_ref());
		$min = $question->get_low();
		$max = $question->get_high();
		$question_name = $this->get_clo_question()->get_id() . '_0';

		for ($i = $min; $i <= $max; $i++)
		{
			$scores[$i] = $i;
		}

		$element_template = array();
		$element_template[] = '<div><!-- BEGIN error --><span class="form_error">{error}</span><br /><!-- END error -->	{element}';
		$element_template[] = '<div class="clear">&nbsp;</div>';
		$element_template[] = '<div class="form_feedback"></div>';
		$element_template[] = '<div class="clear">&nbsp;</div>';
		$element_template[] = '</div>';
		$element_template = implode("\n", $element_template);

		$formvalidator->addElement('select', $question_name, Translation :: get('Rating') . ': ', $scores, 'class="rating_slider"');
		$renderer->setElementTemplate($element_template, $question_name);
		$formvalidator->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/rating_question.js'));
	}

	function add_borders()
	{
		return true;
	}

	function get_instruction()
	{
		return Translation :: get('SelectCorrectRating');
	}
}
?>