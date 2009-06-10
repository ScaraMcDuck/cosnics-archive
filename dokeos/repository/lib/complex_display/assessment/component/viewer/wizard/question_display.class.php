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
		
		$html[] = '<div class="question">';
		$html[] = '<div class="title" style="padding: 5px 5px 5px 35px; border:1px solid grey; background: #e6e6e6 url('. Theme :: get_common_image_path(). 'learning_object/' .$learning_object->get_icon_name().'.png) no-repeat; background-position: 5px 2px; height: 16px;">';
		$html[] = Translation :: get('Question') . ' ' . $this->question_nr . ': ' . $learning_object->get_title();
		$html[] = '</div>';
		$html[] = '<div class="description" style="padding-left: 35px;">';
		$description = $learning_object->get_description();

		if($description != '<p>&#160;</p>' && count($description) > 0 )
		{
			$html[] = '<div style="font-style: italic; ">' . $description . '</div>';
		}
		else
		{
			$html[] = '<br />';
		}
		
		$header = implode("\n", $html);
		$formvalidator->addElement('html', $header);
	}
	
	function add_footer($formvalidator)
	{
		$formvalidator = $this->formvalidator;
		$html[] = '<br/><br /></div>';
		$html[] = '</div>';
		
		$footer = implode("\n", $html);
		$formvalidator->addElement('html', $footer);
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