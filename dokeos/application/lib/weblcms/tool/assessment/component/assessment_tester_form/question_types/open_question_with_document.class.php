<?php

require_once dirname(__FILE__).'/../question_display.class.php';

class OpenQuestionWithDocumentDisplay extends QuestionDisplay
{
	function add_to($formvalidator) 
	{
		$formvalidator->addElement('html', parent :: display_header());
		$answers = $this->get_answers();
		$documents = $this->get_user_documents();
		$name = $this->get_clo_question()->get_ref().'_0';
		$formvalidator->addElement('select', $name, Translation :: get('Select a document:'), $documents);
		$formvalidator->addRule($name, Translation :: get('ThisFieldIsRequired'), 'required');
		$formvalidator->addElement('html', '<br/>');
		$formvalidator->addElement('html', $this->display_footer());
	}
	
	function get_user_documents()
	{
		$dm = RepositoryDataManager :: get_instance();
		$documents = $dm->retrieve_learning_objects('document');
		while ($document = $documents->next_result())
		{
			$user_documents[] = $document;
		}
		return $user_documents;
	}
}
?>