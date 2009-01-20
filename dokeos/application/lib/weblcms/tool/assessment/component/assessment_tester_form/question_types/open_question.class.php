<?php

require_once dirname(__FILE__).'/../question_display.class.php';

class OpenQuestionDisplay extends QuestionDisplay
{
	function add_to($formvalidator)
	{
		$formvalidator->addElement('html', parent :: display_header());
		$clo_question = $this->get_clo_question();
		$question = RepositoryDataManager :: get_instance()->retrieve_learning_object($clo_question->get_ref());
		$type = $question->get_question_type();
		
		switch ($type)
		{
			case OpenQuestion :: TYPE_DOCUMENT:
				$name = $this->get_clo_question()->get_ref().'_0';
				$formvalidator->addElement('hidden', $name, '');
				$formvalidator->addElement('text', $name.'_name', Translation :: get('Selected document'));
				$formvalidator->addElement('submit', 'repoviewer_'.$name, Translation :: get('RepoViewer'));
				break;
			case OpenQuestion :: TYPE_OPEN:
				$name = $clo_question->get_ref().'_0';
				$formvalidator->addElement('html_editor', $name, '');
				break;
			case OpenQuestion :: TYPE_OPEN_WITH_DOCUMENT:
				$name = $clo_question->get_ref().'_1';
				$formvalidator->addElement('html_editor', $name, '');
				$name = $this->get_clo_question()->get_ref().'_0';
				$formvalidator->addElement('hidden', $name, '');
				$formvalidator->addElement('text', $name.'_name', Translation :: get('Selected document'));
				$formvalidator->addElement('submit', 'repoviewer_'.$name, Translation :: get('RepoViewer'));
				break;
		}
		
		$formvalidator->addElement('html', '<br/>');
		$formvalidator->addElement('html', $this->display_footer());
	}
}
?>