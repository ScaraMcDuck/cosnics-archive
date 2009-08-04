<?php
require_once Path :: get_library_path() . 'html/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__) . '/../assessment_publication_user.class.php';

/**
 * This class describes the form for a AssessmentPublicationUser object.
 * @author Sven Vanpoucke
 * @author 
 **/
class AssessmentPublicationUserForm extends FormValidator
{
	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;

	private $assessment_publication_user;
	private $user;

    function AssessmentPublicationUserForm($form_type, $assessment_publication_user, $action, $user)
    {
    	parent :: __construct('assessment_publication_user_settings', 'post', $action);

    	$this->assessment_publication_user = $assessment_publication_user;
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
		$this->addElement('text', AssessmentPublicationUser :: PROPERTY_ASSESSMENT_PUBLICATION, Translation :: get('AssessmentPublication'));
		$this->addRule(AssessmentPublicationUser :: PROPERTY_ASSESSMENT_PUBLICATION, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', AssessmentPublicationUser :: PROPERTY_USER, Translation :: get('User'));
		$this->addRule(AssessmentPublicationUser :: PROPERTY_USER, Translation :: get('ThisFieldIsRequired'), 'required');

    }

    function build_editing_form()
    {
    	$this->build_basic_form();

    	//$this->addElement('hidden', AssessmentPublicationUser :: PROPERTY_ID);

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

    function update_assessment_publication_user()
    {
    	$assessment_publication_user = $this->assessment_publication_user;
    	$values = $this->exportValues();

    	$assessment_publication_user->set_assessment_publication($values[AssessmentPublicationUser :: PROPERTY_ASSESSMENT_PUBLICATION]);
    	$assessment_publication_user->set_user($values[AssessmentPublicationUser :: PROPERTY_USER]);

    	return $assessment_publication_user->update();
    }

    function create_assessment_publication_user()
    {
    	$assessment_publication_user = $this->assessment_publication_user;
    	$values = $this->exportValues();

    	$assessment_publication_user->set_assessment_publication($values[AssessmentPublicationUser :: PROPERTY_ASSESSMENT_PUBLICATION]);
    	$assessment_publication_user->set_user($values[AssessmentPublicationUser :: PROPERTY_USER]);

   		return $assessment_publication_user->create();
    }

	/**
	 * Sets default values.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$assessment_publication_user = $this->assessment_publication_user;

    	$defaults[AssessmentPublicationUser :: PROPERTY_ASSESSMENT_PUBLICATION] = $assessment_publication_user->get_assessment_publication();
    	$defaults[AssessmentPublicationUser :: PROPERTY_USER] = $assessment_publication_user->get_user();

		parent :: setDefaults($defaults);
	}
}
?>