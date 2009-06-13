<?php

abstract class QuestionDisplay
{
	private $clo_question;
	private $question_nr;
	private $formvalidator;

	function QuestionDisplay($formvalidator, $clo_question, $question_nr)
	{
		$this->formvalidator = $formvalidator;

		$this->clo_question = $clo_question;
		$this->question_nr = $question_nr;
	}

	function get_clo_question()
	{
		return $this->clo_question;
	}

	function display()
	{
		$formvalidator = $this->formvalidator;
		$this->add_header();
		if ($this->add_borders())
		{
			$header = '<div class="with_borders">';
			$formvalidator->addElement('html', $header);
		}
		$this->add_question_form($formvalidator);
		if ($this->add_borders())
		{
			$footer = array();
			$footer[] = '<div class="clear"></div>';
			$footer[] = '</div>';
			$formvalidator->addElement('html', implode("\n", $footer));
		}
		$this->add_footer();
	}

	abstract function add_question_form($formvalidator);

	function add_header()
	{
		$formvalidator = $this->formvalidator;
		$clo_question = $this->get_clo_question();
		$learning_object = RepositoryDataManager :: get_instance()->retrieve_learning_object($clo_question->get_ref());

		$number_of_questions = $formvalidator->get_number_of_questions();
		$current_question = $this->question_nr;

		$html[] = '<div class="question">';
		$html[] = '<div class="title">';
		$html[] = '<div class="number">';
		$html[] = '<div class="bevel">';
		$html[] = $this->question_nr . '.';
		$html[] = '</div>';
		$html[] = '</div>';
		$html[] = '<div class="text">';
		$html[] = '<div class="bevel">';
		$html[] = '<img src="'. Theme :: get_common_image_path(). 'treemenu_types/' .$learning_object->get_icon_name().'.png">';
		$html[] = $learning_object->get_title();
		$html[] = '</div>';
		$html[] = '</div>';
		$html[] = '<div class="clear"></div>';
		$html[] = '</div>';
		$html[] = '<div class="answer">';

		$description = $learning_object->get_description();
		if($description != '<p>&#160;</p>' && count($description) > 0 )
		{
			$html[] = '<div class="description">';
			$html[] = $description;
			$html[] = '</div>';
		}

		$html[] = '<div class="clear"></div>';

		$header = implode("\n", $html);
		$formvalidator->addElement('html', $header);
	}

	function add_footer($formvalidator)
	{
		$formvalidator = $this->formvalidator;

		$html[] = '</div>';
		$html[] = '</div>';

		$footer = implode("\n", $html);
		$formvalidator->addElement('html', $footer);
	}

	function add_borders()
	{
		return false;
	}

	static function factory($formvalidator, $clo_question, $question_nr)
	{
		$question = RepositoryDataManager :: get_instance()->retrieve_learning_object($clo_question->get_ref());
		$type = $question->get_type();

		$file = dirname(__FILE__) . '/question_display/' . $type . '.class.php';

		if(!file_exists($file))
		{
			die('file does not exist: ' . $file);
		}

		require_once $file;

		$class = DokeosUtilities :: underscores_to_camelcase($type) . 'Display';
		$question_display = new $class($formvalidator, $clo_question, $question_nr);
		return $question_display;
	}
}
?>