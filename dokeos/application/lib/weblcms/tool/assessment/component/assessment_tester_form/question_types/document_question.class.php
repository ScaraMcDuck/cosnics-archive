<?php

require_once dirname(__FILE__).'/../question_display.class.php';

class DocumentQuestionDisplay extends QuestionDisplay
{
	function add_to($formvalidator) 
	{
		$formvalidator->addElement('html', parent :: display_header());
		$answers = $this->get_answers();
		$documents = $this->get_user_documents();
		$name = $this->get_clo_question()->get_ref().'_0';
		$elements[] = $formvalidator->createElement('html', 'Select a document or upload a file:<br/>');
		$elements[] = $formvalidator->createElement('select', null, Translation :: get('Select a document:'), $documents);
		$elements[] = $formvalidator->createElement('html', '<div style="display:block;" id="editor_html_content">');
		$elements[] = $formvalidator->createElement('file', null, Translation :: get('Upload a file'));
		$elements[] = $formvalidator->createElement('html', '</div>');
		$formvalidator->addGroup($elements, $name, '<br/>');
		//$formvalidator->addElement('upload_or_select', $name, Translation :: get('Upload or select a document'));
		$formvalidator->addElement('html', '<br/>');
		//$formvalidator->addRule($name, Translation :: get('ThisFieldIsRequired'), 'required');
		$formvalidator->addElement('html', $this->display_footer());
	}
	
	function get_user_documents()
	{
		$dm = RepositoryDataManager :: get_instance();
		$documents = $dm->retrieve_learning_objects('document');
		while ($document = $documents->next_result())
		{
			$user_documents[$document->get_id()] = $document;
		}
		return $user_documents;
	}
}
?>