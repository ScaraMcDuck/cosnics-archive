<?php

require_once dirname(__FILE__).'/../question_display.class.php';

class OpenQuestionWithDocumentDisplay extends QuestionDisplay
{
	function add_to($formvalidator) 
	{
		parent :: add_to($formvalidator);
		$answers = $this->get_answers();
		
		//$formvalidator->addElement('html','<div class="learning_object">');
		//foreach($answers as $answer)
		//{
			//$formvalidator->addElement('html_editor', $this->get_clo_question()->get_ref(), '');
		//}
		//$formvalidator->addElement('html','</div>');
		$documents = $this->get_user_documents();
		$formvalidator->addElement('select', $this->get_clo_question()->get_id(), Translation :: get('Select a document:'), $documents);
		$formvalidator->addElement('html', '<br/>');
	}
	
	function get_user_documents()
	{
		$dm = RepositoryDataManager :: get_instance();
		//$condition = new EqualityCondition(LearningObject :: PROPERTY_TYPE, 'document');
		$documents = $dm->retrieve_learning_objects('document');
		while ($document = $documents->next_result())
		{
			$user_documents[] = $document;
		}
		return $user_documents;
	}
}
?>