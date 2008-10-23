<?php

require_once dirname(__FILE__).'/../question_display.class.php';

class OpenQuestionWithDocumentDisplay extends QuestionDisplay
{
	function add_to($formvalidator) 
	{
		parent :: add_to($formvalidator);
		$answers = $this->get_answers();
		$documents = $this->get_user_documents();
		$formvalidator->addElement('select', $this->get_clo_question()->get_id(), Translation :: get('Select a document:'), $documents);
		$formvalidator->addElement('html', '<br/>');
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