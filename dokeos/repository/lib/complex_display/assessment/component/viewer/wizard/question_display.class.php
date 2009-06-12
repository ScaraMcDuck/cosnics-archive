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
		$this->add_header();
		$this->add_question_form($this->formvalidator);
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
		$html[] = $this->question_nr . '.';
		$html[] = '</div>';
		$html[] = '<div class="text">';
		$html[] = '<img src="'. Theme :: get_common_image_path(). 'treemenu_types/' .$learning_object->get_icon_name().'.png">';
		$html[] = $learning_object->get_title();
		$html[] = '</div>';
		$html[] = '<div class="clear"></div>';
		$html[] = '</div>';
		$html[] = '<div class="answer">';
		
		if($this->add_border())
			$html[] = '<div style="border: 1px solid #B5CAE7;">';
		
		$description = $learning_object->get_description();
		if($description != '<p>&#160;</p>' && count($description) > 0 )
		{
			if($this->add_border())
			{
				$html[] = '<div class="description" style="border: none;">';
			}
			else
			{
				$html[] = '<div class="description">'; 
			}
				
			$html[] = $description;
			$html[] = '</div>';
		}
		
		if($this->add_border())
			$html[] = '<div style="padding: 10px;">';

		$html[] = '<div class="clear"></div>';

		$header = implode("\n", $html);
		$formvalidator->addElement('html', $header);
	}

	function add_footer($formvalidator)
	{
		$formvalidator = $this->formvalidator;
		
		if($this->add_border())
		{
			$html[] = '</div>';
			$html[] = '</div>';
		}
		
		$html[] = '</div>';
		$html[] = '</div>';

		$footer = implode("\n", $html);
		$formvalidator->addElement('html', $footer);
	}

	function add_border()
	{
		return true;
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