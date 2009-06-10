<?php
/**
 * $Id: announcement_form.class.php 9191 2006-09-01 11:48:41Z bmol $
 * @package repository.learningobject
 * @subpackage assessment
 */
require_once dirname(__FILE__).'/../../learning_object_form.class.php';
require_once dirname(__FILE__).'/assessment.class.php';
/**
 * This class represents a form to create or update assessment
 */
class AssessmentForm extends LearningObjectForm
{
	const UNLIMITED_ATTEMPTS = 'unlimited_attempts';
	const ALL_QUESTIONS = 'all_questions';
	const UNLIMITED_TIME = 'unlimited_time';

	function set_csv_values($valuearray)
	{
		$defaults[LearningObject :: PROPERTY_TITLE] = $valuearray[0];
		$defaults[LearningObject :: PROPERTY_PARENT_ID] = $valuearray[1];
		$defaults[LearningObject :: PROPERTY_DESCRIPTION] = $valuearray[2];
		$defaults[Assessment :: PROPERTY_ASSESSMENT_TYPE] = $valuearray[3];

		parent :: set_values($defaults);
	}

	function setDefaults($defaults = array ())
	{
		$object = $this->get_learning_object();
		if ($object != null)
		{
			$defaults[Assessment :: PROPERTY_ASSESSMENT_TYPE] = $object->get_assessment_type();
			$defaults[Assessment :: PROPERTY_MAXIMUM_ATTEMPTS] = $object->get_maximum_attempts();
			$defaults[self :: UNLIMITED_ATTEMPTS] = ($defaults[Assessment :: PROPERTY_MAXIMUM_ATTEMPTS] > 0 ? 1 : 0);
			$defaults[Assessment :: PROPERTY_QUESTIONS_PER_PAGE] = $object->get_questions_per_page();
			$defaults[self :: ALL_QUESTIONS] = ($defaults[Assessment :: PROPERTY_QUESTIONS_PER_PAGE] > 0 ? 1 : 0);
			$defaults[Assessment :: PROPERTY_MAXIMUM_TIME] = $object->get_maximum_time();
			$defaults[self :: UNLIMITED_TIME] = ($defaults[Assessment :: PROPERTY_MAXIMUM_TIME] > 0 ? 1 : 0);
		}
		else
		{
			$defaults[Assessment :: PROPERTY_ASSESSMENT_TYPE] = 0;
			$defaults[self :: UNLIMITED_ATTEMPTS] = 0;
			$defaults[self :: ALL_QUESTIONS] = 0;
			$defaults[self :: UNLIMITED_TIME] = 0;
		}

		parent :: setDefaults($defaults);
	}

	protected function build_creation_form()
    {
    	parent :: build_creation_form();
    	$this->addElement('category', Translation :: get(get_class($this) .'Properties'));

    	// Assessment types
        $this->add_select(Assessment :: PROPERTY_ASSESSMENT_TYPE, Translation :: get('AssessmentType'), Assessment :: get_types());

    	// Number of attempts
    	$choices = array();
		$choices[] = $this->createElement('radio', self :: UNLIMITED_ATTEMPTS, '', Translation :: get('Unlimited'), 0, array ('onclick' => 'javascript:window_hide(\''. self :: UNLIMITED_ATTEMPTS .'_window\')', 'id' => self :: UNLIMITED_ATTEMPTS));
		$choices[] = $this->createElement('radio', self :: UNLIMITED_ATTEMPTS, '', Translation :: get('Limited'), 1, array ('onclick' => 'javascript:window_show(\''. self :: UNLIMITED_ATTEMPTS .'_window\')'));
		$this->addGroup($choices, null, Translation :: get('MaximumAttempts'),'<br />',false);
		$this->addElement('html','<div style="margin-left: 25px; display: block;" id="'. self :: UNLIMITED_ATTEMPTS .'_window">');
		$this->add_textfield(Assessment :: PROPERTY_MAXIMUM_ATTEMPTS, null, false);
		$this->addElement('html','</div>');

    	// Number of questions per page
    	$choices = array();
		$choices[] = $this->createElement('radio', self :: ALL_QUESTIONS, '', Translation :: get('AllQuestionsOnOnePage'), 0, array ('onclick' => 'javascript:window_hide(\''. self :: ALL_QUESTIONS .'_window\')', 'id' => self :: ALL_QUESTIONS));
		$choices[] = $this->createElement('radio', self :: ALL_QUESTIONS, '', Translation :: get('LimitedQuestionsOnOnePage'), 1, array ('onclick' => 'javascript:window_show(\''. self :: ALL_QUESTIONS .'_window\')'));
		$this->addGroup($choices, null, Translation :: get('QuestionsPerPage'),'<br />',false);
		$this->addElement('html','<div style="margin-left: 25px; display: block;" id="'. self :: ALL_QUESTIONS .'_window">');
		$this->add_textfield(Assessment :: PROPERTY_QUESTIONS_PER_PAGE, null, false);
		$this->addElement('html','</div>');

    	// Maximum time allowed
    	$choices = array();
		$choices[] = $this->createElement('radio', self :: UNLIMITED_TIME, '', Translation :: get('Unlimited'), 0, array ('onclick' => 'javascript:window_hide(\''. self :: UNLIMITED_TIME .'_window\')', 'id' => self :: UNLIMITED_TIME));
		$choices[] = $this->createElement('radio', self :: UNLIMITED_TIME, '', Translation :: get('Limited'), 1, array ('onclick' => 'javascript:window_show(\''. self :: UNLIMITED_TIME .'_window\')'));
		$this->addGroup($choices, null, Translation :: get('MaximumTimeAllowedMinutes'),'<br />',false);
		$this->addElement('html','<div style="margin-left: 25px; display: block;" id="'. self :: UNLIMITED_TIME .'_window">');
		$this->add_textfield(Assessment :: PROPERTY_MAXIMUM_TIME, null, false);
		$this->addElement('html','</div>');

    	$this->addElement('category');

		$this->addElement('html',"<script type=\"text/javascript\">
					/* <![CDATA[ */
					var ". self :: UNLIMITED_ATTEMPTS ." = document.getElementById('". self :: UNLIMITED_ATTEMPTS ."');
					if (". self :: UNLIMITED_ATTEMPTS .".checked)
					{
						window_hide('". self :: UNLIMITED_ATTEMPTS ."_window');
					}

					var ". self :: ALL_QUESTIONS ." = document.getElementById('". self :: ALL_QUESTIONS ."');
					if (". self :: ALL_QUESTIONS .".checked)
					{
						window_hide('". self :: ALL_QUESTIONS ."_window');
					}

					var ". self :: UNLIMITED_TIME ." = document.getElementById('". self :: UNLIMITED_TIME ."');
					if (". self :: UNLIMITED_TIME .".checked)
					{
						window_hide('". self :: UNLIMITED_TIME ."_window');
					}

					function window_show(item) {
						el = document.getElementById(item);
						el.style.display='';
					}
					function window_hide(item) {
						el = document.getElementById(item);
						el.style.display='none';
					}
					/* ]]> */
					</script>\n");

    	$this->addRule(Assessment :: PROPERTY_MAXIMUM_ATTEMPTS, Translation :: get('ValueShouldBeNumeric'), 'numeric');
		$this->addRule(Assessment :: PROPERTY_QUESTIONS_PER_PAGE, Translation :: get('ValueShouldBeNumeric'), 'numeric');
		$this->addRule(Assessment :: PROPERTY_MAXIMUM_TIME, Translation :: get('ValueShouldBeNumeric'), 'numeric');
    }
    // Inherited
    protected function build_editing_form()
    {
		parent :: build_editing_form();
    	$this->addElement('category', Translation :: get(get_class($this) .'Properties'));

    	// Assessment types
        $this->add_select(Assessment :: PROPERTY_ASSESSMENT_TYPE, Translation :: get('AssessmentType'), Assessment :: get_types());

    	// Number of attempts
    	$choices = array();
		$choices[] = $this->createElement('radio', self :: UNLIMITED_ATTEMPTS, '', Translation :: get('Unlimited'), 0, array ('onclick' => 'javascript:window_hide(\''. self :: UNLIMITED_ATTEMPTS .'_window\')', 'id' => self :: UNLIMITED_ATTEMPTS));
		$choices[] = $this->createElement('radio', self :: UNLIMITED_ATTEMPTS, '', Translation :: get('Limited'), 1, array ('onclick' => 'javascript:window_show(\''. self :: UNLIMITED_ATTEMPTS .'_window\')'));
		$this->addGroup($choices, null, Translation :: get('MaximumAttempts'),'<br />',false);
		$this->addElement('html','<div style="margin-left: 25px; display: block;" id="'. self :: UNLIMITED_ATTEMPTS .'_window">');
		$this->add_textfield(Assessment :: PROPERTY_MAXIMUM_ATTEMPTS, null, false);
		$this->addElement('html','</div>');

    	// Number of questions per page
    	$choices = array();
		$choices[] = $this->createElement('radio', self :: ALL_QUESTIONS, '', Translation :: get('AllQuestionsOnOnePage'), 0, array ('onclick' => 'javascript:window_hide(\''. self :: ALL_QUESTIONS .'_window\')', 'id' => self :: ALL_QUESTIONS));
		$choices[] = $this->createElement('radio', self :: ALL_QUESTIONS, '', Translation :: get('LimitedQuestionsOnOnePage'), 1, array ('onclick' => 'javascript:window_show(\''. self :: ALL_QUESTIONS .'_window\')'));
		$this->addGroup($choices, null, Translation :: get('QuestionsPerPage'),'<br />',false);
		$this->addElement('html','<div style="margin-left: 25px; display: block;" id="'. self :: ALL_QUESTIONS .'_window">');
		$this->add_textfield(Assessment :: PROPERTY_QUESTIONS_PER_PAGE, null, false);
		$this->addElement('html','</div>');

    	// Maximum time allowed
    	$choices = array();
		$choices[] = $this->createElement('radio', self :: UNLIMITED_TIME, '', Translation :: get('Unlimited'), 0, array ('onclick' => 'javascript:window_hide(\''. self :: UNLIMITED_TIME .'_window\')', 'id' => self :: UNLIMITED_TIME));
		$choices[] = $this->createElement('radio', self :: UNLIMITED_TIME, '', Translation :: get('Limited'), 1, array ('onclick' => 'javascript:window_show(\''. self :: UNLIMITED_TIME .'_window\')'));
		$this->addGroup($choices, null, Translation :: get('MaximumTimeAllowedMinutes'),'<br />',false);
		$this->addElement('html','<div style="margin-left: 25px; display: block;" id="'. self :: UNLIMITED_TIME .'_window">');
		$this->add_textfield(Assessment :: PROPERTY_MAXIMUM_TIME, null, false);
		$this->addElement('html','</div>');

    	$this->addElement('category');

		$this->addElement('html',"<script type=\"text/javascript\">
					/* <![CDATA[ */
					var ". self :: UNLIMITED_ATTEMPTS ." = document.getElementById('". self :: UNLIMITED_ATTEMPTS ."');
					if (". self :: UNLIMITED_ATTEMPTS .".checked)
					{
						window_hide('". self :: UNLIMITED_ATTEMPTS ."_window');
					}

					var ". self :: ALL_QUESTIONS ." = document.getElementById('". self :: ALL_QUESTIONS ."');
					if (". self :: ALL_QUESTIONS .".checked)
					{
						window_hide('". self :: ALL_QUESTIONS ."_window');
					}

					var ". self :: UNLIMITED_TIME ." = document.getElementById('". self :: UNLIMITED_TIME ."');
					if (". self :: UNLIMITED_TIME .".checked)
					{
						window_hide('". self :: UNLIMITED_TIME ."_window');
					}

					function window_show(item) {
						el = document.getElementById(item);
						el.style.display='';
					}
					function window_hide(item) {
						el = document.getElementById(item);
						el.style.display='none';
					}
					/* ]]> */
					</script>\n");

    	$this->addRule(Assessment :: PROPERTY_MAXIMUM_ATTEMPTS, Translation :: get('ValueShouldBeNumeric'), 'numeric');
		$this->addRule(Assessment :: PROPERTY_QUESTIONS_PER_PAGE, Translation :: get('ValueShouldBeNumeric'), 'numeric');
		$this->addRule(Assessment :: PROPERTY_MAXIMUM_TIME, Translation :: get('ValueShouldBeNumeric'), 'numeric');
	}

	// Inherited
	function create_learning_object()
	{
		$object = new Assessment();
		$values = $this->exportValues();
		$object->set_maximum_attempts($values[Assessment :: PROPERTY_MAXIMUM_ATTEMPTS]);
		if ($object->get_maximum_attempts() == null)
			$object->set_maximum_attempts(0);

		$object->set_questions_per_page($values[Assessment :: PROPERTY_QUESTIONS_PER_PAGE]);
		if ($object->get_questions_per_page() == null)
			$object->set_questions_per_page(0);

		$object->set_maximum_time($values[Assessment :: PROPERTY_MAXIMUM_TIME]);
		if ($object->get_maximum_time() == null)
			$object->set_maximum_time(0);

		$ass_types = $object->get_types();
		$object->set_assessment_type($ass_types[$values[Assessment :: PROPERTY_ASSESSMENT_TYPE]]);
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}

	function update_learning_object()
	{
		$object = $this->get_learning_object();
		$values = $this->exportValues();
		$object->set_maximum_attempts($values[Assessment :: PROPERTY_MAXIMUM_ATTEMPTS]);
		if ($object->get_maximum_attempts() == null)
			$object->set_maximum_attempts(0);

		$object->set_questions_per_page($values[Assessment :: PROPERTY_QUESTIONS_PER_PAGE]);
		if ($object->get_questions_per_page() == null)
			$object->set_questions_per_page(0);

		$object->set_maximum_time($values[Assessment :: PROPERTY_MAXIMUM_TIME]);
		if ($object->get_maximum_time() == null)
			$object->set_maximum_time(0);

		$ass_types = $object->get_types();
		$object->set_assessment_type($ass_types[$values[Assessment :: PROPERTY_ASSESSMENT_TYPE]]);

		$this->set_learning_object($object);
		return parent :: update_learning_object();
	}
}
?>
