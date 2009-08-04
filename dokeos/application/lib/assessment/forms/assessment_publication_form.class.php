<?php
require_once Path :: get_library_path() . 'html/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__) . '/../assessment_publication.class.php';

/**
 * This class describes the form for a AssessmentPublication object.
 * @author Sven Vanpoucke
 * @author 
 **/
class AssessmentPublicationForm extends FormValidator
{
	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;

	private $assessment_publication;
	private $user;

    function AssessmentPublicationForm($form_type, $assessment_publication, $action, $user)
    {
    	parent :: __construct('assessment_publication_settings', 'post', $action);

    	$this->assessment_publication = $assessment_publication;
    	$this->user = $user;
		$this->form_type = $form_type;

		if ($this->form_type == self :: TYPE_EDIT)
		{
			$this->build_editing_form();
		}
		elseif ($this->form_type == self :: TYPE_CREATE)
		{
			$this->build_creation_form();
		}

		$this->setDefaults();
    }

    function build_basic_form()
    {
		$this->addElement('text', AssessmentPublication :: PROPERTY_ID, Translation :: get('Id'));
		$this->addRule(AssessmentPublication :: PROPERTY_ID, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', AssessmentPublication :: PROPERTY_LEARNING_OBJECT, Translation :: get('LearningObject'));
		$this->addRule(AssessmentPublication :: PROPERTY_LEARNING_OBJECT, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', AssessmentPublication :: PROPERTY_FROM_DATE, Translation :: get('FromDate'));
		$this->addRule(AssessmentPublication :: PROPERTY_FROM_DATE, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', AssessmentPublication :: PROPERTY_TO_DATE, Translation :: get('ToDate'));
		$this->addRule(AssessmentPublication :: PROPERTY_TO_DATE, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', AssessmentPublication :: PROPERTY_HIDDEN, Translation :: get('Hidden'));
		$this->addRule(AssessmentPublication :: PROPERTY_HIDDEN, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', AssessmentPublication :: PROPERTY_PUBLISHER, Translation :: get('Publisher'));
		$this->addRule(AssessmentPublication :: PROPERTY_PUBLISHER, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', AssessmentPublication :: PROPERTY_PUBLISHED, Translation :: get('Published'));
		$this->addRule(AssessmentPublication :: PROPERTY_PUBLISHED, Translation :: get('ThisFieldIsRequired'), 'required');

    }

    function build_editing_form()
    {
    	$this->build_basic_form();

    	//$this->addElement('hidden', AssessmentPublication :: PROPERTY_ID);

		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update'), array('class' => 'positive update'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function build_creation_form()
    {
    	$this->build_basic_form();

		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function update_assessment_publication()
    {
    	$assessment_publication = $this->assessment_publication;
    	$values = $this->exportValues();

    	$assessment_publication->set_id($values[AssessmentPublication :: PROPERTY_ID]);
    	$assessment_publication->set_learning_object($values[AssessmentPublication :: PROPERTY_LEARNING_OBJECT]);
    	$assessment_publication->set_from_date($values[AssessmentPublication :: PROPERTY_FROM_DATE]);
    	$assessment_publication->set_to_date($values[AssessmentPublication :: PROPERTY_TO_DATE]);
    	$assessment_publication->set_hidden($values[AssessmentPublication :: PROPERTY_HIDDEN]);
    	$assessment_publication->set_publisher($values[AssessmentPublication :: PROPERTY_PUBLISHER]);
    	$assessment_publication->set_published($values[AssessmentPublication :: PROPERTY_PUBLISHED]);

    	return $assessment_publication->update();
    }

    function create_assessment_publication()
    {
    	$assessment_publication = $this->assessment_publication;
    	$values = $this->exportValues();

    	$assessment_publication->set_id($values[AssessmentPublication :: PROPERTY_ID]);
    	$assessment_publication->set_learning_object($values[AssessmentPublication :: PROPERTY_LEARNING_OBJECT]);
    	$assessment_publication->set_from_date($values[AssessmentPublication :: PROPERTY_FROM_DATE]);
    	$assessment_publication->set_to_date($values[AssessmentPublication :: PROPERTY_TO_DATE]);
    	$assessment_publication->set_hidden($values[AssessmentPublication :: PROPERTY_HIDDEN]);
    	$assessment_publication->set_publisher($values[AssessmentPublication :: PROPERTY_PUBLISHER]);
    	$assessment_publication->set_published($values[AssessmentPublication :: PROPERTY_PUBLISHED]);

   		return $assessment_publication->create();
    }

	/**
	 * Sets default values.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$assessment_publication = $this->assessment_publication;

    	$defaults[AssessmentPublication :: PROPERTY_ID] = $assessment_publication->get_id();
    	$defaults[AssessmentPublication :: PROPERTY_LEARNING_OBJECT] = $assessment_publication->get_learning_object();
    	$defaults[AssessmentPublication :: PROPERTY_FROM_DATE] = $assessment_publication->get_from_date();
    	$defaults[AssessmentPublication :: PROPERTY_TO_DATE] = $assessment_publication->get_to_date();
    	$defaults[AssessmentPublication :: PROPERTY_HIDDEN] = $assessment_publication->get_hidden();
    	$defaults[AssessmentPublication :: PROPERTY_PUBLISHER] = $assessment_publication->get_publisher();
    	$defaults[AssessmentPublication :: PROPERTY_PUBLISHED] = $assessment_publication->get_published();

		parent :: setDefaults($defaults);
	}
}
?>