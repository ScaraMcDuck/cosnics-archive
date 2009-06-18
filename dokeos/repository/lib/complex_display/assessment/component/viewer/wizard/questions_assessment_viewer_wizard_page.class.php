<?php

require_once dirname(__FILE__) . '/inc/question_display.class.php';

class QuestionsAssessmentViewerWizardPage extends AssessmentViewerWizardPage
{
	private $page_number;
	private $questions;

	function QuestionsAssessmentViewerWizardPage($name, $parent, $number)
	{
		parent :: AssessmentViewerWizardPage($name, $parent);
		$this->page_number = $number;
	}

	function get_number_of_questions()
	{
	    return $this->questions->size();
	}

	function buildForm()
	{
		$this->_formBuilt = true;
		$this->questions = $this->get_parent()->get_questions($this->page_number);
		
		// Add buttons
		if($this->page_number > 1)
			$buttons[] = $this->createElement('style_submit_button', $this->getButtonName('back'), Translation :: get('Back'), array('class' => 'previous'));
		
		if($this->page_number < $this->get_parent()->get_total_pages())
			$buttons[] = $this->createElement('style_submit_button', $this->getButtonName('next'), Translation :: get('Next'), array('class' => 'next'));
		else
			$buttons[] = $this->createElement('style_submit_button', $this->getButtonName('submit'), Translation :: get('Submit'), array('class' => 'positive'));
			
		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
		
		// Add question forms
		$i = (($this->page_number - 1) * $this->get_parent()->get_assessment()->get_questions_per_page()) + 1;

		while($question = $this->questions->next_result())
		{
			$question_display = QuestionDisplay :: factory($this, $question, $i);
			$question_display->display();
			$i++;
		}
		
		// Add buttons
		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
		$this->setDefaultAction('next');
	}
}
?>