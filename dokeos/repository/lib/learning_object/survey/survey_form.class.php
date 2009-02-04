<?php
/**
 * $Id: announcement_form.class.php 9191 2006-09-01 11:48:41Z bmol $
 * @package repository.learningobject
 * @subpackage assessment
 */
require_once dirname(__FILE__).'/../../learning_object_form.class.php';
require_once dirname(__FILE__).'/survey.class.php';
/**
 * This class represents a form to create or update assessment
 */
class SurveyForm extends LearningObjectForm
{
	function set_csv_values($valuearray)
	{
		$defaults[LearningObject :: PROPERTY_TITLE] = $valuearray[0];
		$defaults[LearningObject :: PROPERTY_PARENT_ID] = $valuearray[1];
		$defaults[LearningObject :: PROPERTY_DESCRIPTION] = $valuearray[2];	

		parent :: set_values($defaults);			
	}
	
	function setDefaults($defaults = array ())
	{
		$object = $this->get_learning_object();
		if ($object != null) 
		{
			$defaults[Survey :: PROPERTY_MAXIMUM_ATTEMPTS] = $object->get_maximum_attempts();
			$defaults[Survey :: PROPERTY_ANONYMOUS] = $object->get_anonymous();
			$defaults[Survey :: PROPERTY_QUESTIONS_PER_PAGE] = $object->get_questions_per_page();
		}
			
		parent :: setDefaults($defaults);
	}
	
	protected function build_creation_form()
    {
    	parent :: build_creation_form();
    	$this->addElement('category', Translation :: get(get_class($this) .'Properties'));
    	$this->add_textfield(Survey :: PROPERTY_MAXIMUM_ATTEMPTS, Translation :: get('MaximumAttempts')); //.' (0 = '.Translation :: get('infinite').')';
    	$this->add_textfield(Survey :: PROPERTY_QUESTIONS_PER_PAGE, Translation :: get('QuestionsPerPage'), false);
    	$this->add_html_editor(Survey :: PROPERTY_INTRODUCTION_TEXT, Translation :: get('IntroText'), false);
    	$this->add_html_editor(Survey :: PROPERTY_FINISH_TEXT, Translation :: get('FinishingText'), false);
    	$this->addElement('checkbox', Survey :: PROPERTY_ANONYMOUS, Translation :: get('Anonymous'));
    	$this->addElement('category');
    }
    // Inherited
    protected function build_editing_form()
    {
		parent :: build_editing_form();
		$this->addElement('category', Translation :: get(get_class($this) .'Properties'));
    	$this->add_textfield(Survey :: PROPERTY_MAXIMUM_ATTEMPTS, Translation :: get('MaximumAttempts')); //.' (0 = '.Translation :: get('infinite').')';
    	$this->add_textfield(Survey :: PROPERTY_QUESTIONS_PER_PAGE, Translation :: get('QuestionsPerPage'));
    	$this->add_html_editor(Survey :: PROPERTY_INTRODUCTION_TEXT, Translation :: get('IntroText'), false);
    	$this->add_html_editor(Survey :: PROPERTY_FINISH_TEXT, Translation :: get('FinishingText'), false);
    	$this->addElement('checkbox', Survey :: PROPERTY_ANONYMOUS, Translation :: get('Anonymous'));
    	$this->addElement('category');
	}

	// Inherited
	function create_learning_object()
	{
		$object = new Survey();
		$values = $this->exportValues();
		
		$object->set_maximum_attempts($values[Survey :: PROPERTY_MAXIMUM_ATTEMPTS]);
		if ($object->get_maximum_attempts() == null)
			$object->set_maximum_attempts(0);

		$object->set_questions_per_page($values[Survey :: PROPERTY_QUESTIONS_PER_PAGE]);
		if ($object->get_questions_per_page() == null)
			$object->set_questions_per_page(0);
			
		$object->set_finish_text($values[Survey :: PROPERTY_FINISH_TEXT]);
		$object->set_introduction_text($values[Survey :: PROPERTY_INTRODUCTION_TEXT]);
		
		if (isset($values[Survey :: PROPERTY_ANONYMOUS]))
			$object->set_anonymous($values[Survey :: PROPERTY_ANONYMOUS]);
		else
			$object->set_anonymous(0);
			
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}
	
	function update_learning_object()
	{
		$object = $this->get_learning_object();
		$values = $this->exportValues();
		
		$object->set_maximum_attempts($values[Survey :: PROPERTY_MAXIMUM_ATTEMPTS]);
		if ($object->get_maximum_attempts() == null)
			$object->set_maximum_attempts(0);

		$object->set_questions_per_page($values[Survey :: PROPERTY_QUESTIONS_PER_PAGE]);
		if ($object->get_questions_per_page() == null)
			$object->set_questions_per_page(0);
			
		$object->set_finish_text($values[Survey :: PROPERTY_FINISH_TEXT]);
		$object->set_introduction_text($values[Survey :: PROPERTY_INTRODUCTION_TEXT]);

		$this->set_learning_object($object);
		
		if (isset($values[Survey :: PROPERTY_ANONYMOUS]))
			$object->set_anonymous($values[Survey :: PROPERTY_ANONYMOUS]);
		else
			$object->set_anonymous(0);
			
		$this->set_learning_object($object);
		return parent :: update_learning_object();
	}
}
?>
