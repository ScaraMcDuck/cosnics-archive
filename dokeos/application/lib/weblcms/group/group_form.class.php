<?php
/**
 * $Id:$
 * @package application.lib.weblcms.group
 * @author Bart Mollet
 */
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
require_once Path :: get_user_path(). 'lib/user_data_manager.class.php';
require_once Path :: get_user_path(). 'lib/user.class.php';
require_once dirname(__FILE__).'/group.class.php';

class GroupForm extends FormValidator {

	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;
	const RESULT_SUCCESS = 'ObjectUpdated';
	const RESULT_ERROR = 'ObjectUpdateFailed';

	private $parent;
	private $group;
	private $form_type;

    function GroupForm($form_type, $group, $action) {
    	parent :: __construct('course_settings', 'post', $action);
		$this->form_type = $form_type;
		$this->group = $group;
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
		$this->addElement('text', Group :: PROPERTY_NAME, Translation :: get('Title'));
		$this->addRule(Group :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');
		$this->add_html_editor(Group :: PROPERTY_DESCRIPTION, Translation :: get('Description'));
		$this->addElement('text', Group::PROPERTY_MAX_NUMBER_OF_MEMBERS,Translation :: get('MaxNumberOfMembers'),'size="4"');
		$this->addRule(Group::PROPERTY_MAX_NUMBER_OF_MEMBERS, Translation :: get('ThisFieldShouldBeNumeric'), 'regex','/^[0-9]*$/');
		$this->addElement('checkbox',Group::PROPERTY_SELF_REG,Translation :: get('Registration'),Translation :: get('SelfRegAllowed'));
		$this->addElement('checkbox',Group::PROPERTY_SELF_UNREG,null,Translation :: get('SelfUnRegAllowed'));
		$this->addElement('submit', 'group_settings', Translation :: get('Ok'));
    }

    function build_editing_form()
    {
    	$parent = $this->parent;

    	$this->build_basic_form();

    	$this->addElement('hidden', Group :: PROPERTY_ID);
    }

    function build_creation_form()
    {
    	$this->build_basic_form();
    }

    function update_group()
    {
    	$group = $this->group;
    	$values = $this->exportValues();
    	$group->set_name($values[Group :: PROPERTY_NAME]);
    	$group->set_description($values[Group :: PROPERTY_DESCRIPTION]);
    	$group->set_max_number_of_members($values[Group :: PROPERTY_MAX_NUMBER_OF_MEMBERS]);
   		$group->set_self_registration_allowed($values[Group :: PROPERTY_SELF_REG]);
		$group->set_self_unregistration_allowed($values[Group :: PROPERTY_SELF_UNREG]);
    	return $group->update();
    }

    function create_group()
    {
    	$group = $this->group;
    	$values = $this->exportValues();

    	$group->set_name($values[Group :: PROPERTY_NAME]);
    	$group->set_description($values[Group :: PROPERTY_DESCRIPTION]);
		$group->set_max_number_of_members($values[Group :: PROPERTY_MAX_NUMBER_OF_MEMBERS]);
		$group->set_self_registration_allowed($values[Group :: PROPERTY_SELF_REG]);
		$group->set_self_unregistration_allowed($values[Group :: PROPERTY_SELF_UNREG]);
    	if ($group->create())
    	{
   			return true;
    	}
    	else
    	{
    		return false;
    	}
    }

	/**
	 * Sets default values. Traditionally, you will want to extend this method
	 * so it sets default for your learning object type's additional
	 * properties.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$group = $this->group;
		$defaults[Group :: PROPERTY_NAME] = $group->get_name();
		$$defaults[Group :: PROPERTY_DESCRIPTION] = $group->get_description();
		$defaults[Group :: PROPERTY_MAX_NUMBER_OF_MEMBERS] = $group->get_max_number_of_members();
		$defaults[Group :: PROPERTY_SELF_REG]= $group->is_self_registration_allowed();;
		$defaults[Group :: PROPERTY_SELF_UNREG]= $group->is_self_unregistration_allowed();;
		parent :: setDefaults($defaults);
	}
}
?>