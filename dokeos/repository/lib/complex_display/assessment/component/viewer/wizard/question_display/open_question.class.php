<?php

require_once dirname(__FILE__).'/../question_display.class.php';

class OpenQuestionDisplay extends QuestionDisplay
{
	function add_question_form($formvalidator)
	{
		$clo_question = $this->get_clo_question();
		$question = RepositoryDataManager :: get_instance()->retrieve_learning_object($clo_question->get_ref());
		$type = $question->get_question_type();
		
		switch ($type)
		{
			case OpenQuestion :: TYPE_DOCUMENT:
				$name = $this->get_clo_question()->get_id().'_0';
				$formvalidator->addElement('hidden', $name, '');
				$formvalidator->addElement('text', $name.'_name', Translation :: get('SelectedDocument'));
				$buttons[] = $formvalidator->createElement('style_submit_button', 'repoviewer_'.$name, Translation :: get('Select'), array('class' => 'positive'));

				$formvalidator->addGroup($buttons, 'buttons', null, '&nbsp;', false);
				break;
			case OpenQuestion :: TYPE_OPEN:
				$name = $clo_question->get_id().'_0';
				$formvalidator->add_html_editor($name, '', false);
				break;
			case OpenQuestion :: TYPE_OPEN_WITH_DOCUMENT:
				$name = $clo_question->get_id().'_1';
				$formvalidator->add_html_editor($name, '', false);
				$name = $this->get_clo_question()->get_id().'_0';
				$formvalidator->addElement('hidden', $name, '');
				$formvalidator->addElement('text', $name.'_name', Translation :: get('SelectedDocument'));
				$buttons[] = $formvalidator->createElement('style_submit_button', 'repoviewer_'.$name, Translation :: get('Select'), array('class' => 'positive'));
				
				$formvalidator->addGroup($buttons, 'buttons', null, '&nbsp;', false);
				break;
		}
	}
}
?>