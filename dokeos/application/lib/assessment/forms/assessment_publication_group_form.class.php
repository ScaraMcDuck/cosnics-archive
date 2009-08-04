<?php
require_once Path :: get_library_path() . 'html/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__) . '/../assessment_publication_group.class.php';

/**
 * This class describes the form for a AssessmentPublicationGroup object.
 * @author Sven Vanpoucke
 * @author 
 **/
class AssessmentPublicationGroupForm extends FormValidator
{
	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;

	private $assessment_publication_group;
	private $user;

    function AssessmentPublicationGroupForm($form_type, $assessment_publication_group, $action, $user)
    {
    	parent :: __construct('assessment_publication_group_settings', 'post', $action);

    	$this->assessment_publication_group = $assessment_publication_group;
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
		$this->addElement('text', AssessmentPublicationGroup :: PROPERTY_ASSESSMENT_PUBLICATION, Translation :: get('AssessmentPublication'));
		$this->addRule(AssessmentPublicationGroup :: PROPERTY_ASSESSMENT_PUBLICATION, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', AssessmentPublicationGroup :: PROPERTY_GROUP_ID, Translation :: get('GroupId'));
		$this->addRule(AssessmentPublicationGroup :: PROPERTY_GROUP_ID, Translation :: get('ThisFieldIsRequired'), 'required');

    }

    function build_editing_form()
    {
    	$this->build_basic_form();

    	//$this->addElement('hidden', AssessmentPublicationGroup :: PROPERTY_ID);

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

    function update_assessment_publication_group()
    {
    	$assessment_publication_group = $this->assessment_publication_group;
    	$values = $this->exportValues();

    	$assessment_publication_group->set_assessment_publication($values[AssessmentPublicationGroup :: PROPERTY_ASSESSMENT_PUBLICATION]);
    	$assessment_publication_group->set_group_id($values[AssessmentPublicationGroup :: PROPERTY_GROUP_ID]);

    	return $assessment_publication_group->update();
    }

    function create_assessment_publication_group()
    {
    	$assessment_publication_group = $this->assessment_publication_group;
    	$values = $this->exportValues();

    	$assessment_publication_group->set_assessment_publication($values[AssessmentPublicationGroup :: PROPERTY_ASSESSMENT_PUBLICATION]);
    	$assessment_publication_group->set_group_id($values[AssessmentPublicationGroup :: PROPERTY_GROUP_ID]);

   		return $assessment_publication_group->create();
    }

	/**
	 * Sets default values.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$assessment_publication_group = $this->assessment_publication_group;

    	$defaults[AssessmentPublicationGroup :: PROPERTY_ASSESSMENT_PUBLICATION] = $assessment_publication_group->get_assessment_publication();
    	$defaults[AssessmentPublicationGroup :: PROPERTY_GROUP_ID] = $assessment_publication_group->get_group_id();

		parent :: setDefaults($defaults);
	}
}
?>