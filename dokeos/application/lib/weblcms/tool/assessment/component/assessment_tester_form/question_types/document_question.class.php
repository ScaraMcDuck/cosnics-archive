<?php

require_once dirname(__FILE__).'/../question_display.class.php';

class DocumentQuestionDisplay extends QuestionDisplay
{
	function add_to($formvalidator) 
	{
		$formvalidator->addElement('html', parent :: display_header());
		$name = $this->get_clo_question()->get_ref().'_0';
		
		$formvalidator->addElement('hidden', $name, '');
		$formvalidator->addElement('text', $name.'_name', Translation :: get('SelectedDocument'));
		
		$buttons[] = $formvalidator->createElement('style_submit_button', 'repoviewer_', Translation :: get('Save'), array('class' => 'positive'));
		$formvalidator->addGroup($buttons, 'buttons', null, '&nbsp;', false);
		
		//$formvalidator->addElement('html', '<br/>');
		$formvalidator->addElement('html', $this->display_footer());
	}
}
?>